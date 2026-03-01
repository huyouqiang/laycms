<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\FormGroupController;
use App\Http\Controllers\FormRelationController;
use App\Http\Controllers\TableDataController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.cms'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('form-groups')->name('form-groups.')->middleware('permission:forms.read')->group(function () {
        Route::get('/', [FormGroupController::class, 'index'])->name('index');
        Route::post('/', [FormGroupController::class, 'store'])->name('store');
        Route::put('/{group}', [FormGroupController::class, 'update'])->name('update');
        Route::delete('/{group}', [FormGroupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('forms')->name('forms.')->group(function () {
        Route::get('/', [FormController::class, 'index'])->middleware('permission:forms.read')->name('index');
        Route::get('/create', [FormController::class, 'create'])->middleware('permission:forms.create')->name('create');
        Route::get('/{form}/related-columns', [FormRelationController::class, 'getRelatedColumns'])->middleware('permission:forms.read')->name('related-columns');
        Route::post('/', [FormController::class, 'store'])->middleware('permission:forms.create')->name('store');
        Route::post('/{form}/add-index', [FormController::class, 'addIndex'])->middleware('permission:forms.update')->name('add-index');
        Route::delete('/{form}/drop-index', [FormController::class, 'dropIndex'])->middleware('permission:forms.update')->name('drop-index');
        Route::get('/{form}', [FormController::class, 'edit'])->middleware('permission:forms.read')->name('edit');
        Route::put('/{form}', [FormController::class, 'update'])->middleware('permission:forms.update')->name('update');
        Route::delete('/{form}', [FormController::class, 'destroy'])->middleware('permission:forms.delete')->name('destroy');
    });

    Route::prefix('form-relations')->name('form-relations.')->middleware('permission:forms.update')->group(function () {
        Route::post('/', [FormRelationController::class, 'store'])->name('store');
        Route::delete('/{relation}', [FormRelationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('form-fields')->name('form-fields.')->group(function () {
        Route::get('/{form}', [FormFieldController::class, 'index'])->middleware('permission:forms.read')->name('index');
        Route::post('/', [FormFieldController::class, 'store'])->middleware('permission:forms.update')->name('store');
        Route::put('/{field}', [FormFieldController::class, 'update'])->middleware('permission:forms.update')->name('update');
        Route::delete('/{field}', [FormFieldController::class, 'destroy'])->middleware('permission:forms.delete')->name('destroy');
    });

    Route::post('/api/upload', [UploadController::class, 'store'])->name('upload.store');

    Route::prefix('table-data')->name('table-data.')->group(function () {
        Route::get('/relation-options', [TableDataController::class, 'relationOptions'])->middleware('auth.cms')->name('relation-options');
        Route::get('/{tableName}', [TableDataController::class, 'index'])->middleware('table.permission:read')->name('index');
        Route::get('/{tableName}/create', [TableDataController::class, 'create'])->middleware('table.permission:create')->name('create');
        Route::post('/{tableName}', [TableDataController::class, 'store'])->middleware('table.permission:create')->name('store');
        Route::get('/{tableName}/{id}/edit', [TableDataController::class, 'edit'])->middleware('table.permission:update')->name('edit');
        Route::put('/{tableName}/{id}', [TableDataController::class, 'update'])->middleware('table.permission:update')->name('update');
        Route::delete('/{tableName}/{id}', [TableDataController::class, 'destroy'])->middleware('table.permission:delete')->name('destroy');
    });

    Route::prefix('user-groups')->name('user-groups.')->middleware('permission:users.manage')->group(function () {
        Route::get('/', [UserGroupController::class, 'index'])->name('index');
        Route::post('/', [UserGroupController::class, 'store'])->name('store');
        Route::put('/{group}', [UserGroupController::class, 'update'])->name('update');
        Route::delete('/{group}', [UserGroupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->name('users.')->middleware('permission:users.manage')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:users.manage')->group(function () {
        Route::get('/{group}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{group}', [PermissionController::class, 'update'])->name('update');
    });
});
