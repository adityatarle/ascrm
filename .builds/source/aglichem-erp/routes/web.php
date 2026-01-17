<?php

use App\Livewire\Dealers\DealerForm;
use App\Livewire\Masters\ProductsTable;
use App\Livewire\Orders\CreateOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Products
    Route::get('/products', ProductsTable::class)->name('products.index');
    Route::get('/products/create', \App\Livewire\Masters\ProductForm::class)->name('products.create');
    Route::get('/products/{product}/edit', \App\Livewire\Masters\ProductForm::class)->name('products.edit');

    // Dealers
    Route::get('/dealers', \App\Livewire\Dealers\DealersTable::class)->name('dealers.index');
    Route::get('/dealers/create', DealerForm::class)->name('dealers.create');
    Route::get('/dealers/{dealer}/edit', DealerForm::class)->name('dealers.edit');

    // Orders
    Route::get('/orders', function () {
        return view('orders.index');
    })->name('orders.index');
    Route::get('/orders/create', CreateOrder::class)->name('orders.create');
    Route::get('/orders/{order}', function (\App\Models\Order $order) {
        // Check if user can view this order
        if ($order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $order->load(['items.product', 'dealer', 'organization']);
        return view('orders.show', compact('order'));
    })->name('orders.show');
    Route::get('/orders/{order}/edit', function (\App\Models\Order $order) {
        // Check if user can update this order
        if ($order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        return view('orders.edit', compact('order'));
    })->name('orders.edit')->middleware('can:update,order');
    Route::delete('/orders/{order}', function (\App\Models\Order $order) {
        // Check organization and permissions
        if ($order->organization_id !== auth()->user()->organization_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if (auth()->user()->hasRole('admin') && $order->status === 'pending') {
            $order->delete();
            return response()->json(['success' => true, 'message' => 'Order deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Cannot delete this order'], 403);
    })->name('orders.destroy')->middleware('role:admin');

    // Dispatches
    Route::get('/dispatches', function () {
        return view('dispatches.index');
    })->name('dispatches.index');
    Route::get('/dispatches/{dispatch}', function (\App\Models\Dispatch $dispatch) {
        // Check if user can view this dispatch
        if ($dispatch->order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $dispatch->load(['order.dealer', 'order.items.product']);
        return view('dispatches.show', compact('dispatch'));
    })->name('dispatches.show');
    Route::get('/dispatches/{dispatch}/edit', function (\App\Models\Dispatch $dispatch) {
        // Check if user can update this dispatch
        if ($dispatch->order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        return view('dispatches.edit', compact('dispatch'));
    })->name('dispatches.edit')->middleware('can:update,dispatch');
    Route::delete('/dispatches/{dispatch}', function (\App\Models\Dispatch $dispatch) {
        // Check organization and permissions
        if ($dispatch->order->organization_id !== auth()->user()->organization_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if (auth()->user()->hasRole('admin')) {
            $dispatch->delete();
            return response()->json(['success' => true, 'message' => 'Dispatch deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Only administrators can delete dispatches'], 403);
    })->name('dispatches.destroy')->middleware('role:admin');

    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', \App\Livewire\Users\UsersTable::class)->name('users.index');
        Route::get('/users/create', \App\Livewire\Users\UserForm::class)->name('users.create');
        Route::get('/users/{user}/edit', \App\Livewire\Users\UserForm::class)->name('users.edit');
    });
});

require __DIR__.'/auth.php';
