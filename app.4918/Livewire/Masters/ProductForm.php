<?php

namespace App\Livewire\Masters;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductForm extends Component
{
    public $productId;
    public $name;
    public $code;
    public $description;
    public $unit;
    public $unitId;
    public $basePrice;
    public $isActive = true;
    
    // Product sizes
    public $sizes = [];
    public $units = [];

    public function mount($product = null)
    {
        $this->units = Unit::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        
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
            $this->unitId = $productModel->unit_id;
            $this->basePrice = $productModel->base_price;
            $this->isActive = $productModel->is_active;
            
            // Load product sizes
            $this->sizes = $productModel->sizes()->with('unit')->get()->map(function($size) {
                return [
                    'id' => $size->id,
                    'unit_id' => $size->unit_id,
                    'size_value' => $size->size_value,
                    'size_label' => $size->size_label,
                    'base_price' => $size->base_price,
                    'is_active' => $size->is_active,
                ];
            })->toArray();
        }
    }
    
    public function addSize()
    {
        $this->sizes[] = [
            'id' => null,
            'unit_id' => null,
            'size_value' => '',
            'size_label' => '',
            'base_price' => 0,
            'is_active' => true,
        ];
    }
    
    public function removeSize($index)
    {
        unset($this->sizes[$index]);
        $this->sizes = array_values($this->sizes);
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:20',
            'unitId' => 'nullable|exists:units,id',
            'basePrice' => 'required|numeric|min:0',
            'isActive' => 'boolean',
            'sizes.*.unit_id' => 'nullable|exists:units,id',
            'sizes.*.size_value' => 'nullable|numeric|min:0',
            'sizes.*.size_label' => 'nullable|string|max:255',
            'sizes.*.base_price' => 'nullable|numeric|min:0',
            'sizes.*.is_active' => 'boolean',
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
                'unit_id' => $this->unitId,
                'base_price' => $this->basePrice,
                'is_active' => $this->isActive,
            ]);
            
            // Update product sizes
            $this->updateProductSizes($product);

            session()->flash('message', 'Product updated successfully');
        } else {
            $product = Product::create([
                'organization_id' => $user->organization_id,
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'unit' => $this->unit,
                'unit_id' => $this->unitId,
                'base_price' => $this->basePrice,
                'is_active' => $this->isActive,
            ]);
            
            // Create product sizes
            $this->updateProductSizes($product);

            session()->flash('message', 'Product created successfully');
        }

        return redirect()->route('products.index');
    }

    protected function updateProductSizes($product)
    {
        // Get existing size IDs
        $existingSizeIds = collect($this->sizes)->pluck('id')->filter()->toArray();
        
        // Delete sizes that are no longer in the list
        $product->sizes()->whereNotIn('id', $existingSizeIds)->delete();
        
        // Update or create sizes
        foreach ($this->sizes as $sizeData) {
            if (empty($sizeData['size_value']) && empty($sizeData['size_label'])) {
                continue; // Skip empty sizes
            }
            
            $sizeData['product_id'] = $product->id;
            
            if ($sizeData['id']) {
                // Update existing size
                ProductSize::where('id', $sizeData['id'])
                    ->where('product_id', $product->id)
                    ->update([
                        'unit_id' => $sizeData['unit_id'],
                        'size_value' => $sizeData['size_value'] ?? 0,
                        'size_label' => $sizeData['size_label'],
                        'base_price' => $sizeData['base_price'] ?? 0,
                        'is_active' => $sizeData['is_active'] ?? true,
                    ]);
            } else {
                // Create new size
                ProductSize::create([
                    'product_id' => $product->id,
                    'unit_id' => $sizeData['unit_id'],
                    'size_value' => $sizeData['size_value'] ?? 0,
                    'size_label' => $sizeData['size_label'],
                    'base_price' => $sizeData['base_price'] ?? 0,
                    'is_active' => $sizeData['is_active'] ?? true,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.masters.product-form');
    }
}

