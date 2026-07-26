<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/titles', [TitleController::class, 'index'])->name('titles.index');
    Route::post('/titles', [TitleController::class, 'store'])->name('titles.store');
    Route::patch('/titles/{title}', [TitleController::class, 'update'])->name('titles.update');
    Route::delete('/titles/{title}', [TitleController::class, 'destroy'])->name('titles.destroy');

    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::patch('/admin/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::post('/admin/customers/{customer}/reset-password', [CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
