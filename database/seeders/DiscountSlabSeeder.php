<?php

namespace Database\Seeders;

use App\Models\DiscountSlab;
use Illuminate\Database\Seeder;

class DiscountSlabSeeder extends Seeder
{
    /**
     * Seed discount slabs.
     * Example: 0-11999 => 0%, 12000-49999 => 5%, 50000+ => 8%
     */
    public function run(): void
    {
        DiscountSlab::create([
            'min_amount' => 0,
            'max_amount' => 11999.99,
            'discount_percent' => 0,
            'is_active' => true,
        ]);

        DiscountSlab::create([
            'min_amount' => 12000,
            'max_amount' => 49999.99,
            'discount_percent' => 5,
            'is_active' => true,
        ]);

        DiscountSlab::create([
            'min_amount' => 50000,
            'max_amount' => null, // No upper limit
            'discount_percent' => 8,
            'is_active' => true,
        ]);
    }
}

