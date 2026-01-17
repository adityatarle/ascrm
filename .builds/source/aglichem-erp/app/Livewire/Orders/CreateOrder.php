<?php

namespace App\Livewire\Orders;

use App\Models\Dealer;
use App\Models\Product;
use App\Models\ProductStateRate;
use App\Models\State;
use Livewire\Component;

class CreateOrder extends Component
{
    public $dealerId;
    public $selectedDealer;
    public $cartItems = [];
    public $products = [];
    public $selectedStateId;

    public function mount()
    {
        $user = auth()->user();
        $this->products = Product::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function selectDealer($dealerId)
    {
        $this->dealerId = $dealerId;
        $this->selectedDealer = Dealer::with(['state', 'city', 'zone'])->find($dealerId);
        $this->selectedStateId = $this->selectedDealer->state_id ?? null;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return;
        }

        $existingIndex = collect($this->cartItems)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existingIndex !== false) {
            $this->cartItems[$existingIndex]['quantity']++;
        } else {
            $rate = $this->getProductRate($product, $this->selectedStateId);
            
            $this->cartItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'rate' => $rate,
                'subtotal' => $rate,
            ];
        }

        $this->updateCartTotals();
    }

    public function removeFromCart($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
        $this->updateCartTotals();
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        $this->cartItems[$index]['quantity'] = $quantity;
        $this->cartItems[$index]['subtotal'] = $this->cartItems[$index]['rate'] * $quantity;
        $this->updateCartTotals();
    }

    public function updateCartTotals()
    {
        // Recalculate rates if state changed
        if ($this->selectedStateId) {
            foreach ($this->cartItems as $index => $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $newRate = $this->getProductRate($product, $this->selectedStateId);
                    $this->cartItems[$index]['rate'] = $newRate;
                    $this->cartItems[$index]['subtotal'] = $newRate * $this->cartItems[$index]['quantity'];
                }
            }
        }
    }

    protected function getProductRate(Product $product, ?int $stateId): float
    {
        if ($stateId) {
            $stateRate = ProductStateRate::where('product_id', $product->id)
                ->where('state_id', $stateId)
                ->first();

            if ($stateRate) {
                return $stateRate->rate;
            }
        }

        return $product->base_price;
    }

    public function getSubtotalProperty()
    {
        return collect($this->cartItems)->sum('subtotal');
    }

    public function render()
    {
        $dealers = Dealer::where('is_active', true)
            ->with(['state', 'city', 'zone'])
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.orders.create-order', [
            'dealers' => $dealers,
        ]);
    }
}

