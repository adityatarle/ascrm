<?php

namespace App\Livewire\Masters;

use App\Models\Crop;
use Livewire\Component;
use Livewire\WithPagination;

class CropsTable extends Component
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

    public function delete($cropId)
    {
        $crop = Crop::findOrFail($cropId);
        $crop->delete();
        session()->flash('message', 'Crop deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $query = Crop::query();

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('unique_id', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $crops = $query->withCount('products')
            ->orderBy('sort_order')->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.masters.crops-table', [
            'crops' => $crops,
        ]);
    }
}
