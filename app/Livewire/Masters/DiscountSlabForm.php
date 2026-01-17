<?php

namespace App\Livewire\Masters;

use App\Models\DiscountSlab;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DiscountSlabForm extends Component
{
    public $slabId;
    public $organizationId;
    public $name;
    public $description;
    public $minAmount;
    public $maxAmount;
    public $discountPercent;
    public $isActive = true;

    public $organizations = [];

    public function mount($slab = null)
    {
        $this->organizations = Organization::orderBy('name')->get();
        $this->organizationId = Auth::user()->organization_id;

        if ($slab) {
            if (is_object($slab)) {
                $slabModel = $slab;
            } else {
                $slabModel = DiscountSlab::findOrFail($slab);
            }

            $this->slabId = $slabModel->id;
            $this->organizationId = $slabModel->organization_id;
            $this->name = $slabModel->name;
            $this->description = $slabModel->description;
            $this->minAmount = $slabModel->min_amount;
            $this->maxAmount = $slabModel->max_amount;
            $this->discountPercent = $slabModel->discount_percent;
            $this->isActive = $slabModel->is_active;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'organizationId' => 'nullable|exists:organizations,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'minAmount' => 'required|numeric|min:0',
            'maxAmount' => 'nullable|numeric|min:0|gt:minAmount',
            'discountPercent' => 'required|numeric|min:0|max:100',
            'isActive' => 'boolean',
        ], [
            'maxAmount.gt' => 'Maximum amount must be greater than minimum amount.',
        ]);

        if ($this->slabId) {
            $slab = DiscountSlab::findOrFail($this->slabId);
            $slab->update([
                'organization_id' => $this->organizationId,
                'name' => $this->name,
                'description' => $this->description,
                'min_amount' => $this->minAmount,
                'max_amount' => $this->maxAmount,
                'discount_percent' => $this->discountPercent,
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Discount slab updated successfully');
        } else {
            DiscountSlab::create([
                'organization_id' => $this->organizationId,
                'name' => $this->name,
                'description' => $this->description,
                'min_amount' => $this->minAmount,
                'max_amount' => $this->maxAmount,
                'discount_percent' => $this->discountPercent,
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Discount slab created successfully');
        }

        return redirect()->route('discount-slabs.index');
    }

    public function render()
    {
        return view('livewire.masters.discount-slab-form');
    }
}
