<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MahasiswaDataController;
use Illuminate\Support\Facades\Auth;



Route::get('/', [LoginController::class, 'login'])->name('login')->middleware(middleware: 'guest');
Route::get('actionlogin',[LoginController::class, 'login'])->middleware(middleware: 'guest');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin')->middleware(middleware: 'guest');

Route::get('home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');
Route::get('actionlogout', [LoginController::class, 'actionlogin'])->middleware('auth');

//REGISTER
Route::get('register', [RegisterController::class, 'register'])->name('register')->middleware(middleware: 'guest');
Route::post('register/action', [RegisterController::class, 'actionregister'])->name('actionregister')->middleware(middleware: 'guest');

Route::get('/mahasiswa/status', [MahasiswaDataController::class,'status'])->middleware('auth');
Route::resource('/mahasiswa', MahasiswaDataController::class)->middleware('auth');

Route::get('register/verify/{verify_key}', [RegisterController::class, 'verify'])->name('verify')->middleware(middleware: 'guest');

