<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Models\Product;
use App\Models\Transaction;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {

    $products = Product::all();

    $productCount = Product::count();
    $categoryCount = \App\Models\Categories::count();
    $totalStock = Product::sum('stock');

    $pendingCount = Transaction::where('status', 'pending')->count();

    $cancelledCount = Transaction::where('status', 'cancelled')->count();

    $completedCount = Transaction::where('status', 'completed')->count();

    return view('dashboard', compact(
        'products',
        'productCount',
        'categoryCount',
        'totalStock',
        'pendingCount',
        'cancelledCount',
        'completedCount'
    ));

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
