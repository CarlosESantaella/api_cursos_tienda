<?php

use Illuminate\Http\Request;
use App\Models\Discount\Discount;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Tienda\CartController;
use App\Http\Controllers\Tienda\HomeController;
use App\Http\Controllers\Tienda\CheckoutController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Course\ClaseGController;
use App\Http\Controllers\Admin\Course\CourseGController;
use App\Http\Controllers\Tienda\ProfileClientController;
use App\Http\Controllers\Admin\Course\SeccionGController;
use App\Http\Controllers\Admin\Course\CategoriesController;
use App\Http\Controllers\Admin\Discount\DiscountController;
use App\Http\Controllers\Tienda\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login_tienda', [AuthController::class, 'login_tienda'])->name('login_tienda');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::resource('/users', UserController::class)->except(['edit', 'create', 'update']);
    Route::post('/users/{id}', [UserController::class, "update"]);
    //
    Route::resource('/categories', CategoriesController::class)->except(['edit', 'create', 'update']);
    Route::post('/categories/{id}', [CategoriesController::class, "update"]);

    //
    

    Route::post('/course/upload_video/{id}', [CourseGController::class, "uploadVideo"]);
    Route::get('/course/config', [CourseGController::class, "config"]);
    Route::resource('/course', CourseGController::class);
    Route::post('/course/{id}', [CourseGController::class, "update"]);

    //

    Route::apiResource('/course-section', SeccionGController::class);

    Route::apiResource('/course-clases', ClaseGController::class);
    Route::post('/course-clases-file', [ClaseGController::class, "addFiles"]);
    Route::delete('/course-clases-file/{id}', [ClaseGController::class, "removeFiles"]);
    Route::post('/course-clases/upload_video/{id}', [ClaseGController::class, "uploadVideo"]);


    Route::get('/coupon/config', [CouponController::class, "config"]);
    Route::apiResource('/coupon', CouponController::class);


    Route::apiResource('/discount', DiscountController::class);




});

Route::group([
    "prefix" => "ecommerce"
], function($router){
    Route::get("home", [HomeController::class, 'home']);
    Route::get("config_all", [HomeController::class, 'config_all']);
    Route::post("list_courses", [HomeController::class, 'listCourses']);

    Route::get("course-detail/{slug}", [HomeController::class, 'course_detail']);
    Route::apiResource('/cart', CartController::class);
    
    
    
    Route::group([
        'middleware' => 'api',
    ], function ($router) {

        Route::get('/course_leason/{slug}', [HomeController::class, "course_leason"]);
        
        Route::post('/apply_coupon', [CartController::class, "apply_coupon"]);
        Route::post('/checkout', [CheckoutController::class, "store"]);
        Route::resource('/cart', CartController::class);
        Route::post('/profile', [ProfileClientController::class, "profile"]);
        Route::post('/update_client', [ProfileClientController::class, "update_client"]);

        Route::apiResource('/review', ReviewController::class);


    });
});