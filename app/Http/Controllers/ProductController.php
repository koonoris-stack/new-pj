<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class ProductController extends SearchableController
{
    const int MAX_ITEMS = 5;

    #[\Override]
    function getQuery(): Builder
    {
        return Product::orderBy('code');
    }

    #[\Override]
    function prepareCriteria(array $criteria): array
    {
        // คืนค่า criteria ที่ parent เตรียมไว้โดยตรง (เอา minPrice/maxPrice ออก)
        return parent::prepareCriteria($criteria);
    }

    #[\Override]
    function applyWhereToFilterByTerm(Builder $query, string $word): void
    {
        parent::applyWhereToFilterByTerm($query, $word);

        $query->orWhereHas(
            'category',
            function (Builder $innerQuery) use ($word): void {
                $innerQuery->where('name', 'LIKE', "%{$word}%");
            },
        );
    }

    #[\Override]
    function filter(Builder|Relation $query, array $criteria): Builder|Relation
    {
        // ใช้ filter ของ parent เท่านั้น (ไม่กรอง min/max price)
        return parent::filter($query, $criteria);
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Product::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)
            ->with('category')
            ->withCount('shops');

        return view('products.list', [
            'criteria' => $criteria,
            'products' => $query->paginate(self::MAX_ITEMS),
        ]);
    }

    function view(string $productCode): View
    {
        $product = $this->find($productCode);
        Gate::authorize('view', $product);

        return view('products.view', [
            'product' => $product,
        ]);
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', Product::class);

        $categories = Category::orderBy('code')->get();

        return view('products.create-form', [
            'categories' => $categories,
        ]);
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        $data = $request->getParsedBody();

        // map/normalize category/category_id (รองรับทั้ง key เก่า/ใหม่)
        $categoryId = null;
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $cat = Category::where('code', $data['category'])->first();
            $categoryId = $cat->id ?? null;
        }
        $data['category_id'] = $categoryId;

        // Validation rules
        $validator = Validator::make($data, [
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            // image_file handled via request()->file — validation for files can be added if desired
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $product = Product::create($data);

            // handle uploaded file
            if (request()->hasFile('image_file')) {
                $file = request()->file('image_file');
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = $product->code . '.' . $ext;
                $file->move(public_path('images'), $filename);

                if (Schema::hasColumn('products', 'image')) {
                    $product->image = asset("images/{$filename}");
                    $product->save();
                }
            } elseif (!empty($data['image']) && Schema::hasColumn('products', 'image')) {
                $product->image = $data['image'];
                $product->save();
            }

            return redirect(
                session()->get('bookmarks.products.create-form', route('products.list'))
            )
                ->with('status', "Product {$product->code} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function showUpdateForm(string $productCode): View
    {
        $product = $this->find($productCode);
        Gate::authorize('update', $product);

        $categories = Category::orderBy('code')->get();

        return view('products.update-form', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    function update(
        ServerRequestInterface $request,
        string $productCode,
    ): RedirectResponse {
        $product = $this->find($productCode);
        Gate::authorize('update', $product);

        $data = $request->getParsedBody();

        // map category_id if provided (รองรับ legacy)
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $cat = Category::where('code', $data['category'])->first();
            $categoryId = $cat->id ?? null;
        } else {
            $categoryId = null;
        }

        $data['category_id'] = $categoryId;

        // Validation rules for update: unique code except current product id
        $validator = Validator::make($data, [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'code')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            // keep original code for filename if user changes code
            $originalCode = $product->code;

            $product->fill($data);

            // handle uploaded file
            if (request()->hasFile('image_file')) {
                $file = request()->file('image_file');
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = ($product->code ?? $originalCode) . '.' . $ext;
                $file->move(public_path('images'), $filename);
                if (Schema::hasColumn('products', 'image')) {
                    $product->image = asset("images/{$filename}");
                }
            } elseif (isset($data['image']) && !empty($data['image']) && Schema::hasColumn('products', 'image')) {
                $product->image = $data['image'];
            }

            $product->save();

            return redirect()
                ->route('products.view', [
                    'product' => $product->code,
                ])
                ->with('status', "Product {$product->code} was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
                
            ]);
        }
    }

    function delete(string $productCode): RedirectResponse
    {
        $product = $this->find($productCode);
        Gate::authorize('delete', $product);

        try {
            $product->delete();

            return redirect(
                session()->get('bookmarks.products.view', route('products.list'))
            )
                ->with('status', "Product {$product->code} was deleted.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function viewShops(
        ServerRequestInterface $request,
        ShopController $shopController,
        string $productCode
    ): View {
        $product = $this->find($productCode);
        Gate::authorize('view', $product);

        $criteria = $shopController->prepareCriteria($request->getQueryParams());
        $query = $shopController
            ->filter($product->shops(), $criteria)
            ->withCount('products');

        return view('products.view-shops', [
            'product' => $product,
            'criteria' => $criteria,
            'shops' => $query->paginate($shopController::MAX_ITEMS),
        ]);
    }

    function showAddShopsForm(
        ServerRequestInterface $request,
        ShopController $shopController,
        string $productCode
    ): View {
        $product = $this->find($productCode);
        Gate::authorize('update', $product);

        $criteria = $shopController->prepareCriteria($request->getQueryParams());
        $query = $shopController
            ->getQuery()
            ->whereDoesntHave(
                'products',
                function (Builder $innerQuery) use ($product): void {
                    $innerQuery->where('code', $product->code);
                },
            );
        $query = $shopController
            ->filter($query, $criteria)
            ->withCount('products');

        return view('products.add-shops-form', [
            'criteria' => $criteria,
            'product' => $product,
            'shops' => $query->paginate($shopController::MAX_ITEMS),
        ]);
    }

    function addShop(
        ServerRequestInterface $request,
        ShopController $shopController,
        string $productCode,
    ): RedirectResponse {
        $product = $this->find($productCode);
        Gate::authorize('update', $product);

        $data = $request->getParsedBody();

        try {
            $shop = $shopController
                ->getQuery()
                ->whereDoesntHave(
                    'products',
                    function (Builder $innerQuery) use ($product): void {
                        $innerQuery->where('code', $product->code);
                    },
                )
                ->where('code', $data['shop'])
                ->firstOrFail();

            $product->shops()->attach($shop);

            return redirect()
                ->back()
                ->with('status', "Shop {$shop->code} was added to Product {$product->code}.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function removeShop(
        ServerRequestInterface $request,
        string $productCode,
    ): RedirectResponse {
        $product = $this->find($productCode);
        Gate::authorize('update', $product);

        $data = $request->getParsedBody();

        try {
            $shop = $product
                ->shops()
                ->where('code', $data['shop'])
                ->firstOrFail();

            $product->shops()->detach($shop);
            return redirect()
                ->back()
                ->with('status', "Shop {$shop->code} was removed from Product {$product->code}.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    // เพิ่มเมธอดสำหรับลบ PD102..PD105 (เว้น 'yourname')
    public function purgeSample(\Psr\Http\Message\ServerRequestInterface $request): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', Product::class);

        $codes = ['PD102', 'PD103', 'PD104', 'PD105'];

        try {
            // ลบโดยตรงจาก DB แต่ป้องกันไม่ให้ลบ 'yourname'
            $deleted = Product::whereIn('code', $codes)
                ->where('code', '!=', 'yourname')
                ->delete();

            return redirect()->route('products.list')
                ->with('status', "{$deleted} product(s) deleted.");
        } catch (\Illuminate\Database\QueryException $excp) {
            return redirect()->route('products.list')
                ->withErrors(['alert' => $excp->errorInfo[2]]);
        }
    }

    // เพิ่มเมธอดสำหรับลบ product ที่มีโค้ด 'pd001' (จะไม่ลบ 'yourname')
    public function purgePd001(\Psr\Http\Message\ServerRequestInterface $request): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', Product::class);

        $codes = ['pd001', 'PD001']; // ใส่รูปแบบรหัสที่ต้องการลบ (case-sensitive ตาม DB) 

        try {
            $deleted = Product::whereIn('code', $codes)
                ->where('code', '!=', 'yourname')
                ->delete();

            return redirect()->route('products.list')
                ->with('status', "{$deleted} product(s) deleted.");
        } catch (\Illuminate\Database\QueryException $excp) {
            return redirect()->route('products.list')
                ->withErrors(['alert' => $excp->errorInfo[2]]);
        }
    }
}