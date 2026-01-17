<?php

namespace App\Livewire\Dispatches;

use App\Models\Dispatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DispatchesTable extends Component
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

    public function delete($dispatchId)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete dispatches.');
            return;
        }

        $dispatch = Dispatch::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->findOrFail($dispatchId);

        // Soft delete
        $dispatch->delete();

        session()->flash('message', 'Dispatch deleted successfully');
        $this->resetPage();
    }

    public function updateStatus($dispatchId, $status)
    {
        $user = Auth::user();
        
        $dispatch = Dispatch::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->findOrFail($dispatchId);

        $dispatch->update(['status' => $status]);
        
        // Update order status if dispatch is delivered
        if ($status === Dispatch::STATUS_DELIVERED) {
            $dispatch->order->update(['status' => \App\Models\Order::STATUS_DELIVERED]);
        }
        
        session()->flash('message', 'Dispatch status updated successfully');
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Dispatch::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->with(['order.dealer', 'order.organization']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('dispatch_number', 'like', '%' . $this->search . '%')
                    ->orWhere('lr_number', 'like', '%' . $this->search . '%')
                    ->orWhere('vehicle_number', 'like', '%' . $this->search . '%')
                    ->orWhere('transporter_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('order', function ($q) {
                        $q->where('order_number', 'like', '%' . $this->search . '%')
                            ->orWhereHas('dealer', function ($q) {
                                $q->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $dispatches = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.dispatches.dispatches-table', [
            'dispatches' => $dispatches,
        ]);
    }
}
