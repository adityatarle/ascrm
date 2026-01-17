<?php

namespace App\Livewire\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartSidebar extends Component
{
    public $items = [];
    public $isOpen = false;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $dealer = Auth::guard('sanctum')->user();
        
        if ($dealer) {
            $this->items = Cart::where('dealer_id', $dealer->id)
                ->with('product')
                ->get()
                ->toArray();
        } else {
            $sessionId = session()->getId();
            $this->items = Cart::where('session_id', $sessionId)
                ->with('product')
                ->get()
                ->toArray();
        }
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function addItem($productId)
    {
        $product = Product::findOrFail($productId);
        $dealer = Auth::guard('sanctum')->user();
        $sessionId = session()->getId();

        $cartItem = Cart::where('product_id', $productId)
            ->where(function ($query) use ($dealer, $sessionId) {
                if ($dealer) {
                    $query->where('dealer_id', $dealer->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Cart::create([
                'dealer_id' => $dealer?->id,
                'session_id' => $dealer ? null : $sessionId,
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function removeItem($cartId)
    {
        Cart::findOrFail($cartId)->delete();
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($cartId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($cartId);
            return;
        }

        Cart::findOrFail($cartId)->update(['quantity' => $quantity]);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function getTotalProperty()
    {
        return collect($this->items)->sum(function ($item) {
            $rate = $item['product']['base_price'] ?? 0;
            return $rate * $item['quantity'];
        });
    }

    public function render()
    {
        return view('livewire.cart.cart-sidebar');
    }
}

