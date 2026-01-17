<?php

namespace App\Livewire\Masters;

use App\Models\Product;
use App\Models\ProductStateRate;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StateWiseProductRates extends Component
{
    public $selectedStateId = null;
    public $selectedState = null;
    public $states = [];
    public $products = [];
    public $rates = []; // [product_id => rate]
    public $productSizes = []; // [product_id => [size_id => rate]]

    public function mount()
    {
        $this->states = State::orderBy('name')->get();
        $this->loadProducts();
    }

    public function updatedSelectedStateId()
    {
        if ($this->selectedStateId) {
            $this->selectedState = State::find($this->selectedStateId);
            $this->loadRates();
        } else {
            $this->selectedState = null;
            $this->rates = [];
            $this->productSizes = [];
        }
    }

    public function loadProducts()
    {
        $this->products = Product::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)
            ->with(['unit', 'sizes' => function($query) {
                $query->where('is_active', true)->with('unit');
            }])
            ->orderBy('name')
            ->get();
    }

    public function loadRates()
    {
        if (!$this->selectedStateId) {
            return;
        }

        $this->rates = [];
        $this->productSizes = [];

        // Load existing rates for base products (without size)
        $baseRates = ProductStateRate::where('state_id', $this->selectedStateId)
            ->whereNull('product_size_id')
            ->get();

        foreach ($baseRates as $rate) {
            $this->rates[$rate->product_id] = $rate->rate;
        }

        // Load existing rates for product sizes
        $sizeRates = ProductStateRate::where('state_id', $this->selectedStateId)
            ->whereNotNull('product_size_id')
            ->get();

        foreach ($sizeRates as $rate) {
            if (!isset($this->productSizes[$rate->product_id])) {
                $this->productSizes[$rate->product_id] = [];
            }
            $this->productSizes[$rate->product_id][$rate->product_size_id] = $rate->rate;
        }
    }

    public function saveRates()
    {
        if (!$this->selectedStateId) {
            session()->flash('error', 'Please select a state first');
            return;
        }

        $this->validate([
            'rates.*' => 'nullable|numeric|min:0',
            'productSizes.*.*' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () {
            // Delete existing rates for this state
            ProductStateRate::where('state_id', $this->selectedStateId)->delete();

            // Save base product rates
            foreach ($this->rates as $productId => $rate) {
                if ($rate && $rate > 0) {
                    ProductStateRate::create([
                        'product_id' => $productId,
                        'product_size_id' => null,
                        'state_id' => $this->selectedStateId,
                        'rate' => $rate,
                    ]);
                }
            }

            // Save product size rates
            foreach ($this->productSizes as $productId => $sizes) {
                foreach ($sizes as $sizeId => $rate) {
                    if ($rate && $rate > 0) {
                        ProductStateRate::create([
                            'product_id' => $productId,
                            'product_size_id' => $sizeId,
                            'state_id' => $this->selectedStateId,
                            'rate' => $rate,
                        ]);
                    }
                }
            }
        });

        session()->flash('message', 'State-wise product rates saved successfully');
        $this->loadRates();
    }

    public function render()
    {
        return view('livewire.masters.state-wise-product-rates');
    }
}

