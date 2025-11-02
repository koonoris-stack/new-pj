<?php
 
namespace App\Http\Controllers;
 
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate; // เพิ่ม import
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Database\QueryException;
 
   class CategoryController extends SearchableController
{
    const int MAX_ITEMS = 5;
 
    #[\Override]
    function getQuery(): Builder
    {
        return Category::orderBy('code');
    }
 
    #[\Override]
    function prepareCriteria(array $criteria): array
    {
        return [
            ...parent::prepareCriteria($criteria),
            'term' => $criteria['term'] ?? null,
        ];
    }
 
    #[\Override]
    function filter(Builder|Relation $query, array $criteria): Builder|Relation
    {
        $query = parent::filter($query, $criteria);
 
        if (!empty($criteria['term'])) {
            $term = strtolower($criteria['term']);
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(code) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"]);
            });
        }
 
        return $query;
    }
 
    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Category::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('products');
        // สมมติว่า Category มี relation products()
 
        return view('categories.list', [
            'criteria' => $criteria,
            'categories' => $query->paginate(self::MAX_ITEMS),
        ]);
    }
 
    function view(string $categoryCode): View
    {
        $category = $this->find($categoryCode);
        Gate::authorize('view', $category);

        return view('categories.view', [
            'category' => $category,
        ]);
    }
 
    function showCreateForm(): View
    {
        Gate::authorize('create', Category::class);

        return view('categories.create-form');
    }
 
    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        $data = $request->getParsedBody();

        try {
            $category = Category::create($data);

            return redirect(
                session()->get('bookmarks.categories.create-form', route('categories.list'))
            )
                ->with('status', "Category {$category->code} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }
 
    function showUpdateForm(string $categoryCode): View
    {
        $category = $this->find($categoryCode);
        Gate::authorize('update', $category);

        return view('categories.update-form', [
            'category' => $category,
        ]);
    }
 
    function update(ServerRequestInterface $request, string $categoryCode): RedirectResponse
    {
        $category = $this->find($categoryCode);
        Gate::authorize('update', $category);
        $data = $request->getParsedBody();

        try {
            $category->fill($data);
            $category->save();

            return redirect()
                ->route('categories.view', [
                    'category' => $category->code,
                ])
                ->with('status', "Category {$category->code} was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }
 
    function delete(string $categoryCode): RedirectResponse
    {
        $category = $this->find($categoryCode);
        Gate::authorize('delete', $category);
        try {
            $category->delete();

            return redirect(
                session()->get('bookmarks.categories.view', route('categories.list'))
            )
                ->with('status', "Category {$category->code} was deleted.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }
 
        function viewProducts(
            ServerRequestInterface $request,
            ProductController $productController,
            string $categoryCode
        ): View {
            // 1. หา category
            $category = $this->find($categoryCode); // CategoryController->find
                Gate::authorize('view', $category);
 
            // 2. เตรียม criteria จาก query string
            $criteria = $productController->prepareCriteria($request->getQueryParams());
 
            // 3. Query products ของ category นี้
            $query = $productController
                ->filter($category->products(), $criteria) // ใช้ hasMany relation
                 ->with('category')
                ->withCount('shops'); // ถ้า Product มี relation shops
 
            // 4. ส่งไป view
            return view('categories.view-products', [
                'category' => $category,
                'criteria' => $criteria,
                'products' => $query->paginate($productController::MAX_ITEMS),
            ]);
        }
 
 
            function showAddProductsForm(
        ServerRequestInterface $request,
        ProductController $productController,
        string $categoryCode
    ): View {
        $category = $this->find($categoryCode);
                Gate::authorize('update', $category);
        $criteria = $productController->prepareCriteria($request->getQueryParams());
 
        // ดึง products ที่ยังไม่มี category หรือมี category อื่น
        $query = $productController
            ->getQuery()
            ->where(function (Builder $q) use ($category) {
                $q->whereNull('category_id')
                  ->orWhere('category_id', '!=', $category->id);
            });
 
        $query = $productController
            ->filter($query, $criteria)
             ->with('category');
 
        return view('categories.add-products-form', [
            'criteria' => $criteria,
            'category' => $category,
            'products' => $query->paginate($productController::MAX_ITEMS),
        ]);
    }
 
        function addProduct(
            ServerRequestInterface $request,
            ProductController $productController,
            string $categoryCode
        ): RedirectResponse {
            $category = $this->find($categoryCode);
                Gate::authorize('update', $category);
            $data = $request->getParsedBody();

            try {
                $product = $productController
                    ->getQuery()
                    ->where(function (Builder $q) use ($category) {
                        $q->whereNull('category_id')
                        ->orWhere('category_id', '!=', $category->id);
                    })
                    ->where('code', $data['product']) // 'product' คือ name ของ input
                    ->firstOrFail();

                // Assign category ให้ product
                $product->category()->associate($category);
                $product->save();

                return redirect()
                    ->back()
                    ->with('status', "Product {$product->code} was added to Category {$category->code}.");
            } catch (QueryException $excp) {
                // We don't want withInput() here.
                return redirect()->back()->withErrors([
                    'alert' => $excp->errorInfo[2],
                ]);
            }
        }



        
}