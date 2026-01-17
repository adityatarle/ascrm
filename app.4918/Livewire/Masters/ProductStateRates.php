<?php

namespace App\Livewire\Masters;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductStateRate;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductStateRates extends Component
{
    public $productId;
    public $product;
    public $selectedSizeId = null; // null for base product, or product_size_id
    
    public $states = [];
    public $productSizes = [];
    public $rates = []; // [state_id => rate]

    public function mount($product = null)
    {
        if (!$product) {
            return redirect()->route('products.index');
        }

        if (is_object($product)) {
            $this->product = $product;
        } else {
            $this->product = Product::where('organization_id', Auth::user()->organization_id)
                ->with(['sizes', 'stateRates'])
                ->findOrFail($product);
        }

        $this->productId = $this->product->id;
        $this->states = State::orderBy('name')->get();
        $this->productSizes = $this->product->sizes()->with('unit')->where('is_active', true)->get();
        
        $this->loadRates();
    }

    public function updatedSelectedSizeId()
    {
        $this->loadRates();
    }

    public function loadRates()
    {
        $this->rates = [];
        
        // Load existing rates for selected product/size
        $query = ProductStateRate::where('product_id', $this->productId);
        
        if ($this->selectedSizeId) {
            $query->where('product_size_id', $this->selectedSizeId);
        } else {
            $query->whereNull('product_size_id'); // Base product rates
        }
        
        $existingRates = $query->get();
        
        foreach ($existingRates as $rate) {
            $this->rates[$rate->state_id] = $rate->rate;
        }
    }

    public function saveRates()
    {
        $validated = $this->validate([
            'rates.*' => 'nullable|numeric|min:0',
        ]);

        // Delete existing rates for this product/size combination
        ProductStateRate::where('product_id', $this->productId)
            ->where(function($query) {
                if ($this->selectedSizeId) {
                    $query->where('product_size_id', $this->selectedSizeId);
                } else {
                    $query->whereNull('product_size_id');
                }
            })
            ->delete();

        // Create new rates
        foreach ($this->rates as $stateId => $rate) {
            if ($rate && $rate > 0) {
                ProductStateRate::create([
                    'product_id' => $this->productId,
                    'product_size_id' => $this->selectedSizeId,
                    'state_id' => $stateId,
                    'rate' => $rate,
                ]);
            }
        }

        session()->flash('message', 'State rates saved successfully');
        $this->loadRates();
    }

    public function render()
    {
        return view('livewire.masters.product-state-rates');
    }
}
