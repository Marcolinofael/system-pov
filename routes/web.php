<?php

use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('beneficiarios/{beneficiario}/foto', [BeneficiarioController::class, 'foto'])->name('beneficiarios.foto');
    Route::get('beneficiarios/{beneficiario}/pdf', [BeneficiarioController::class, 'pdf'])->name('beneficiarios.pdf');
    Route::resource('beneficiarios', BeneficiarioController::class)->parameters(['beneficiarios' => 'beneficiario']);

    Route::get('atendimentos/fotos/{foto}', [AtendimentoController::class, 'foto'])->name('atendimentos.foto');
    Route::resource('atendimentos', AtendimentoController::class)->except('show');

    Route::resource('users', UserController::class)->except('show')->middleware('can:admin');
});
