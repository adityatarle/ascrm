<?php

namespace App\Livewire\Masters;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CategoryForm extends Component
{
    use WithFileUploads;

    public $categoryId;
    public $name;
    public $image;
    public $existingImage;
    public $description;
    public $isActive = true;
    public $sortOrder = 0;

    public function mount($category = null)
    {
        if ($category) {
            if (is_object($category)) {
                $categoryModel = $category;
            } else {
                $categoryModel = Category::findOrFail($category);
            }

            $this->categoryId = $categoryModel->id;
            $this->name = $categoryModel->name;
            $this->existingImage = $categoryModel->image;
            $this->description = $categoryModel->description;
            $this->isActive = $categoryModel->is_active;
            $this->sortOrder = $categoryModel->sort_order;
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isActive' => 'boolean',
            'sortOrder' => 'nullable|integer|min:0',
        ];

        // Image is required for new categories, optional for updates
        if ($this->categoryId) {
            $rules['image'] = 'nullable|image|max:2048';
        } else {
            $rules['image'] = 'required|image|max:2048';
        }

        $validated = $this->validate($rules);

        $imagePath = $this->existingImage;

        // Handle image upload
        if ($this->image) {
            // Delete old image if exists
            if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }

            // Store new image
            $imagePath = $this->image->store('categories', 'public');
        }

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'image' => $imagePath,
                'description' => $this->description,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
            ]);

            session()->flash('message', 'Category updated successfully');
        } else {
            Category::create([
                'name' => $this->name,
                'image' => $imagePath,
                'description' => $this->description,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
            ]);

            session()->flash('message', 'Category created successfully');
        }

        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.masters.category-form');
    }
}
