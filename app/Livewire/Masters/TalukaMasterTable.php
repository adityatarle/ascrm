<?php

namespace App\Livewire\Masters;

use App\Models\District;
use App\Models\State;
use App\Models\Taluka;
use Livewire\Component;
use Livewire\WithPagination;

class TalukaMasterTable extends Component
{
    use WithPagination;

    public $search = '';
    public $stateFilter = '';
    public $districtFilter = '';
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStateFilter()
    {
        $this->districtFilter = '';
        $this->resetPage();
    }

    public function updatingDistrictFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $taluka = Taluka::where('fld_taluka_id', $id)->first();
        if ($taluka) {
            $taluka->update(['fld_isdeleted' => 1]);
            session()->flash('message', 'Taluka deleted successfully.');
        }
    }

    public function render()
    {
        $query = Taluka::with(['state', 'district'])->where('fld_isdeleted', 0);

        if ($this->search) {
            $query->where('fld_name', 'like', '%' . $this->search . '%');
        }

        if ($this->districtFilter) {
            $query->where('fld_disc_id', $this->districtFilter);
        } elseif ($this->stateFilter) {
            $query->where('fld_state_id', $this->stateFilter);
        }

        $talukas = $query->orderBy('fld_taluka_id', 'asc')->paginate($this->perPage);
        $states = State::where('fld_isdeleted', 0)->orderBy('fld_name')->get();
        $districts = $this->stateFilter 
            ? District::where('fld_state_id', $this->stateFilter)->where('fld_isdeleted', 0)->orderBy('fld_dist_name')->get()
            : collect();

        return view('livewire.masters.taluka-master-table', [
            'talukas' => $talukas,
            'states' => $states,
            'districts' => $districts,
        ]);
    }
}
