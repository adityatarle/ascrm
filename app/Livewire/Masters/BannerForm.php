<?php

namespace App\Livewire\Masters;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BannerForm extends Component
{
    use WithFileUploads;

    public $bannerId;
    public $title;
    public $description;
    public $image;
    public $existingImage;
    public $link;
    public $isActive = true;
    public $sortOrder = 0;
    public $startDate;
    public $endDate;

    public function mount($banner = null)
    {
        if ($banner) {
            if (is_object($banner)) {
                $bannerModel = $banner;
            } else {
                $bannerModel = Banner::findOrFail($banner);
            }

            $this->bannerId = $bannerModel->id;
            $this->title = $bannerModel->title;
            $this->description = $bannerModel->description;
            $this->existingImage = $bannerModel->image;
            $this->link = $bannerModel->link;
            $this->isActive = $bannerModel->is_active;
            $this->sortOrder = $bannerModel->sort_order;
            $this->startDate = $bannerModel->start_date?->format('Y-m-d');
            $this->endDate = $bannerModel->end_date?->format('Y-m-d');
        }
    }

    public function save()
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:500',
            'isActive' => 'boolean',
            'sortOrder' => 'nullable|integer|min:0',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ];

        // Image is required for new banners, optional for updates
        if ($this->bannerId) {
            $rules['image'] = 'nullable|image|max:5120'; // 5MB max
        } else {
            $rules['image'] = 'required|image|max:5120';
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
            $imagePath = $this->image->store('banners', 'public');
        }

        if ($this->bannerId) {
            $banner = Banner::findOrFail($this->bannerId);
            $banner->update([
                'title' => $this->title,
                'description' => $this->description,
                'image' => $imagePath,
                'link' => $this->link,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);

            session()->flash('message', 'Banner updated successfully');
        } else {
            Banner::create([
                'title' => $this->title,
                'description' => $this->description,
                'image' => $imagePath,
                'link' => $this->link,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);

            session()->flash('message', 'Banner created successfully');
        }

        return redirect()->route('banners.index');
    }

    public function render()
    {
        return view('livewire.masters.banner-form');
    }
}
