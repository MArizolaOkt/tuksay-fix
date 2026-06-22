<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOutletController;
use App\Http\Controllers\BarangController;
// BiayaOperasionalController dihapus (Perubahan 5 — SKILL.md)
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\LogistikController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

// Welcome / landing
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // ─── Profile (Breeze default) ───────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Master Data: Customers ─────────────────────────────────────
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/outlets-json', [CustomerController::class, 'outlets'])
        ->name('customers.outlets');
    // Nested outlets
    Route::post('/customers/{customer}/outlets', [CustomerOutletController::class, 'store'])
        ->name('customer-outlets.store');
    Route::patch('/customers/{customer}/outlets/{outlet}', [CustomerOutletController::class, 'update'])
        ->name('customer-outlets.update');
    Route::delete('/customers/{customer}/outlets/{outlet}', [CustomerOutletController::class, 'destroy'])
        ->name('customer-outlets.destroy');

    // ─── Master Data: Barangs ───────────────────────────────────────
    Route::resource('barangs', BarangController::class)->except(['show']);

    // ─── Master Data: Biaya Operasional (DIHAPUS — SKILL.md Perubahan 5) ────

    // ─── Purchase Orders ────────────────────────────────────────────
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])
        ->name('purchase-orders.status');
    Route::delete('/purchase-orders/{purchaseOrder}/destroy', [PurchaseOrderController::class, 'destroy'])
        ->name('purchase-orders.delete');

    // ─── Belanja ────────────────────────────────────────────────────
    Route::get('/belanja/konsolidasi', [BelanjaController::class, 'konsolidasi'])
        ->name('belanja.konsolidasi');
    Route::post('/belanja/harga', [BelanjaController::class, 'inputHarga'])
        ->name('belanja.input-harga');

    // ─── Logistik / Surat Jalan ─────────────────────────────────────
    Route::get('/logistik', [LogistikController::class, 'index'])->name('logistik.index');
    Route::get('/logistik/create', [LogistikController::class, 'create'])->name('logistik.create');
    Route::post('/logistik/generate', [LogistikController::class, 'generate'])->name('logistik.generate');
    Route::get('/logistik/{suratJalan}', [LogistikController::class, 'show'])->name('logistik.show');
    Route::get('/logistik/{suratJalan}/print', [LogistikController::class, 'print'])->name('logistik.print');

    // ─── Invoices ───────────────────────────────────────────────────
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::patch('/invoices/{invoice}/lunas', [InvoiceController::class, 'markLunas'])->name('invoices.lunas');

    // ─── Finance Reports ────────────────────────────────────────────
    Route::get('/finance/dashboard', [FinanceReportController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/price-trend', [FinanceReportController::class, 'priceTrend'])->name('finance.price-trend');
    Route::get('/finance/pl', [FinanceReportController::class, 'plReport'])->name('finance.pl');
    Route::get('/finance/margin', [FinanceReportController::class, 'marginAnalysis'])->name('finance.margin');
});

require __DIR__.'/auth.php';
