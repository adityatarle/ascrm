<?php

namespace App\Livewire\Dispatches;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class DispatchForm extends Component
{
    public $dispatchId;
    public $orderId;
    public $selectedOrder;
    public $lrNumber;
    public $transporterName;
    public $vehicleNumber;
    public $dispatchedAt;
    public $status = Dispatch::STATUS_DISPATCHED;
    public $dispatchItems = [];

    public function mount($order = null, $dispatch = null)
    {
        $user = Auth::user();

        if ($dispatch) {
            // Editing existing dispatch
            if (is_object($dispatch)) {
                $dispatchModel = $dispatch;
            } else {
                $dispatchModel = Dispatch::whereHas('order', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                })->with('items.orderItem')->findOrFail($dispatch);
            }

            $this->dispatchId = $dispatchModel->id;
            $this->orderId = $dispatchModel->order_id;
            $this->selectedOrder = $dispatchModel->order;
            $this->lrNumber = $dispatchModel->lr_number;
            $this->transporterName = $dispatchModel->transporter_name;
            $this->vehicleNumber = $dispatchModel->vehicle_number;
            $this->dispatchedAt = $dispatchModel->dispatched_at ? $dispatchModel->dispatched_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
            $this->status = $dispatchModel->status;
            
            // Load dispatch items
            $this->dispatchItems = $dispatchModel->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_item_id' => $item->order_item_id,
                    'quantity' => $item->quantity,
                ];
            })->toArray();
        } elseif ($order) {
            // Creating dispatch for specific order
            if (is_object($order)) {
                $orderModel = $order;
            } else {
                $orderModel = Order::where('organization_id', $user->organization_id)
                    ->with(['items.product', 'items.productSize', 'items.dispatchItems'])
                    ->findOrFail($order);
            }

            $this->orderId = $orderModel->id;
            $this->selectedOrder = $orderModel;
            $this->dispatchedAt = now()->format('Y-m-d\TH:i');
            
            // Initialize dispatch items from order items with remaining quantities
            $this->dispatchItems = $orderModel->items->map(function ($item) {
                $dispatchedQty = $item->dispatched_quantity ?? 0;
                $remainingQty = $item->quantity - $dispatchedQty;
                
                return [
                    'order_item_id' => $item->id,
                    'quantity' => 0,
                    'max_quantity' => max(0, $remainingQty),
                    'product_name' => $item->product->name,
                    'size_label' => $item->productSize ? ($item->productSize->size_label ?: ($item->productSize->size_value . ($item->productSize->unit ? $item->productSize->unit->symbol : ''))) : 'Base',
                ];
            })->toArray();
        }
    }

    public function updatedOrderId()
    {
        if ($this->orderId) {
            $user = Auth::user();
            $this->selectedOrder = Order::where('organization_id', $user->organization_id)
                ->with(['dealer', 'items.product', 'items.productSize', 'items.dispatchItems'])
                ->find($this->orderId);
            
            if ($this->selectedOrder) {
                // Initialize dispatch items from order items
                $this->dispatchItems = $this->selectedOrder->items->map(function ($item) {
                    $dispatchedQty = $item->dispatched_quantity ?? 0;
                    $remainingQty = $item->quantity - $dispatchedQty;
                    
                    return [
                        'order_item_id' => $item->id,
                        'quantity' => 0,
                        'max_quantity' => max(0, $remainingQty),
                        'product_name' => $item->product->name,
                        'size_label' => $item->productSize ? ($item->productSize->size_label ?: ($item->productSize->size_value . ($item->productSize->unit ? $item->productSize->unit->symbol : ''))) : 'Base',
                    ];
                })->toArray();
            }
        } else {
            $this->selectedOrder = null;
            $this->dispatchItems = [];
        }
    }

    public function updateDispatchItem($index, $field, $value)
    {
        if (isset($this->dispatchItems[$index])) {
            $this->dispatchItems[$index][$field] = $value;
            
            // Validate quantity doesn't exceed max
            if ($field === 'quantity') {
                $maxQuantity = $this->dispatchItems[$index]['max_quantity'] ?? 0;
                if ($value > $maxQuantity) {
                    $this->dispatchItems[$index]['quantity'] = $maxQuantity;
                }
                if ($value < 0) {
                    $this->dispatchItems[$index]['quantity'] = 0;
                }
            }
        }
    }

    public function save()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'orderId' => 'required|exists:orders,id',
            'lrNumber' => 'nullable|string|max:255',
            'transporterName' => 'nullable|string|max:255',
            'vehicleNumber' => 'nullable|string|max:255',
            'dispatchedAt' => 'nullable|date',
            'status' => 'required|in:' . implode(',', [Dispatch::STATUS_PENDING, Dispatch::STATUS_DISPATCHED, Dispatch::STATUS_IN_TRANSIT, Dispatch::STATUS_DELIVERED]),
            'dispatchItems.*.order_item_id' => 'required|exists:order_items,id',
            'dispatchItems.*.quantity' => 'required|integer|min:0',
        ]);

        // Verify order belongs to user's organization
        $order = Order::where('organization_id', $user->organization_id)
            ->with(['items'])
            ->findOrFail($this->orderId);

        // Filter out items with zero quantity
        $validItems = array_filter($this->dispatchItems, function ($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        if (empty($validItems) && !$this->dispatchId) {
            $this->addError('dispatchItems', 'Please select at least one item to dispatch');
            return;
        }

        // Validate quantities don't exceed remaining
        foreach ($validItems as $item) {
            $orderItem = $order->items->find($item['order_item_id']);
            if ($orderItem) {
                $dispatchedQty = $orderItem->dispatched_quantity ?? 0;
                $remainingQty = $orderItem->quantity - $dispatchedQty;
                
                // If editing, subtract current dispatch quantities
                if ($this->dispatchId) {
                    $currentDispatch = Dispatch::with('items')->find($this->dispatchId);
                    $currentDispatchQty = $currentDispatch->items
                        ->where('order_item_id', $item['order_item_id'])
                        ->sum('quantity');
                    $remainingQty += $currentDispatchQty;
                }
                
                if ($item['quantity'] > $remainingQty) {
                    $this->addError('dispatchItems', "Quantity for {$orderItem->product->name} cannot exceed remaining quantity ({$remainingQty})");
                    return;
                }
            }
        }

        try {
            DB::transaction(function () use ($order, $user, $validItems) {
                if ($this->dispatchId) {
                    // Update existing dispatch
                    $dispatch = Dispatch::whereHas('order', function ($q) use ($user) {
                        $q->where('organization_id', $user->organization_id);
                    })->findOrFail($this->dispatchId);

                    // Revert previous dispatch quantities
                    foreach ($dispatch->items as $item) {
                        $orderItem = $order->items->find($item->order_item_id);
                        if ($orderItem) {
                            $orderItem->decrement('dispatched_quantity', $item->quantity);
                        }
                    }

                    $dispatch->update([
                        'lr_number' => $this->lrNumber,
                        'transporter_name' => $this->transporterName,
                        'vehicle_number' => $this->vehicleNumber,
                        'dispatched_at' => $this->dispatchedAt ?: now(),
                        'status' => $this->status,
                    ]);

                    // Delete old dispatch items
                    $dispatch->items()->delete();

                    // Create new dispatch items and update order item quantities
                    foreach ($validItems as $item) {
                        DispatchItem::create([
                            'dispatch_id' => $dispatch->id,
                            'order_item_id' => $item['order_item_id'],
                            'quantity' => $item['quantity'],
                        ]);

                        $orderItem = $order->items->find($item['order_item_id']);
                        if ($orderItem) {
                            $orderItem->increment('dispatched_quantity', $item['quantity']);
                        }
                    }

                    session()->flash('message', 'Dispatch updated successfully');
                } else {
                    // Create new dispatch
                    $dispatchNumber = 'DISP-' . strtoupper(Str::random(8));

                    $dispatch = Dispatch::create([
                        'order_id' => $this->orderId,
                        'dispatch_number' => $dispatchNumber,
                        'lr_number' => $this->lrNumber,
                        'transporter_name' => $this->transporterName,
                        'vehicle_number' => $this->vehicleNumber,
                        'dispatched_at' => $this->dispatchedAt ?: now(),
                        'status' => $this->status,
                    ]);

                    // Create dispatch items and update order item quantities
                    foreach ($validItems as $item) {
                        DispatchItem::create([
                            'dispatch_id' => $dispatch->id,
                            'order_item_id' => $item['order_item_id'],
                            'quantity' => $item['quantity'],
                        ]);

                        $orderItem = $order->items->find($item['order_item_id']);
                        if ($orderItem) {
                            $orderItem->increment('dispatched_quantity', $item['quantity']);
                        }
                    }

                    // Update order status if dispatched
                    if ($this->status === Dispatch::STATUS_DISPATCHED && $order->status === Order::STATUS_PENDING) {
                        $order->update(['status' => Order::STATUS_DISPATCHED]);
                    }

                    session()->flash('message', 'Dispatch created successfully');
                }
            });

            return redirect()->route('dispatches.index');
        } catch (\Exception $e) {
            $this->addError('dispatchItems', 'Error creating dispatch: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        $orders = Order::where('organization_id', $user->organization_id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_DISPATCHED])
            ->with(['dealer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.dispatches.dispatch-form', [
            'orders' => $orders,
        ]);
    }
}
