<?php

namespace App\Livewire\Masters;

use App\Models\State;
use Livewire\Component;

class StateMasterForm extends Component
{
    public $stateId;
    public $name;
    public $countryId = 1; // Default to India

    protected $rules = [
        'name' => 'required|string|max:50',
        'countryId' => 'required|exists:tbl_country_master,fld_country_id',
    ];

    public function mount($state = null)
    {
        if ($state) {
            // Handle both route model binding and direct ID
            $stateModel = is_numeric($state) 
                ? State::where('fld_state_id', $state)->first() 
                : (is_object($state) ? $state : State::where('fld_state_id', $state)->first());
            
            if ($stateModel) {
                $this->stateId = $stateModel->fld_state_id;
                $this->name = $stateModel->fld_name;
                $this->countryId = $stateModel->fld_country_id;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'fld_name' => $this->name,
            'fld_country_id' => $this->countryId,
            'fld_created_by' => auth()->user()->id,
            'fld_created_date' => now(),
            'fld_updated_by' => auth()->user()->id,
            'fld_updated_date' => now(),
            'fld_isdeleted' => 0,
            'fld_system_date' => time(),
        ];

        if ($this->stateId) {
            $state = State::find($this->stateId);
            $state->update($data);
            session()->flash('message', 'State updated successfully.');
        } else {
            State::create($data);
            session()->flash('message', 'State created successfully.');
        }

        return redirect()->route('states.index');
    }

    public function render()
    {
        return view('livewire.masters.state-master-form');
    }
}
