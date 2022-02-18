<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/dashboard', function() {
    return redirect('/');
});

// Employee Routes
Route::middleware(['auth:sanctum', 'verified'])
        ->get('/employee', [EmployeeController::class, 'index'])
        ->name('employee');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/employee/create', [EmployeeController::class, 'create'])
        ->name('employee.create');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/employee/{employee}', [EmployeeController::class, 'show'])
        ->name('employee.show');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/employee/edit/{employee}', [EmployeeController::class, 'edit'])
        ->name('employee.edit');


Route::middleware(['auth:sanctum', 'verified'])
        ->patch('/employee/{employee}', [EmployeeController::class, 'update'])
        ->name('employee.update');

Route::middleware(['auth:sanctum', 'verified'])
        ->post('/employee', [EmployeeController::class, 'store'])
        ->name('employee.store');


// Addresse Routes
Route::middleware(['auth:sanctum', 'verified'])
        ->get('/employee/address/edit/{address}', [AddressController::class, 'edit'])
        ->name('address.edit');

Route::middleware(['auth:sanctum', 'verified'])
        ->patch('/address/{address}', [AddressController::class, 'update'])
        ->name('address.update');



// Job Routes
Route::middleware(['auth:sanctum', 'verified'])
        ->get('/job', [JobController::class, 'index'])
        ->name('jobs');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/job/create', [JobController::class, 'create'])
        ->name('job.create');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/job/{job}', [JobController::class, 'edit'])
        ->name('job.edit');

Route::middleware(['auth:sanctum', 'verified'])
        ->post('/job', [JobController::class, 'store'])
        ->name('job.store');

Route::middleware(['auth:sanctum', 'verified'])
        ->patch('/job/{job}', [JobController::class, 'update'])
        ->name('job.update');

// Department Routes
Route::middleware(['auth:sanctum', 'verified'])
        ->get('/department', [DepartmentController::class, 'index'])
        ->name('departments');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/department/create', [DepartmentController::class, 'create'])
        ->name('department.create');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/department/{department}', [DepartmentController::class, 'edit'])
        ->name('department.edit');

        Route::middleware(['auth:sanctum', 'verified'])
        ->post('/department', [DepartmentController::class, 'store'])
        ->name('department.store');

Route::middleware(['auth:sanctum', 'verified'])
        ->patch('/department/{department}', [DepartmentController::class, 'update'])
        ->name('department.update');

// Payment Routes
Route::middleware(['auth:sanctum', 'verified'])
        ->get('/pay', [PaymentController::class, 'index'])
        ->name('payments');

Route::middleware(['auth:sanctum', 'verified'])
        ->get('/pay/create', [PaymentController::class, 'create'])
        ->name('payment.create');

Route::middleware(['auth:sanctum', 'verified'])
        ->post('/pay', [PaymentController::class, 'store'])
        ->name('payment.store');

Route::post('/test/post', function(Request $req){
    try{
        $hue = 'test';
    } catch(Exception $e) {
        dd('is error');
        // dd($e);
    }
    dd($hue);
});

Route::get('/test', function() {
    return Inertia::render('test');
});

