<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ProductEcommerceController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\IngredientController;

/*
|--------------------------------------------------------------------------
| API Routes - Simplified Version without Authentication
|--------------------------------------------------------------------------
*/

// PENTING: Route dengan parameter dinamis harus ditempatkan setelah route dengan path tetap
// agar tidak konflik dengan route lain

// categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('api.categories.index');
        Route::post('/', [CategoryController::class, 'store'])->name('api.categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('api.categories.show');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');
    });
// product
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
        Route::post('/', [ProductController::class, 'store'])->name('api.products.store');
        Route::get('/slug/{slug}', [ProductController::class, 'findBySlug'])->name('api.products.by-slug');
        Route::get('/type/{typeId}', [ProductController::class, 'findByType'])->name('api.products.by-type');
        Route::get('/{product}', [ProductController::class, 'show'])->name('api.products.show');
        Route::put('/{product}', [ProductController::class, 'update'])->name('api.products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy');
    });

// news
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('api.news.index'); // GET /api/news
        Route::post('/', [NewsController::class, 'store'])->name('api.news.store'); // POST /api/news

        Route::get('/slug/{slug}', [NewsController::class, 'showBySlug'])->name('api.news.by-slug'); // GET /api/news/slug/{slug}
        Route::get('/highlights', [NewsController::class, 'getHighlights'])->name('api.news.highlights'); // GET /api/news/highlights
        Route::get('/you-may-like', [NewsController::class, 'getYouMayLike'])->name('api.news.you-may-like'); // GET /api/news/you-may-like
        Route::get('/you-may-like/{id}', [NewsController::class, 'getRelated'])->name('api.news.related'); // GET /api/news/you-may-like/{id}

        Route::get('/{id}', [NewsController::class, 'show'])->name('api.news.show'); // GET /api/news/{id}
        Route::put('/{id}', [NewsController::class, 'update'])->name('api.news.update'); // PUT /api/news/{id}
        Route::delete('/{id}', [NewsController::class, 'destroy'])->name('api.news.destroy'); // DELETE /api/news/{id}
    });

// video
    Route::prefix('video')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('api.video.index'); // GET /api/video
        Route::post('/', [VideoController::class, 'store'])->name('api.video.store'); // POST /api/video

        Route::get('/slug/{slug}', [VideoController::class, 'showBySlug'])->name('api.video.by-slug'); // GET /api/video/slug/{slug}
        Route::get('/highlights', [VideoController::class, 'getHighlights'])->name('api.video.highlights'); // GET /api/video/highlights
        Route::get('/you-may-like', [VideoController::class, 'getYouMayLike'])->name('api.video.you-may-like'); // GET /api/video/you-may-like
        Route::get('/you-may-like/{id}', [VideoController::class, 'getRelated'])->name('api.video.related'); // GET /api/video/you-may-like/{id}

        Route::get('/{id}', [VideoController::class, 'show'])->name('api.video.show'); // GET /api/video/{id}
        Route::put('/{id}', [VideoController::class, 'update'])->name('api.video.update'); // PUT /api/video/{id}
        Route::delete('/{id}', [VideoController::class, 'destroy'])->name('api.video.destroy'); // DELETE /api/video/{id}
    });

// product eccomerce
    Route::prefix('product-ecommerce')->group(function () {
        Route::get('/', [ProductEcommerceController::class, 'index'])->name('api.product-ecommerce.index');
        Route::post('/', [ProductEcommerceController::class, 'store'])->name('api.product-ecommerce.store');
        Route::get('/type/{type}', [ProductEcommerceController::class, 'findByType'])->name('api.product-ecommerce.by-type');
        Route::get('/product/{productId}', [ProductEcommerceController::class, 'findByProduct'])->name('api.product-ecommerce.by-product');
        Route::get('/{id}', [ProductEcommerceController::class, 'show'])->name('api.product-ecommerce.show');
        Route::put('/{id}', [ProductEcommerceController::class, 'update'])->name('api.product-ecommerce.update');
        Route::delete('/{id}', [ProductEcommerceController::class, 'destroy'])->name('api.product-ecommerce.destroy');
    });

// variant
    Route::prefix('variant')->group(function () {
        Route::get('/', [VariantController::class, 'index']);       // List variant
        Route::post('/', [VariantController::class, 'store']);       // Tambah variant
        Route::get('/{id}', [VariantController::class, 'show']);     // Lihat 1 variant
        Route::put('/{id}', [VariantController::class, 'update']);   // Update variant
        Route::delete('/{id}', [VariantController::class, 'destroy']); // Hapus variant
    });

// ingredients
    Route::get('/ingredients', [IngredientController::class, 'index']);
