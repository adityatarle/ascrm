<?php

namespace App\Livewire\Masters;

use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showInactive = false;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($unitId)
    {
        $unit = Unit::findOrFail($unitId);
        
        // Check if unit is being used
        if ($unit->products()->count() > 0 || $unit->productSizes()->count() > 0) {
            session()->flash('error', 'Cannot delete unit. It is being used by products.');
            return;
        }

        $unit->delete();
        session()->flash('message', 'Unit deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $query = Unit::query();

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('symbol', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $units = $query->orderBy('sort_order')->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.masters.units-table', [
            'units' => $units,
        ]);
    }
}
