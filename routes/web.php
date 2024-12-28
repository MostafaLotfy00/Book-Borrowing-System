<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\BookingController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;



// Clear Cache of Permissions for the admin
Route::get('/forget-cached-permissions', function () {
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    return response()->json(['message' => 'Cached permissions cleared.']);
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/data', [UserController::class, 'getUsers'])->name('users.data');
Route::get('/users/export-pdf', [UserController::class, 'exportPDF'])->name('users.export.pdf');

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROLES AND PERMISSIONS
    Route::get('/roles', [RolePermissionController::class, 'showAssignPermissionsForm'])->name('roles.index');
    Route::post('/roles/permissions', [RolePermissionController::class, 'assignPermissions'])->name('roles.assignPermissions');
    Route::get('/roles/{roleId}/permissions', [RolePermissionController::class, 'getPermissionsForRole']);

    // BOOKS
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('getbooks', [BookController::class, 'getBooks'])->name('books.table');
    Route::get('books/export', [BookController::class, 'exportPDF'])->name('books.export.pdf');
    Route::get('books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('books', [BookController::class, 'store'])->name('books.store');
    Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{id}/reserve', [BookingController::class, 'reserve'])->name('books.reserve');

});



require __DIR__.'/auth.php';
