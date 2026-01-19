<?php

use App\Livewire\Dealers\DealerForm;
use App\Livewire\Masters\ProductsTable;
use App\Livewire\Orders\CreateOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\Dashboard::class)->name('dashboard');

    // Products
    Route::get('/products', ProductsTable::class)->name('products.index');
    Route::get('/products/create', \App\Livewire\Masters\ProductForm::class)->name('products.create');
    Route::get('/products/{product}/edit', \App\Livewire\Masters\ProductForm::class)->name('products.edit');
    Route::get('/products/{product}/state-rates', \App\Livewire\Masters\ProductStateRates::class)->name('products.state-rates');

    // Units Master
    Route::get('/units', \App\Livewire\Masters\UnitsTable::class)->name('units.index');
    Route::get('/units/create', \App\Livewire\Masters\UnitForm::class)->name('units.create');
    Route::get('/units/{unit}/edit', \App\Livewire\Masters\UnitForm::class)->name('units.edit');

    // Discount Slabs
    Route::get('/discount-slabs', \App\Livewire\Masters\DiscountSlabsTable::class)->name('discount-slabs.index');
    Route::get('/discount-slabs/create', \App\Livewire\Masters\DiscountSlabForm::class)->name('discount-slabs.create');
    Route::get('/discount-slabs/{slab}/edit', \App\Livewire\Masters\DiscountSlabForm::class)->name('discount-slabs.edit');

    // State-wise Product Rates (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/state-wise-product-rates', \App\Livewire\Masters\StateWiseProductRates::class)->name('state-wise-product-rates.index');
    });

    // Category Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/categories', \App\Livewire\Masters\CategoriesTable::class)->name('categories.index');
        Route::get('/categories/create', \App\Livewire\Masters\CategoryForm::class)->name('categories.create');
        Route::get('/categories/{category}/edit', \App\Livewire\Masters\CategoryForm::class)->name('categories.edit');
    });

    // Crop Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/crops', \App\Livewire\Masters\CropsTable::class)->name('crops.index');
        Route::get('/crops/create', \App\Livewire\Masters\CropForm::class)->name('crops.create');
        Route::get('/crops/{crop}/edit', \App\Livewire\Masters\CropForm::class)->name('crops.edit');
    });

    // Banner Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/banners', \App\Livewire\Masters\BannersTable::class)->name('banners.index');
        Route::get('/banners/create', \App\Livewire\Masters\BannerForm::class)->name('banners.create');
        Route::get('/banners/{banner}/edit', \App\Livewire\Masters\BannerForm::class)->name('banners.edit');
    });

    // State Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/states', \App\Livewire\Masters\StateMasterTable::class)->name('states.index');
        Route::get('/states/create', \App\Livewire\Masters\StateMasterForm::class)->name('states.create');
        Route::get('/states/{state}/edit', \App\Livewire\Masters\StateMasterForm::class)->name('states.edit');
    });

    // District Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/districts', \App\Livewire\Masters\DistrictMasterTable::class)->name('districts.index');
        Route::get('/districts/create', \App\Livewire\Masters\DistrictMasterForm::class)->name('districts.create');
        Route::get('/districts/{district}/edit', \App\Livewire\Masters\DistrictMasterForm::class)->name('districts.edit');
    });

    // Taluka Master (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/talukas', \App\Livewire\Masters\TalukaMasterTable::class)->name('talukas.index');
        Route::get('/talukas/create', \App\Livewire\Masters\TalukaMasterForm::class)->name('talukas.create');
        Route::get('/talukas/{taluka}/edit', \App\Livewire\Masters\TalukaMasterForm::class)->name('talukas.edit');
    });

    // Dealers
    Route::get('/dealers', \App\Livewire\Dealers\DealersTable::class)->name('dealers.index');
    Route::get('/dealers/create', DealerForm::class)->name('dealers.create');
    Route::get('/dealers/{dealer}/edit', DealerForm::class)->name('dealers.edit');

    // Orders
    Route::get('/orders', \App\Livewire\Orders\OrdersTable::class)->name('orders.index');
    Route::get('/orders/create', CreateOrder::class)->name('orders.create');
    Route::get('/orders/{order}', function (\App\Models\Order $order) {
        // Check if user can view this order
        if ($order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $order->load(['items.product.unit', 'items.productSize.unit', 'dealer', 'organization', 'dispatches', 'payments', 'returns']);
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
    Route::get('/dispatches', \App\Livewire\Dispatches\DispatchesTable::class)->name('dispatches.index');
    Route::get('/dispatches/create', \App\Livewire\Dispatches\DispatchForm::class)->name('dispatches.create');
    Route::get('/dispatches/{order}/create', \App\Livewire\Dispatches\DispatchForm::class)->name('dispatches.create-for-order');
    Route::get('/dispatches/{dispatch}', function (\App\Models\Dispatch $dispatch) {
        // Check if user can view this dispatch
        if ($dispatch->order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $dispatch->load(['order.dealer', 'order.items.product']);
        return view('dispatches.show', compact('dispatch'));
    })->name('dispatches.show');
    Route::get('/dispatches/{dispatch}/edit', \App\Livewire\Dispatches\DispatchForm::class)->name('dispatches.edit');
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

    // Payments
    Route::get('/payments', \App\Livewire\Payments\PaymentsTable::class)->name('payments.index');
    Route::get('/payments/create', \App\Livewire\Payments\PaymentForm::class)->name('payments.create');
    Route::get('/payments/{order}/create', \App\Livewire\Payments\PaymentForm::class)->name('payments.create-for-order');
    Route::get('/payments/{payment}/edit', \App\Livewire\Payments\PaymentForm::class)->name('payments.edit');

    // Returns
    Route::get('/returns', \App\Livewire\Returns\ReturnsTable::class)->name('returns.index');
    Route::get('/returns/create', \App\Livewire\Returns\ReturnForm::class)->name('returns.create');
    Route::get('/returns/{order}/create', \App\Livewire\Returns\ReturnForm::class)->name('returns.create-for-order');
    Route::get('/returns/{return}/edit', \App\Livewire\Returns\ReturnForm::class)->name('returns.edit');
    Route::get('/returns/{return}', function (\App\Models\OrderReturn $return) {
        if ($return->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $return->load(['order.dealer', 'items.orderItem.product', 'items.orderItem.productSize']);
        return view('returns.show', compact('return'));
    })->name('returns.show');

    // Reports
    Route::get('/reports', \App\Livewire\Reports\ReportsPage::class)->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Reports\ReportExportController::class, 'export'])->name('reports.export');

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', \App\Livewire\Users\UsersTable::class)->name('users.index');
        Route::get('/users/create', \App\Livewire\Users\UserForm::class)->name('users.create');
        Route::get('/users/{user}/edit', \App\Livewire\Users\UserForm::class)->name('users.edit');
        Route::get('/users/{user}/permissions', \App\Livewire\Users\UserPermissions::class)->name('users.permissions');
    });
});

require __DIR__.'/auth.php';
