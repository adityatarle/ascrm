<?php

namespace App\Livewire\Masters;

use App\Models\DiscountSlab;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountSlabsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showInactive = false;
    public $organizationFilter = null;

    protected $queryString = ['search'];

    public function mount()
    {
        $this->organizationFilter = Auth::user()->organization_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($slabId)
    {
        $slab = DiscountSlab::findOrFail($slabId);
        $slab->delete();
        session()->flash('message', 'Discount slab deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $organizations = Organization::orderBy('name')->get();

        $query = DiscountSlab::query();

        // Filter by organization if user is not super admin
        if ($this->organizationFilter) {
            $query->where(function($q) {
                $q->where('organization_id', $this->organizationFilter)
                  ->orWhereNull('organization_id'); // Include global slabs
            });
        }

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $slabs = $query->with('organization')
            ->orderBy('min_amount', 'asc')
            ->paginate($this->perPage);

        return view('livewire.masters.discount-slabs-table', [
            'slabs' => $slabs,
            'organizations' => $organizations,
        ]);
    }
}
