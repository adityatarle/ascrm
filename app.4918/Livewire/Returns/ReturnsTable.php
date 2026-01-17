<?php

namespace App\Livewire\Returns;

use App\Models\OrderReturn;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnsTable extends Component
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

    public function delete($returnId)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete returns.');
            return;
        }

        $return = OrderReturn::where('organization_id', $user->organization_id)
            ->findOrFail($returnId);

        $return->delete();
        session()->flash('message', 'Return deleted successfully');
        $this->resetPage();
    }

    public function updateStatus($returnId, $status)
    {
        $user = Auth::user();
        
        $return = OrderReturn::where('organization_id', $user->organization_id)
            ->findOrFail($returnId);

        $return->update(['status' => $status]);
        session()->flash('message', 'Return status updated successfully');
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = OrderReturn::where('organization_id', $user->organization_id)
            ->with(['order.dealer', 'dealer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('return_number', 'like', '%' . $this->search . '%')
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

        $returns = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.returns.returns-table', [
            'returns' => $returns,
        ]);
    }
}
