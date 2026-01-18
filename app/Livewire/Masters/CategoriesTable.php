<?php

namespace App\Livewire\Masters;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesTable extends Component
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

    public function delete($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->delete();
        session()->flash('message', 'Category deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $query = Category::query();

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $categories = $query->orderBy('sort_order')->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.masters.categories-table', [
            'categories' => $categories,
        ]);
    }
}
