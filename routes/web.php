<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyActiveUser;
use App\Http\Middleware\VerifyAdmin;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', VerifyActiveUser::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Administrador - Solo módulos exclusivos
    Route::middleware(['role:Administrador'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users');
            Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::post('/users/validate-field', [UserController::class, 'validateField'])->name('admin.users.validate-field');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::patch('/users/{user}', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
            Route::get('/users/eliminados', [UserController::class, 'eliminados'])->name('admin.users.eliminados');
            Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
            Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.forceDelete');
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs');
            Route::get('/audit-logs/{id}/details', [AuditLogController::class, 'details'])->name('admin.audit-logs.details');
        });
        
        // Rutas de eliminados y restauración (solo admin)
        Route::get('/clients/eliminados', [ClientController::class, 'eliminados'])->name('clients.eliminados');
        Route::post('/clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');
        Route::delete('/clients/{id}/force-delete', [ClientController::class, 'forceDelete'])->name('clients.force-delete');
        
        Route::get('/products/eliminados', [ProductController::class, 'eliminados'])->name('products.eliminados');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
    });

    // Rutas de Clientes - Administrador y Secretario
    Route::middleware(['role:Administrador|Secretario'])->group(function () {
        Route::post('/clients/validate-field', [ClientController::class, 'validateField'])->name('clients.validate-field');
        Route::resource('clients', ClientController::class);
    });

    // Rutas de Productos - Administrador y Bodega
    Route::middleware(['role:Administrador|Bodega'])->group(function () {
        Route::post('/products/validate-field', [ProductController::class, 'validateField'])->name('products.validate-field');
        Route::resource('products', ProductController::class);
        Route::get('/api/products/{product}', [ProductController::class, 'getProduct'])->name('products.get');
    });

    // Rutas específicas de Facturas - Solo Administrador (DEBEN ir PRIMERO)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/invoices/eliminados', [InvoiceController::class, 'eliminados'])->name('invoices.eliminados');
        Route::post('/invoices/{id}/restore', [InvoiceController::class, 'restore'])->name('invoices.restore');
        Route::delete('/invoices/{id}/force-delete', [InvoiceController::class, 'forceDelete'])->name('invoices.force-delete');
    });

    // Rutas específicas de Facturas - Administrador y Ventas (CRUD específico)
    Route::middleware(['role:Administrador|Ventas'])->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    });

    // Rutas de Facturas - Administrador, Ventas y Secretario (con parámetros)
    Route::middleware(['role:Administrador|Ventas|Secretario'])->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    });

    // Rutas de Facturas con parámetros - Solo Administrador y Ventas 
    Route::middleware(['role:Administrador|Ventas'])->group(function () {
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::get('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'confirmCancel'])->name('invoices.confirm-cancel');
        Route::get('/invoices/{invoice}/confirm-delete', [InvoiceController::class, 'destroy'])->name('invoices.confirm-delete');
        Route::post('/invoices/{invoice}/destroy', [InvoiceController::class, 'confirmDestroy'])->name('invoices.confirm-destroy');
    });


});

// Endpoint que retorna si el usuario sigue activo
Route::get('/check-active', function () {
    if (Auth::check()) {
        $user = \App\Models\User::withTrashed()->find(Auth::id());

        if (!$user || !$user->is_active || $user->trashed()) {
            // Buscamos el último registro de auditoría relacionado
            $lastLog = \App\Models\AuditLog::where('record_id', $user ? $user->id : null)
                ->where('table_name', 'users')
                ->whereIn('action', ['delete', 'deactivate'])
                ->latest()
                ->first();

            $reason = $lastLog
                ? $lastLog->reason
                : ($user ? $user->deactivation_reason : 'Cuenta eliminada.');

            return response()->json([
                'is_active' => false,
                'reason' => $reason ?? 'Su sesión ha finalizado, contacte con el administrador.',
            ]);
        }

        return response()->json(['is_active' => true]);
    }

    return response()->json([
        'is_active' => false,
        'reason' => 'Su sesión ha finalizado, contacte con el administrador.',
    ]);
})->name('check-active');



// Endpoint que cierra sesión y redirige con mensaje
Route::get('/force-logout', function (Request $request) {
    // No uses Auth::logout() si el usuario no existe
    if (Auth::check()) {
        Auth::logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $reason = $request->query('reason', 'Su sesión ha finalizado, contacte con el administrador.');

    return redirect('/login?reason='.urlencode($reason));
})->name('force-logout');

require __DIR__.'/auth.php';
