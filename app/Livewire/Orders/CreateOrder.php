<?php

namespace App\Livewire\Orders;

use App\Models\Dealer;
use App\Models\DiscountSlab;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductStateRate;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateOrder extends Component
{
    public $dealerId;
    public $selectedDealer;
    public $cartItems = [];
    public $products = [];
    public $selectedStateId;
    public $selectedProductId;
    public $selectedSizeId;
    public $quantity = 1;

    public function mount()
    {
        $user = auth()->user();
        $this->products = Product::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->with(['sizes.unit', 'unit'])
            ->orderBy('name', 'asc')
            ->get();
    }

    public function updatedDealerId()
    {
        if ($this->dealerId) {
            $this->selectedDealer = Dealer::with(['state', 'city', 'zone'])->find($this->dealerId);
            $this->selectedStateId = $this->selectedDealer->state_id ?? null;
            // Recalculate rates when dealer changes
            $this->updateCartTotals();
        } else {
            $this->selectedDealer = null;
            $this->selectedStateId = null;
        }
    }

    public function selectProduct($productId)
    {
        $this->selectedProductId = $productId;
        $this->selectedSizeId = null;
        $this->quantity = 1;
    }

    public function addToCart()
    {
        if (!$this->dealerId) {
            session()->flash('error', 'Please select a dealer first');
            return;
        }

        if (!$this->selectedProductId) {
            session()->flash('error', 'Please select a product');
            return;
        }

        $product = Product::with(['sizes', 'unit'])->find($this->selectedProductId);
        
        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        // Check if same product+size combination exists
        $existingIndex = collect($this->cartItems)->search(function ($item) {
            return $item['product_id'] == $this->selectedProductId 
                && $item['product_size_id'] == $this->selectedSizeId;
        });

        $rate = $this->getProductRate($product, $this->selectedStateId, $this->selectedSizeId);
        $sizeLabel = $this->getSizeLabel($product, $this->selectedSizeId);

        if ($existingIndex !== false) {
            $this->cartItems[$existingIndex]['quantity'] += $this->quantity;
            $this->cartItems[$existingIndex]['subtotal'] = $this->cartItems[$existingIndex]['rate'] * $this->cartItems[$existingIndex]['quantity'];
        } else {
            $this->cartItems[] = [
                'product_id' => $product->id,
                'product_size_id' => $this->selectedSizeId,
                'product_name' => $product->name,
                'size_label' => $sizeLabel,
                'quantity' => $this->quantity,
                'rate' => $rate,
                'subtotal' => $rate * $this->quantity,
            ];
        }

        // Reset selection
        $this->selectedProductId = null;
        $this->selectedSizeId = null;
        $this->quantity = 1;

        session()->flash('message', 'Product added to cart');
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
                    $newRate = $this->getProductRate($product, $this->selectedStateId, $item['product_size_id'] ?? null);
                    $this->cartItems[$index]['rate'] = $newRate;
                    $this->cartItems[$index]['subtotal'] = $newRate * $this->cartItems[$index]['quantity'];
                }
            }
        }
    }

    protected function getProductRate(Product $product, ?int $stateId, ?int $sizeId = null): float
    {
        // First try to get size-specific rate
        if ($sizeId && $stateId) {
            $stateRate = ProductStateRate::where('product_id', $product->id)
                ->where('product_size_id', $sizeId)
                ->where('state_id', $stateId)
                ->first();

            if ($stateRate) {
                return $stateRate->rate;
            }
        }

        // Try base product rate for state
        if ($stateId) {
            $stateRate = ProductStateRate::where('product_id', $product->id)
                ->whereNull('product_size_id')
                ->where('state_id', $stateId)
                ->first();

            if ($stateRate) {
                return $stateRate->rate;
            }
        }

        // If size selected, use size base price
        if ($sizeId) {
            $size = ProductSize::find($sizeId);
            if ($size) {
                return $size->base_price;
            }
        }

        // Fallback to product base price
        return $product->base_price;
    }

    protected function getSizeLabel(Product $product, ?int $sizeId): string
    {
        if ($sizeId) {
            $size = ProductSize::with('unit')->find($sizeId);
            if ($size) {
                if ($size->size_label) {
                    return $size->size_label;
                }
                if ($size->unit && $size->size_value) {
                    return $size->size_value . $size->unit->symbol;
                }
                return (string) $size->size_value;
            }
        }
        return '';
    }

    public function getSubtotalProperty()
    {
        return collect($this->cartItems)->sum('subtotal');
    }

    public function getDiscountAmountProperty()
    {
        $subtotal = $this->subtotal;
        $user = Auth::user();
        $discountPercent = DiscountSlab::getDiscountPercent($subtotal, $user->organization_id);
        return $subtotal * ($discountPercent / 100);
    }

    public function getTaxableAmountProperty()
    {
        return $this->subtotal - $this->discountAmount;
    }

    public function getGstAmountProperty()
    {
        if (!$this->selectedDealer || !$this->selectedDealer->state_id) {
            return 0;
        }

        $user = Auth::user();
        $organization = $user->organization;
        
        if (!$organization || !$organization->state_id) {
            return 0;
        }

        $taxableAmount = $this->taxableAmount;

        if ($organization->state_id === $this->selectedDealer->state_id) {
            // Same state: CGST + SGST (each 9% = 18% total)
            return $taxableAmount * 0.18;
        } else {
            // Different state: IGST (18%)
            return $taxableAmount * 0.18;
        }
    }

    public function getGrandTotalProperty()
    {
        return $this->taxableAmount + $this->gstAmount;
    }

    public function saveOrder()
    {
        if (!$this->dealerId) {
            session()->flash('error', 'Please select a dealer');
            return;
        }

        if (empty($this->cartItems)) {
            session()->flash('error', 'Cart is empty. Add products to create an order');
            return;
        }

        $user = Auth::user();

        try {
            DB::transaction(function () use ($user) {
                // Generate order number
                $orderNumber = 'ORD-' . strtoupper(Str::random(8));

                // Create order
                $order = Order::create([
                    'organization_id' => $user->organization_id,
                    'dealer_id' => $this->dealerId,
                    'order_number' => $orderNumber,
                    'subtotal' => $this->subtotal,
                    'status' => Order::STATUS_PENDING,
                ]);

                // Create order items
                foreach ($this->cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_size_id' => $item['product_size_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'rate' => $item['rate'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // Refresh to get calculated totals from observer
                $order->refresh();

                session()->flash('success', 'Order created successfully! Order Number: ' . $order->order_number);
                
                // Clear cart
                $this->cartItems = [];
                $this->dealerId = null;
                $this->selectedDealer = null;
                $this->selectedStateId = null;
            });

            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $dealers = Dealer::where('is_active', true)
            ->with(['state', 'city', 'zone'])
            ->orderBy('name', 'asc')
            ->get();

        $selectedProduct = $this->selectedProductId 
            ? Product::with(['sizes.unit', 'unit'])->find($this->selectedProductId)
            : null;

        return view('livewire.orders.create-order', [
            'dealers' => $dealers,
            'selectedProduct' => $selectedProduct,
        ]);
    }
}

