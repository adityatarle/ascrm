<?php

namespace App\Livewire\Masters;

use App\Models\District;
use App\Models\State;
use Livewire\Component;

class DistrictMasterForm extends Component
{
    public $districtId;
    public $name;
    public $stateId;
    public $countryId = 1; // Default to India

    protected $rules = [
        'name' => 'required|string|max:50',
        'stateId' => 'required|exists:tbl_state_master,fld_state_id',
        'countryId' => 'required|exists:tbl_country_master,fld_country_id',
    ];

    public function mount($district = null)
    {
        if ($district) {
            // Handle both route model binding and direct ID
            $districtModel = is_numeric($district) 
                ? District::where('fld_dist_id', $district)->first() 
                : (is_object($district) ? $district : District::where('fld_dist_id', $district)->first());
            
            if ($districtModel) {
                $this->districtId = $districtModel->fld_dist_id;
                $this->name = $districtModel->fld_dist_name;
                $this->stateId = $districtModel->fld_state_id;
                $this->countryId = $districtModel->fld_country_id;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'fld_dist_name' => $this->name,
            'fld_state_id' => $this->stateId,
            'fld_country_id' => $this->countryId,
            'fld_created_by' => auth()->user()->id,
            'fld_created_date' => now(),
            'fld_updated_by' => auth()->user()->id,
            'fld_updated_date' => now(),
            'fld_isdeleted' => 0,
            'fld_system_date' => time(),
        ];

        if ($this->districtId) {
            $district = District::find($this->districtId);
            $district->update($data);
            session()->flash('message', 'District updated successfully.');
        } else {
            District::create($data);
            session()->flash('message', 'District created successfully.');
        }

        return redirect()->route('districts.index');
    }

    public function render()
    {
        $states = State::where('fld_isdeleted', 0)->orderBy('fld_name')->get();
        return view('livewire.masters.district-master-form', [
            'states' => $states,
        ]);
    }
}
