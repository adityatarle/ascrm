<?php

namespace App\Livewire\Masters;

use App\Models\State;
use Livewire\Component;
use Livewire\WithPagination;

class StateMasterTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $state = State::where('fld_state_id', $id)->first();
        if ($state) {
            $state->update(['fld_isdeleted' => 1]);
            session()->flash('message', 'State deleted successfully.');
        }
    }

    public function render()
    {
        $query = State::where('fld_isdeleted', 0);

        if ($this->search) {
            $query->where('fld_name', 'like', '%' . $this->search . '%');
        }

        $states = $query->orderBy('fld_state_id', 'asc')->paginate($this->perPage);

        return view('livewire.masters.state-master-table', [
            'states' => $states,
        ]);
    }
}
