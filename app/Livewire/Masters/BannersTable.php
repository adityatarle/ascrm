<?php

namespace App\Livewire\Masters;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class BannersTable extends Component
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

    public function delete($bannerId)
    {
        $banner = Banner::findOrFail($bannerId);
        $banner->delete();
        session()->flash('message', 'Banner deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $query = Banner::query();

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $banners = $query->orderBy('sort_order')->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.masters.banners-table', [
            'banners' => $banners,
        ]);
    }
}
