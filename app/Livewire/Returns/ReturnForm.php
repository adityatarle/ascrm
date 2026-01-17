<?php

namespace App\Livewire\Returns;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ReturnForm extends Component
{
    public $returnId;
    public $orderId;
    public $selectedOrder;
    public $reason;
    public $status = OrderReturn::STATUS_PENDING;
    public $returnedAt;
    public $returnItems = [];

    public function mount($order = null, $return = null)
    {
        $user = Auth::user();

        if ($return) {
            // Editing existing return
            if (is_object($return)) {
                $returnModel = $return;
            } else {
                $returnModel = OrderReturn::where('organization_id', $user->organization_id)
                    ->findOrFail($return);
            }

            $this->returnId = $returnModel->id;
            $this->orderId = $returnModel->order_id;
            $this->selectedOrder = $returnModel->order;
            $this->reason = $returnModel->reason;
            $this->status = $returnModel->status;
            $this->returnedAt = $returnModel->returned_at ? $returnModel->returned_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
            
            // Load return items
            $this->returnItems = $returnModel->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_item_id' => $item->order_item_id,
                    'quantity' => $item->quantity,
                    'reason' => $item->reason,
                ];
            })->toArray();
        } elseif ($order) {
            // Creating return for specific order
            if (is_object($order)) {
                $orderModel = $order;
            } else {
                $orderModel = Order::where('organization_id', $user->organization_id)
                    ->findOrFail($order);
            }

            $this->orderId = $orderModel->id;
            $this->selectedOrder = $orderModel;
            $this->returnedAt = now()->format('Y-m-d\TH:i');
            
            // Initialize return items from order items
            $this->returnItems = $orderModel->items->map(function ($item) {
                return [
                    'order_item_id' => $item->id,
                    'quantity' => 0,
                    'max_quantity' => $item->quantity,
                    'reason' => '',
                ];
            })->toArray();
        }
    }

    public function updatedOrderId()
    {
        if ($this->orderId) {
            $user = Auth::user();
            $this->selectedOrder = Order::where('organization_id', $user->organization_id)
                ->with(['items.product', 'items.productSize'])
                ->find($this->orderId);
            
            if ($this->selectedOrder) {
                $this->returnItems = $this->selectedOrder->items->map(function ($item) {
                    return [
                        'order_item_id' => $item->id,
                        'quantity' => 0,
                        'max_quantity' => $item->quantity,
                        'reason' => '',
                    ];
                })->toArray();
            }
        } else {
            $this->selectedOrder = null;
            $this->returnItems = [];
        }
    }

    public function updateReturnItem($index, $field, $value)
    {
        if (isset($this->returnItems[$index])) {
            $this->returnItems[$index][$field] = $value;
            
            // Validate quantity doesn't exceed max
            if ($field === 'quantity') {
                $maxQuantity = $this->returnItems[$index]['max_quantity'] ?? 0;
                if ($value > $maxQuantity) {
                    $this->returnItems[$index]['quantity'] = $maxQuantity;
                }
            }
        }
    }

    public function save()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'orderId' => 'required|exists:orders,id',
            'reason' => 'nullable|string',
            'status' => 'required|in:' . implode(',', [
                OrderReturn::STATUS_PENDING,
                OrderReturn::STATUS_APPROVED,
                OrderReturn::STATUS_REJECTED,
                OrderReturn::STATUS_PROCESSED,
            ]),
            'returnedAt' => 'nullable|date',
            'returnItems.*.order_item_id' => 'required|exists:order_items,id',
            'returnItems.*.quantity' => 'required|integer|min:1',
        ]);

        // Verify order belongs to user's organization
        $order = Order::where('organization_id', $user->organization_id)
            ->findOrFail($this->orderId);

        // Filter out items with zero quantity
        $validItems = array_filter($this->returnItems, function ($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        if (empty($validItems)) {
            $this->addError('returnItems', 'Please select at least one item to return');
            return;
        }

        if ($this->returnId) {
            // Update existing return
            $return = OrderReturn::where('organization_id', $user->organization_id)
                ->findOrFail($this->returnId);

            $return->update([
                'reason' => $this->reason,
                'status' => $this->status,
                'returned_at' => $this->returnedAt ?: now(),
            ]);

            // Update return items
            $return->items()->delete();
            foreach ($validItems as $item) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $item['order_item_id'],
                    'quantity' => $item['quantity'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            session()->flash('message', 'Return updated successfully');
        } else {
            // Create new return
            $returnNumber = 'RET-' . strtoupper(Str::random(8));

            $return = OrderReturn::create([
                'organization_id' => $user->organization_id,
                'order_id' => $this->orderId,
                'dealer_id' => $order->dealer_id,
                'return_number' => $returnNumber,
                'reason' => $this->reason,
                'status' => $this->status,
                'returned_at' => $this->returnedAt ?: now(),
            ]);

            // Create return items
            foreach ($validItems as $item) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $item['order_item_id'],
                    'quantity' => $item['quantity'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            session()->flash('message', 'Return created successfully');
        }

        return redirect()->route('returns.index');
    }

    public function render()
    {
        $user = Auth::user();
        
        $orders = Order::where('organization_id', $user->organization_id)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->with(['dealer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.returns.return-form', [
            'orders' => $orders,
        ]);
    }
}
