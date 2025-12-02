<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::prefix('admin')->middleware(['auth', 'ensure.is.admin', 'ensure.is.active'])->as('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD de clientes
    Route::resource('clients', ClientController::class);
    Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status');
    Route::get('clients-ajax', [ClientController::class, 'getClients'])
        ->name('clients.ajax');

    Route::get('clients-export', [ClientController::class, 'export'])
        ->name('clients.export');
    Route::post('clients-import', [ClientController::class, 'import'])
        ->name('clients.import');
    Route::get('clients-template', [ClientController::class, 'downloadTemplate'])
        ->name('clients.template');

    // Rutas de mensajería masiva con imagen
    Route::get('/messages', [MessageController::class, 'index'])->name('message.index');
    Route::post('presentar-papeleria', [MessageController::class, 'presentarPapeleria'])
        ->name('messages.presentar-papeleria');
    Route::post('oferta-servicios', [MessageController::class, 'ofertaServicios'])
        ->name('messages.oferta-servicios');
    Route::post('info-servicios', [MessageController::class, 'infoServicios'])
        ->name('messages.info-servicios');
    Route::post('seguimiento-clientes', [MessageController::class, 'seguimientoClientes'])
        ->name('messages.seguimiento-clientes');
    Route::post('messages/individual', [MessageController::class, 'enviarIndividual'])
        ->name('messages.individual');
});

require __DIR__ . '/auth.php';
