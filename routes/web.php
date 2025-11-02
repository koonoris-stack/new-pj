<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Models\category;
use App\Models\Product; // added for home route
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // added for search route
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\RegisterController;


Route::middleware([

    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',

])
    ->group(function () {

        // Search gateway: require login before showing product list results
        Route::get('/search', function (Request $request) {
            $term = $request->query('term');
            if (auth()->check()) {
                return redirect()->route('products.list', ['term' => $term]);
            }
            // store intended url so user returns after login (optional)
            session()->put('url.intended', url()->current() . ($term ? '?term=' . urlencode($term) : ''));
            return redirect()->route('login')->with('status', 'Please login to search.');
        })->name('search');

        // Public home: show a paginated product grid (no auth required)
        Route::get('/', function () {
            // show 12 items per page on home
            $products = Product::with('category')->paginate(12);
            return view('chikuru.home', ['products' => $products]);
        })->name('home');

        // Login routes (ไม่ต้องมี auth middleware)
        Route::controller(LoginController::class)
    ->prefix('auth')
    ->group(static function (): void {
        // Login routes
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
        Route::post('/logout', 'logout')->name('logout');

        // ✅ Register routes
        Route::get('/register', 'showRegisterForm')->name('auth.register');
        Route::post('/register', 'register')->name('auth.register.store');
    });

        Route::get('/register', [RegisterController::class, 'show'])->name('register');
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

        // Assign middleware group
        Route::middleware(['auth'])->group(static function (): void {

            // Routes definition for products.*
            Route::controller(ProductController::class)
                ->prefix('/products')
                ->name('products.')
                ->group(static function (): void {
                    Route::get('', 'list')->name('list');
                    // Route เพื่อลบ pd001 (POST)
                    Route::post('/purge-pd001', 'purgePd001')->name('purge-pd001');
                    Route::get('/create', 'showCreateForm')->name('create-form');
                    Route::post('/create', 'create')->name('create');
                    Route::prefix('/{product}')->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/update', 'update')->name('update');
                        Route::post('/delete', 'delete')->name('delete');
                        Route::prefix('/shops')->group(static function (): void {
                            Route::get('', 'viewShops')->name('view-shops');
                            Route::get('/add', 'showAddShopsForm')->name('add-shops-form');
                            Route::post('/add', 'addShop')->name('add-shop');
                            Route::post('/remove', 'removeShop')->name('remove-shop');
                        });
                    });
                });

            // Routes definition for shops.*
            Route::controller(ShopController::class)
                ->prefix('/shops')
                ->name('shops.')
                ->group(static function (): void {
                    Route::get('', 'list')->name('list');
                    Route::get('/create', 'showCreateForm')->name('create-form');
                    Route::post('/create', 'create')->name('create');
                    Route::prefix('/{shop}')->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/update', 'update')->name('update');
                        Route::post('/delete', 'delete')->name('delete');
                        Route::prefix('/products')->group(static function (): void {
                            Route::get('', 'viewProducts')->name('view-products');
                            Route::get('/add', 'showAddProductsForm')->name('add-products-form');
                            Route::post('/add', 'addProduct')->name('add-product');
                            Route::post('/remove', 'removeProduct')->name('remove-product');
                        });
                    });
                });

            // Routes definition for categories.*
            Route::controller(CategoryController::class)
                ->prefix('/categories')
                ->name('categories.')
                ->group(static function (): void {
                    Route::get('', 'list')->name('list');
                    Route::get('/create', 'showCreateForm')->name('create-form');
                    Route::post('/create', 'create')->name('create');
                    Route::prefix('/{category}')->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/update', 'update')->name('update');
                        Route::post('/delete', 'delete')->name('delete');
                        Route::prefix('/products')->group(static function (): void {
                            Route::get('', 'viewProducts')->name('view-products');
                            Route::get('/add', 'showAddProductsForm')->name('add-products-form');
                            Route::post('/add', 'addProduct')->name('add-product');
                        });
                    });
                });

            // Routes definition for users.*
            Route::middleware(['auth'])->controller(\App\Http\Controllers\UserController::class)
                ->prefix('/users')
                ->name('users.')
                ->group(static function (): void {
                    Route::get('', 'list')->name('list');
                    Route::get('/create', 'showCreateForm')->name('create-form');
                    Route::post('/create', 'create')->name('create');

                    // Self routes (current authenticated user)
                    Route::get('/self', 'viewSelf')->name('self.view');
                    Route::get('/self/update', 'showUpdateSelfForm')->name('self.update-form');
                    Route::post('/self/update', 'updateSelf')->name('self.update');

                    Route::prefix('/{user}')->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/update', 'update')->name('update');
                        Route::post('/delete', 'delete')->name('delete');
                    });
                });
        });
    });
