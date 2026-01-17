<?php

namespace App\Livewire\Masters;

use App\Models\Unit;
use Livewire\Component;

class UnitForm extends Component
{
    public $unitId;
    public $name;
    public $symbol;
    public $code;
    public $description;
    public $isActive = true;
    public $sortOrder = 0;

    public function mount($unit = null)
    {
        if ($unit) {
            if (is_object($unit)) {
                $unitModel = $unit;
            } else {
                $unitModel = Unit::findOrFail($unit);
            }

            $this->unitId = $unitModel->id;
            $this->name = $unitModel->name;
            $this->symbol = $unitModel->symbol;
            $this->code = $unitModel->code;
            $this->description = $unitModel->description;
            $this->isActive = $unitModel->is_active;
            $this->sortOrder = $unitModel->sort_order;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'code' => 'nullable|string|max:20|unique:units,code,' . $this->unitId,
            'description' => 'nullable|string',
            'isActive' => 'boolean',
            'sortOrder' => 'nullable|integer|min:0',
        ]);

        if ($this->unitId) {
            $unit = Unit::findOrFail($this->unitId);
            $unit->update([
                'name' => $this->name,
                'symbol' => $this->symbol,
                'code' => $this->code,
                'description' => $this->description,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
            ]);

            session()->flash('message', 'Unit updated successfully');
        } else {
            Unit::create([
                'name' => $this->name,
                'symbol' => $this->symbol,
                'code' => $this->code,
                'description' => $this->description,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
            ]);

            session()->flash('message', 'Unit created successfully');
        }

        return redirect()->route('units.index');
    }

    public function render()
    {
        return view('livewire.masters.unit-form');
    }
}
