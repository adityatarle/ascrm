<?php

namespace App\Livewire\Masters;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductForm extends Component
{
    public $productId;
    public $name;
    public $code;
    public $description;
    public $unit;
    public $basePrice;
    public $isActive = true;

    public function mount($product = null)
    {
        if ($product) {
            // Handle route model binding
            if (is_object($product)) {
                $productModel = $product;
            } else {
                $productModel = Product::where('organization_id', Auth::user()->organization_id)
                    ->findOrFail($product);
            }
            
            $this->productId = $productModel->id;
            $this->name = $productModel->name;
            $this->code = $productModel->code;
            $this->description = $productModel->description;
            $this->unit = $productModel->unit;
            $this->basePrice = $productModel->base_price;
            $this->isActive = $productModel->is_active;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:20',
            'basePrice' => 'required|numeric|min:0',
            'isActive' => 'boolean',
        ]);

        $user = Auth::user();

        if ($this->productId) {
            $product = Product::where('organization_id', $user->organization_id)
                ->findOrFail($this->productId);
            
            $product->update([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'unit' => $this->unit,
                'base_price' => $this->basePrice,
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Product updated successfully');
        } else {
            Product::create([
                'organization_id' => $user->organization_id,
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'unit' => $this->unit,
                'base_price' => $this->basePrice,
                'is_active' => $this->isActive,
            ]);

            session()->flash('message', 'Product created successfully');
        }

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.masters.product-form');
    }
}

