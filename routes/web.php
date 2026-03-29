<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminPosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Vet\MedicalRecordController;
use App\Http\Controllers\Vet\DiagnosisController;
use App\Http\Controllers\Vet\PrescriptionController;
use App\Http\Controllers\Vet\LabResultController;
use App\Http\Controllers\Vet\VaccinationController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard — all authenticated staff
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile — all authenticated staff
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| POS — admin and receptionist only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,receptionist'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {
        Route::get('/', [AdminPosController::class, 'index'])->name('index');
        Route::get('/items/search', [AdminPosController::class, 'searchItems'])->name('items.search');
        Route::post('/checkout', [AdminPosController::class, 'checkout'])->name('checkout');
        Route::get('/receipt/{invoice}', [AdminPosController::class, 'receipt'])->name('receipt');
        Route::patch('/invoice/{invoice}/void', [AdminPosController::class, 'void'])->name('void');
        Route::get('/summary', [AdminPosController::class, 'summary'])->name('summary');
    });

/*
|--------------------------------------------------------------------------
| Admin — admin only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');})->name('dashboard');
            
        // User management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Inventory & suppliers
        Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class);
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
        Route::resource('purchase-orders', \App\Http\Controllers\Admin\PurchaseOrderController::class);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/financial', [\App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('financial');
            Route::get('/appointments', [\App\Http\Controllers\Admin\ReportController::class, 'appointments'])->name('appointments');
            Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('inventory');
        });

        // Audit logs
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    });

/*
|--------------------------------------------------------------------------
| Vet — vet and admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,vet'])
    ->prefix('vet')
    ->name('vet.')
    ->group(function () {
        Route::resource('medical-records', \App\Http\Controllers\Vet\MedicalRecordController::class);
        Route::resource('diagnoses', \App\Http\Controllers\Vet\DiagnosisController::class);
        Route::resource('prescriptions', \App\Http\Controllers\Vet\PrescriptionController::class);
        Route::resource('lab-results', \App\Http\Controllers\Vet\LabResultController::class);
        Route::resource('vaccinations', \App\Http\Controllers\Vet\VaccinationController::class);
    });

/*
|--------------------------------------------------------------------------
| Receptionist — admin, receptionist, and assistant
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,receptionist,assistant'])
    ->prefix('clinic')
    ->name('clinic.')
    ->group(function () {
        // Clients & pets
        Route::resource('clients', \App\Http\Controllers\Clinic\ClientController::class);
        Route::resource('clients.pets', \App\Http\Controllers\Clinic\PetController::class)->shallow();

        // Appointments
        Route::resource('appointments', \App\Http\Controllers\Clinic\AppointmentController::class);
        Route::patch('appointments/{appointment}/status', [\App\Http\Controllers\Clinic\AppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Online booking approvals
        Route::resource('online-bookings', \App\Http\Controllers\Clinic\OnlineBookingController::class)->only(['index', 'show']);
        Route::patch('online-bookings/{onlineBooking}/approve', [\App\Http\Controllers\Clinic\OnlineBookingController::class, 'approve'])->name('online-bookings.approve');
        Route::patch('online-bookings/{onlineBooking}/reject', [\App\Http\Controllers\Clinic\OnlineBookingController::class, 'reject'])->name('online-bookings.reject');

        // Invoices
        Route::resource('invoices', \App\Http\Controllers\Clinic\InvoiceController::class)->only(['index', 'show']);

        // Reminders
        Route::resource('reminders', \App\Http\Controllers\Clinic\ReminderController::class)->only(['index', 'store', 'destroy']);

        // Messages / communication hub
        Route::get('messages', [\App\Http\Controllers\Clinic\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{client}', [\App\Http\Controllers\Clinic\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{client}', [\App\Http\Controllers\Clinic\MessageController::class, 'send'])->name('messages.send');
    });

require __DIR__ . '/auth.php';