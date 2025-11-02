<?php
 
namespace App\Http\Controllers;
 
use App\Models\Shop;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Database\QueryException; 
use Illuminate\Support\Facades\Gate;
 
class ShopController extends SearchableController
{
    // กำหนดจำนวนรายการที่แสดงต่อหน้า
    const int MAX_ITEMS = 5;
 
    // คืนค่า query เริ่มต้น ใช้ orderBy 'code'
    #[\Override]
    function getQuery(): Builder
    {
        return Shop::orderBy('code');
    }
 
    // เตรียม criteria สำหรับการค้นหา รับแค่ 'term' ตัวเดียว
    #[\Override]
    function prepareCriteria(array $criteria): array
    {
        return [
            ...parent::prepareCriteria($criteria),
            'term' => $criteria['term'] ?? null,
        ];
    }
 
    // กรอง query ด้วย 'term' ค้นหาใน code, name, owner ด้วย LIKE แบบไม่ case sensitive
        #[\Override]
        function filter(Builder|Relation $query, array $criteria): Builder|Relation
        {
            if (!empty($criteria['term'])) {
                $term = $criteria['term'];
 
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(code) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(owner) LIKE ?', ["%{$term}%"]);
                });
            }
 
            return $query;
        }
 
    // ฟังก์ชันแสดงรายการร้านค้า (list) พร้อม pagination และส่ง criteria กลับไป view
    function list(ServerRequestInterface $request): View
    {
        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('products');
 
 
        return view('shops.list', [
            'criteria' => $criteria,
            'shops' => $query->paginate(self::MAX_ITEMS),
        ]);
    }
 
    // ฟังก์ชันแสดงรายละเอียดร้านค้า ตาม code
    function view(string $shopCode): View
    {
        $shop = $this->find($shopCode);
 
        return view('shops.view', [
            'shop' => $shop,
        ]);
    }
 
    // แสดงฟอร์มสร้างร้านใหม่
    function showCreateForm(): View
    {
        return view('shops.create-form');
    }
 
    // สร้างร้านใหม่ แล้ว redirect กลับหน้า list
    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Shop::class);
       $data = $request->getParsedBody();
 
        $data['address'] = $data['address'] ?? ''; // หรือกำหนดค่า default เป็น '' หรือ null ตามต้องการ
        $data['latitude'] = $data['latitude'] ?? 0;
        $data['longitude'] = $data['longitude'] ?? 0;

        try {
            $shop = Shop::create($data);

            return redirect(
                session()->get('bookmarks.shops.create-form', route('shops.list'))
            )
                ->with('status', "Shop {$shop->code} was created.");
        } catch (QueryException $excp) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'alert' => $excp->errorInfo[2],
                ]);
        }

    }
 
    // แสดงฟอร์มแก้ไขร้านค้า
    function showUpdateForm(string $shopCode): View
    {
        $shop = $this->find($shopCode);
 
        return view('shops.update-form', [
            'shop' => $shop,
        ]);
    }
 
    // บันทึกการแก้ไขร้านค้า แล้ว redirect ไปหน้า view ร้านนั้น
    function update(ServerRequestInterface $request, string $shopCode): RedirectResponse
    {
        $shop = $this->find($shopCode);
        Gate::authorize('update', $shop);

        $data = $request->getParsedBody();

        try {
            $shop->fill($data);
            $shop->save();

            return redirect()
                ->route('shops.view', [
                    'shop' => $shop->code,
                ])
                ->with('status', "Shop {$shop->code} was updated.");
        } catch (QueryException $excp) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'alert' => $excp->errorInfo[2],
                ]);
        }

    }
 
    // ลบร้านค้า แล้ว redirect กลับหน้า list
    function delete(string $shopCode): RedirectResponse
    {
        $shop = $this->find($shopCode);
        Gate::authorize('delete', $shop);

        try {
            $shop->delete();

            return redirect(
                session()->get('bookmarks.shops.view', route('shops.list'))
            )
                ->with('status', "Shop {$shop->code} was deleted.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()
                ->back()
                ->withErrors([
                    'alert' => $excp->errorInfo[2],
                ]);
        }

    }
 
     function viewProducts(
        ServerRequestInterface $request,
        ProductController $productController,
        string $shopCode
        ): View
        {
        $shop = $this->find($shopCode); //ดึง productมา
        $criteria = $productController->prepareCriteria($request->getQueryParams()); //ต้องส่งไป prepare ที่ shopController ก่อน เอา $criteria มาจาก $request->getQueryParams() -> criteria ใช้ searchตัวของshop
        $query = $productController // เรียก filter
 
        ->filter($shop->products(), $criteria)
        ->withCount('shops');
       
        return view('shops.view-products', [
        'shop' => $shop,
        'criteria' => $criteria,
        'products' => $query->paginate($productController::MAX_ITEMS),
        ]);
        }
 
 
          function showAddProductsForm(
        ServerRequestInterface $request,
        ProductController $productController,
        string $shopCode
    ): View {
        $shop = $this->find($shopCode);
        $criteria = $productController->prepareCriteria($request->getQueryParams());
 
        $query = $productController
            ->getQuery()
            ->whereDoesntHave(
                'shops',
                function (Builder $innerQuery) use ($shop): void {
                    $innerQuery->where('code', $shop->code);
                }
            );
 
        $query = $productController
            ->filter($query, $criteria)
             ->with('category')
            ->withCount('shops');
 
        return view('shops.add-products-form', [
            'criteria' => $criteria,
            'shop' => $shop,
            'products' => $query->paginate($productController::MAX_ITEMS),
        ]);
    }
 
        function addProduct(
            ServerRequestInterface $request,
            ProductController $productController,
            string $shopCode
        ): RedirectResponse {
            $shop = $this->find($shopCode);
            Gate::authorize('update', $shop);
            $data = $request->getParsedBody();
 
            $product = $productController
                ->getQuery()
                ->whereDoesntHave(
                    'shops',
                    function (Builder $innerQuery) use ($shop): void {
                        $innerQuery->where('code', $shop->code);
                    }
                )
                ->where('code', $data['product']) // 'product' คือชื่อ field ในฟอร์ม
                ->firstOrFail();
 
            try {
                $shop->products()->attach($product);

                return redirect()
                    ->back()
                    ->with('status', "Product {$product->code} was added to Shop {$shop->code}.");
            } catch (QueryException $excp) {
                // We don't want withInput() here.
                return redirect()
                    ->back()
                    ->withErrors([
                        'alert' => $excp->errorInfo[2],
                    ]);
            }
        }
 
 
        function removeProduct(
            ServerRequestInterface $request,
            string $shopCode
        ): RedirectResponse {
            $shop = $this->find($shopCode);
Gate::authorize('update', $shop);
            $data = $request->getParsedBody();
 
            $product = $shop
                ->products()
                ->where('code', $data['product'])
                ->firstOrFail();
 
            try {
                $shop->products()->detach($product);

                return redirect()
                    ->back()
                    ->with('status', "Product {$product->code} was removed from Shop {$shop->code}.");
            } catch (QueryException $excp) {
                // We don't want withInput() here.
                return redirect()
                    ->back()
                    ->withErrors([
                        'alert' => $excp->errorInfo[2],
                    ]);
            }
        }
}