<?php

namespace App\Livewire\Masters;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showInactive = false;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($productId)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete products.');
            return;
        }

        $product = Product::where('organization_id', $user->organization_id)
            ->findOrFail($productId);

        // Soft delete by setting is_active to false
        $product->update(['is_active' => false]);

        session()->flash('message', 'Product deleted successfully');
        $this->resetPage();
    }

    public function toggleInactive()
    {
        $this->showInactive = !$this->showInactive;
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Product::where('organization_id', $user->organization_id);

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.masters.products-table', [
            'products' => $products,
        ]);
    }
}

