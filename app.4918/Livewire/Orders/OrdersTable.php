<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 15;

    protected $queryString = ['search', 'statusFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function delete($orderId)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete orders.');
            return;
        }

        $order = Order::where('organization_id', $user->organization_id)
            ->findOrFail($orderId);

        // Only allow deletion of pending orders
        if ($order->status !== Order::STATUS_PENDING) {
            session()->flash('error', 'Only pending orders can be deleted.');
            return;
        }

        // Soft delete
        $order->delete();

        session()->flash('message', 'Order deleted successfully');
        $this->resetPage();
    }

    public function updateStatus($orderId, $status)
    {
        $user = Auth::user();
        
        $order = Order::where('organization_id', $user->organization_id)
            ->findOrFail($orderId);

        $order->update(['status' => $status]);
        session()->flash('message', 'Order status updated successfully');
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Order::where('organization_id', $user->organization_id)
            ->with(['dealer', 'organization']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('dealer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('mobile', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.orders.orders-table', [
            'orders' => $orders,
        ]);
    }
}
