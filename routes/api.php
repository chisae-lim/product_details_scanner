<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ClassController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\SessionController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ComponentController;
use App\Http\Controllers\API\TransportController;
use App\Http\Controllers\API\StudyDetailController;
use App\Http\Controllers\API\CourseDetailController;
use App\Http\Controllers\API\CoursePaymentController;
use App\Http\Controllers\API\TransportDetailController;
use App\Http\Controllers\API\TransportPaymentController;

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
    });
    Route::prefix('/components')->group(function () {
        Route::get('/user/permissions', [ComponentController::class, 'getUserPermissions']);
    });
});
