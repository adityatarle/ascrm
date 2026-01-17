<?php

namespace App\Livewire\Dealers;

use App\Models\City;
use App\Models\Dealer;
use App\Models\State;
use App\Models\Zone;
use Livewire\Component;

class DealerForm extends Component
{
    public $dealerId;
    public $name;
    public $mobile;
    public $email;
    public $gstin;
    public $address;
    public $stateId;
    public $cityId;
    public $pincode;
    public $zoneId;
    public $isActive = true;

    public $states = [];
    public $cities = [];
    public $zones = [];

    public function mount($dealer = null)
    {
        $this->states = State::orderBy('name', 'asc')->get();

        if ($dealer) {
            // Handle route model binding
            if (is_object($dealer)) {
                $dealerModel = $dealer;
            } else {
                $dealerModel = Dealer::findOrFail($dealer);
            }

            $this->dealerId = $dealerModel->id;
            $this->name = $dealerModel->name;
            $this->mobile = $dealerModel->mobile;
            $this->email = $dealerModel->email;
            $this->gstin = $dealerModel->gstin;
            $this->address = $dealerModel->address;
            $this->stateId = $dealerModel->state_id;
            $this->cityId = $dealerModel->city_id;
            $this->pincode = $dealerModel->pincode;
            $this->zoneId = $dealerModel->zone_id;
            $this->isActive = $dealerModel->is_active;

            if ($this->stateId) {
                $this->loadCities();
                $this->loadZones();
            }
        }
    }

    public function updatedStateId()
    {
        $this->cityId = null;
        $this->zoneId = null;
        $this->loadCities();
        $this->loadZones();
    }

    public function updatedCityId()
    {
        $city = City::find($this->cityId);
        if ($city && $city->zone_id) {
            $this->zoneId = $city->zone_id;
        }
    }

    public function loadCities()
    {
        if ($this->stateId) {
            $this->cities = City::where('state_id', $this->stateId)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $this->cities = [];
        }
    }

    public function loadZones()
    {
        if ($this->stateId) {
            $this->zones = Zone::where('state_id', $this->stateId)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $this->zones = [];
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:dealers,mobile,' . $this->dealerId,
            'email' => 'nullable|email|unique:dealers,email,' . $this->dealerId,
            'gstin' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'stateId' => 'required|exists:states,id',
            'cityId' => 'required|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'zoneId' => 'nullable|exists:zones,id',
            'isActive' => 'boolean',
        ]);

        $city = City::find($this->cityId);
        if ($city && $city->zone_id && !$this->zoneId) {
            $this->zoneId = $city->zone_id;
        }

        if ($this->dealerId) {
            $dealer = Dealer::findOrFail($this->dealerId);
            $dealer->update([
                'name' => $this->name,
                'mobile' => $this->mobile,
                'email' => $this->email,
                'gstin' => $this->gstin,
                'address' => $this->address,
                'state_id' => $this->stateId,
                'city_id' => $this->cityId,
                'pincode' => $this->pincode,
                'zone_id' => $this->zoneId,
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Dealer updated successfully');
        } else {
            Dealer::create([
                'name' => $this->name,
                'mobile' => $this->mobile,
                'email' => $this->email,
                'gstin' => $this->gstin,
                'address' => $this->address,
                'state_id' => $this->stateId,
                'city_id' => $this->cityId,
                'pincode' => $this->pincode,
                'zone_id' => $this->zoneId,
                'password' => bcrypt('password'), // Default password, should be changed
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Dealer created successfully');
        }

        return redirect()->route('dealers.index');
    }

    public function render()
    {
        return view('livewire.dealers.dealer-form');
    }
}

