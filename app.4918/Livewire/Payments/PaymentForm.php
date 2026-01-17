<?php

namespace App\Livewire\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentForm extends Component
{
    public $paymentId;
    public $orderId;
    public $selectedOrder;
    public $amount;
    public $paymentMethod = Payment::METHOD_CASH;
    public $transactionId;
    public $chequeNumber;
    public $bankName;
    public $referenceNumber;
    public $notes;
    public $status = Payment::STATUS_COMPLETED;
    public $paidAt;

    public function mount($order = null, $payment = null)
    {
        $user = Auth::user();

        if ($payment) {
            // Editing existing payment
            if (is_object($payment)) {
                $paymentModel = $payment;
            } else {
                $paymentModel = Payment::whereHas('order', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                })->findOrFail($payment);
            }

            $this->paymentId = $paymentModel->id;
            $this->orderId = $paymentModel->order_id;
            $this->selectedOrder = $paymentModel->order;
            $this->amount = $paymentModel->amount;
            $this->paymentMethod = $paymentModel->payment_method;
            $this->transactionId = $paymentModel->transaction_id;
            $this->chequeNumber = $paymentModel->cheque_number;
            $this->bankName = $paymentModel->bank_name;
            $this->referenceNumber = $paymentModel->reference_number;
            $this->notes = $paymentModel->notes;
            $this->status = $paymentModel->status;
            $this->paidAt = $paymentModel->paid_at ? $paymentModel->paid_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        } elseif ($order) {
            // Creating payment for specific order
            if (is_object($order)) {
                $orderModel = $order;
            } else {
                $orderModel = Order::where('organization_id', $user->organization_id)
                    ->findOrFail($order);
            }

            $this->orderId = $orderModel->id;
            $this->selectedOrder = $orderModel;
            $this->amount = $orderModel->remaining_amount;
            $this->paidAt = now()->format('Y-m-d\TH:i');
        }
    }

    public function updatedOrderId()
    {
        if ($this->orderId) {
            $user = Auth::user();
            $this->selectedOrder = Order::where('organization_id', $user->organization_id)
                ->with(['dealer', 'payments'])
                ->find($this->orderId);
            
            if ($this->selectedOrder) {
                $this->amount = $this->selectedOrder->remaining_amount;
            }
        } else {
            $this->selectedOrder = null;
        }
    }

    public function save()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'orderId' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|in:' . implode(',', [
                Payment::METHOD_CASH,
                Payment::METHOD_CHEQUE,
                Payment::METHOD_NEFT,
                Payment::METHOD_RTGS,
                Payment::METHOD_UPI,
                Payment::METHOD_BANK_TRANSFER,
                Payment::METHOD_CREDIT_CARD,
                Payment::METHOD_DEBIT_CARD,
            ]),
            'transactionId' => 'nullable|string|max:255',
            'chequeNumber' => 'nullable|string|max:255',
            'bankName' => 'nullable|string|max:255',
            'referenceNumber' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:' . implode(',', [
                Payment::STATUS_PENDING,
                Payment::STATUS_COMPLETED,
                Payment::STATUS_FAILED,
                Payment::STATUS_REFUNDED,
            ]),
            'paidAt' => 'nullable|date',
        ]);

        // Verify order belongs to user's organization
        $order = Order::where('organization_id', $user->organization_id)
            ->with('payments')
            ->findOrFail($this->orderId);

        // Check if payment amount exceeds remaining amount
        $remainingAmount = $order->remaining_amount;
        if ($this->amount > $remainingAmount && !$this->paymentId) {
            $this->addError('amount', "Payment amount cannot exceed remaining amount (₹" . number_format($remainingAmount, 2) . ")");
            return;
        }

        if ($this->paymentId) {
            // Update existing payment
            $payment = Payment::whereHas('order', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })->findOrFail($this->paymentId);

            $payment->update([
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'transaction_id' => $this->transactionId,
                'cheque_number' => $this->chequeNumber,
                'bank_name' => $this->bankName,
                'reference_number' => $this->referenceNumber,
                'notes' => $this->notes,
                'status' => $this->status,
                'paid_at' => $this->paidAt ?: now(),
            ]);

            session()->flash('message', 'Payment updated successfully');
        } else {
            // Create new payment
            $payment = Payment::create([
                'order_id' => $this->orderId,
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'transaction_id' => $this->transactionId,
                'cheque_number' => $this->chequeNumber,
                'bank_name' => $this->bankName,
                'reference_number' => $this->referenceNumber,
                'notes' => $this->notes,
                'status' => $this->status,
                'paid_at' => $this->paidAt ?: now(),
            ]);

            session()->flash('message', 'Payment recorded successfully');
        }

        return redirect()->route('orders.show', $this->orderId);
    }

    public function render()
    {
        $user = Auth::user();
        
        $orders = Order::where('organization_id', $user->organization_id)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->with(['dealer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.payments.payment-form', [
            'orders' => $orders,
        ]);
    }
}
