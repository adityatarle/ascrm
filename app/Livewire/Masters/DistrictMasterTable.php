<?php

namespace App\Livewire\Masters;

use App\Models\District;
use App\Models\State;
use Livewire\Component;
use Livewire\WithPagination;

class DistrictMasterTable extends Component
{
    use WithPagination;

    public $search = '';
    public $stateFilter = '';
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStateFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $district = District::where('fld_dist_id', $id)->first();
        if ($district) {
            $district->update(['fld_isdeleted' => 1]);
            session()->flash('message', 'District deleted successfully.');
        }
    }

    public function render()
    {
        $query = District::with('state')->where('fld_isdeleted', 0);

        if ($this->search) {
            $query->where('fld_dist_name', 'like', '%' . $this->search . '%');
        }

        if ($this->stateFilter) {
            $query->where('fld_state_id', $this->stateFilter);
        }

        $districts = $query->orderBy('fld_dist_id', 'asc')->paginate($this->perPage);
        $states = State::where('fld_isdeleted', 0)->orderBy('fld_name')->get();

        return view('livewire.masters.district-master-table', [
            'districts' => $districts,
            'states' => $states,
        ]);
    }
}
