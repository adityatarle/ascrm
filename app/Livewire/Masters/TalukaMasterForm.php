<?php

namespace App\Livewire\Masters;

use App\Models\District;
use App\Models\State;
use App\Models\Taluka;
use Livewire\Component;

class TalukaMasterForm extends Component
{
    public $talukaId;
    public $name;
    public $code;
    public $stateId;
    public $districtId;
    public $countryId = 1; // Default to India
    public $sequence = 0;

    protected $rules = [
        'name' => 'required|string|max:200',
        'code' => 'nullable|string|max:5',
        'stateId' => 'required|exists:tbl_state_master,fld_state_id',
        'districtId' => 'required|exists:tbl_dist_master,fld_dist_id',
        'countryId' => 'required|exists:tbl_country_master,fld_country_id',
        'sequence' => 'nullable|integer',
    ];

    public function mount($taluka = null)
    {
        if ($taluka) {
            // Handle both route model binding and direct ID
            $talukaModel = is_numeric($taluka) 
                ? Taluka::where('fld_taluka_id', $taluka)->first() 
                : (is_object($taluka) ? $taluka : Taluka::where('fld_taluka_id', $taluka)->first());
            
            if ($talukaModel) {
                $this->talukaId = $talukaModel->fld_taluka_id;
                $this->name = $talukaModel->fld_name;
                $this->code = $talukaModel->fld_code;
                $this->stateId = $talukaModel->fld_state_id;
                $this->districtId = $talukaModel->fld_disc_id;
                $this->countryId = $talukaModel->fld_country_id;
                $this->sequence = $talukaModel->fld_sequence;
            }
        }
    }

    public function updatedStateId()
    {
        $this->districtId = null;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'fld_name' => $this->name,
            'fld_code' => $this->code,
            'fld_state_id' => $this->stateId,
            'fld_disc_id' => $this->districtId,
            'fld_country_id' => $this->countryId,
            'fld_sequence' => $this->sequence ?? 0,
            'fld_created_by' => auth()->user()->id,
            'fld_created_date' => now(),
            'fld_updated_by' => auth()->user()->id,
            'fld_updated_date' => now(),
            'fld_isdeleted' => 0,
            'fld_system_date' => time(),
        ];

        if ($this->talukaId) {
            $taluka = Taluka::find($this->talukaId);
            $taluka->update($data);
            session()->flash('message', 'Taluka updated successfully.');
        } else {
            Taluka::create($data);
            session()->flash('message', 'Taluka created successfully.');
        }

        return redirect()->route('talukas.index');
    }

    public function render()
    {
        $states = State::where('fld_isdeleted', 0)->orderBy('fld_name')->get();
        $districts = $this->stateId 
            ? District::where('fld_state_id', $this->stateId)->where('fld_isdeleted', 0)->orderBy('fld_dist_name')->get()
            : collect();

        return view('livewire.masters.taluka-master-form', [
            'states' => $states,
            'districts' => $districts,
        ]);
    }
}
