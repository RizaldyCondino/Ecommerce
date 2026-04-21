<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\ProductListController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UserAddressController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;


Route::get('/', [UserController::class, 'index'])->name('user.home');



Route::get('/dashboard', [UserController::class, 'index'])->middleware(['auth', 'verified', UserMiddleware::class])
  ->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout
    Route::prefix('checkout')->controller(CheckoutController::class)->group((function(){
        Route::post('order', 'store')->name('checkout.store');
        Route::post('address', 'storeAddress')->name('checkout.address');
        Route::get('success','success')->name('checkout.success');
        Route::get('cancel','cancel')->name('checkout.cancel');
        Route::patch('address/{id}/update-main', 'updateIsmain')->name('cart.address.updateIsMain');

        Route::post('/orders/{order}/pay', 'pay')->name('orders.pay');
      
    }));
    Route::get('orders/dashboard', [OrderDashboard::class, 'index'])->name('orders.dashboard.index');
});

Route::middleware([AdminMiddleware::class])->group(function () {
    
    Route::get('/dashboard/admin', [AdminController::class, 'index'])
        ->name('admin.dashboard');
    Route::put('/transactions/{id}', [AdminController::class, 'update'])
    ->name('transactions.update');
    

    Route::get('/products', [AdminProductController::class,'index'])->name('admin.products.index');
    Route::post('/products/store', [AdminProductController::class,'store'])->name('admin.products.store');
    Route::post('/products/updated/{product}', [AdminProductController::class,'update'])->name('admin.products.update');
    Route::delete('/products/image/{id}', [AdminProductController::class,'deleteImage'])->name('admin.products.image.delete');
    Route::put('/products/{product}/published', [AdminProductController::class, 'updatePublished'])
    ->name('products.updatePublished');


    Route::controller(BrandController::class)->group(function(){
        Route::get('brand', 'index')->name('brand.index');
        Route::post('brand/store', 'store')->name('brand.store');
        Route::put('brand/{brand}', 'update')->name('brand.update');
    });

     Route::controller(CategoryController::class)->group(function () {
        Route::get('category', 'index')->name('category.index');
        Route::post('category/store', 'store')->name('category.store');
        Route::put('category/{category}', 'update')->name('category.update');
    });
    
});

Route::prefix('cart')->controller(CartController::class)->group(function (){
    Route::get('view', 'view')->name('cart.view');
    Route::post('store/{product}','store')->name('cart.store');
    Route::patch('update/{product}','update')->name('cart.update');
    Route::delete('delete/{product}', 'delete')->name('cart.delete');
});

Route::prefix('productList')->controller(ProductListController::class)->group(function(){
    Route::get('/','index')->name('products.index');
    Route::get('view/{id}', 'view')->name('productsList.view');
    Route::patch('update/{product}','storeOrUpdate')->name('productsLisCart.update');

    //
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
