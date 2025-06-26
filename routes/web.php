<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceDetectionController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect('login');
});
Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //Upload Images
    Route::get('/upload-image',[FaceDetectionController::class,'uploadImage'])->name('uploadImage');
    Route::post('/post-upload-image',[FaceDetectionController::class,'postUploadImage'])->name('postUploadImage');

    // Live Capture images
    Route::get('/live-capture',[FaceDetectionController::class,'liveCapture'])->name('liveCapture');
    Route::post('/live-capture',[FaceDetectionController::class,'proceedLiveCapture'])->name('proceedLiveCapture');

    //purchase credit
    Route::get('purchase-credit',[CreditController::class,'purchaseCredit'])->name('purchaseCredit');

    Route::get('transaction-history',function(){
        return view('history.transaction');
    })->name('transactionHistory');

    Route::get('settings',[\App\Http\Controllers\HomeController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [\App\Http\Controllers\HomeController::class, 'update'])->name('settings.update');
});


//Admin Routes
// Route::get('admin/login',function(){
//      return view('admin.auth.login');
// });
// Route::post('admin/postlogin',[AdminController::class,'postLogin'])->name('admin.login');

Route::get('admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminController::class, 'login']);

Route::middleware(['auth:admin'])->group(function () {
    Route::post('admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('admin/dashboard', function(){
        return view('admin.dashboard');
    });
});
