<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\ColorController;
use App\Http\Controllers\API\ScaleController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ComponentController;

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

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/verify', [AuthController::class, 'verify']);
});

Route::middleware(['authorize'])->group(function () {
    Route::prefix('/user')->group(function () {
        Route::patch('/password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('/manage')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::get('/', [UserController::class, 'getUsers']);
            Route::post('/create', [UserController::class, 'createUser']);
            Route::get('/read/{id_user}', [UserController::class, 'readUser']);
            Route::put('/update', [UserController::class, 'updateUser']);
            Route::patch('/disable', [UserController::class, 'disableUser']);
            Route::patch('/enable', [UserController::class, 'enableUser']);
            Route::patch('/forbid', [UserController::class, 'forbidUser']);
            Route::patch('/permit', [UserController::class, 'permitUser']);
            Route::delete('/delete/{id_user}', [UserController::class, 'deleteUser']);
        });
        Route::prefix('/brands')->group(function () {
            Route::get('/', [BrandController::class, 'getBrands']);
            Route::post('/create', [BrandController::class, 'createBrand']);
            Route::get('/read/{id_brand}', [BrandController::class, 'readBrand']);
            Route::put('/update', [BrandController::class, 'updateBrand']);
            Route::delete('/delete/{id_brand}', [BrandController::class, 'deleteBrand']);
        });
        Route::prefix('/categories')->group(function () {
            Route::get('/', [CategoryController::class, 'getCategories']);
            Route::post('/create', [CategoryController::class, 'createCategory']);
            Route::get('/read/{id_category}', [CategoryController::class, 'readCategory']);
            Route::put('/update', [CategoryController::class, 'updateCategory']);
            Route::delete('/delete/{id_category}', [CategoryController::class, 'deleteCategory']);
        });
        Route::prefix('/scales')->group(function () {
            Route::get('/', [ScaleController::class, 'getScales']);
            Route::post('/create', [ScaleController::class, 'createScale']);
            Route::get('/read/{id_scale}', [ScaleController::class, 'readScale']);
            Route::put('/update', [ScaleController::class, 'updateScale']);
            Route::delete('/delete/{id_scale}', [ScaleController::class, 'deleteScale']);
        });
        Route::prefix('/units')->group(function () {
            Route::get('/', [UnitController::class, 'getUnits']);
            Route::post('/create', [UnitController::class, 'createUnit']);
            Route::get('/read/{id_unit}', [UnitController::class, 'readUnit']);
            Route::put('/update', [UnitController::class, 'updateUnit']);
            Route::delete('/delete/{id_unit}', [UnitController::class, 'deleteUnit']);
        });
        Route::prefix('/colors')->group(function () {
            Route::get('/', [ColorController::class, 'getColors']);
            Route::post('/create', [ColorController::class, 'createColor']);
            Route::get('/read/{id_color}', [ColorController::class, 'readColor']);
            Route::put('/update', [ColorController::class, 'updateColor']);
            Route::delete('/delete/{id_color}', [ColorController::class, 'deleteColor']);
        });
        Route::prefix('/products')->group(function () {
            Route::get('/', [ProductController::class, 'getProducts']);
            Route::post('/create', [ProductController::class, 'createProduct']);
            Route::get('/read/{id_product}', [ProductController::class, 'readProduct']);
            Route::put('/update', [ProductController::class, 'updateProduct']);
            Route::delete('/delete/{id_product}', [ProductController::class, 'deleteProduct']);
        });
    });
    Route::prefix('/components')->group(function () {
        Route::get('/user/permissions', [ComponentController::class, 'getUserPermissions']);
    });
});
