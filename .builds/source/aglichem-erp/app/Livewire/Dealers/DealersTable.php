<?php

namespace App\Livewire\Dealers;

use App\Models\Dealer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DealersTable extends Component
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

    public function delete($dealerId)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete dealers.');
            return;
        }

        $dealer = Dealer::findOrFail($dealerId);

        // Soft delete by setting is_active to false
        $dealer->update(['is_active' => false]);

        session()->flash('message', 'Dealer deleted successfully');
        $this->resetPage();
    }

    public function toggleInactive()
    {
        $this->showInactive = !$this->showInactive;
        $this->resetPage();
    }

    public function render()
    {
        $query = Dealer::with(['city', 'state', 'zone']);

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $dealers = $query->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.dealers.dealers-table', [
            'dealers' => $dealers,
        ]);
    }
}

