<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $methodFilter = '';
    public $perPage = 15;

    protected $queryString = ['search', 'statusFilter', 'methodFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingMethodFilter()
    {
        $this->resetPage();
    }

    public function delete($paymentId)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete payments.');
            return;
        }

        $payment = Payment::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->findOrFail($paymentId);

        $payment->delete();
        session()->flash('message', 'Payment deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Payment::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->with(['order.dealer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_id', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('cheque_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('order', function ($q) {
                        $q->where('order_number', 'like', '%' . $this->search . '%')
                            ->orWhereHas('dealer', function ($q) {
                                $q->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->methodFilter) {
            $query->where('payment_method', $this->methodFilter);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.payments.payments-table', [
            'payments' => $payments,
        ]);
    }
}
