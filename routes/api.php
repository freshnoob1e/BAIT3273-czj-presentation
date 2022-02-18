<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Employees
Route::get('/employees/{searchTerm}',[EmployeeController::class, 'searchEmployees']);

// Jobs/Dept
Route::get('/deparment/{department}/jobs', [DepartmentController::class, 'getJobInDepartment']);
Route::get('/departments/{searchTerm}',[DepartmentController::class, 'searchDepartments']);

Route::get('/jobs/{searchTerm}',[JobController::class, 'searchJobs']);
