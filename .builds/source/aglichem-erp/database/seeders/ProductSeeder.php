<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed products.
     */
    public function run(): void
    {
        $org1 = Organization::where('gstin', '27AABCU9601R1ZM')->first();
        $org2 = Organization::where('gstin', '24AABCU9601R1ZN')->first();

        // Products for Organization 1
        Product::create([
            'organization_id' => $org1->id,
            'name' => 'Urea Fertilizer',
            'code' => 'PROD-001',
            'description' => 'High-grade urea fertilizer for agricultural use',
            'unit' => 'Kg',
            'base_price' => 500.00,
            'is_active' => true,
        ]);

        Product::create([
            'organization_id' => $org1->id,
            'name' => 'NPK 19:19:19',
            'code' => 'PROD-002',
            'description' => 'Balanced NPK fertilizer',
            'unit' => 'Kg',
            'base_price' => 1200.00,
            'is_active' => true,
        ]);

        Product::create([
            'organization_id' => $org1->id,
            'name' => 'DAP Fertilizer',
            'code' => 'PROD-003',
            'description' => 'Diammonium Phosphate fertilizer',
            'unit' => 'Kg',
            'base_price' => 800.00,
            'is_active' => true,
        ]);

        // Products for Organization 2
        Product::create([
            'organization_id' => $org2->id,
            'name' => 'Organic Compost',
            'code' => 'PROD-004',
            'description' => 'Premium organic compost',
            'unit' => 'Kg',
            'base_price' => 300.00,
            'is_active' => true,
        ]);

        Product::create([
            'organization_id' => $org2->id,
            'name' => 'Potash Fertilizer',
            'code' => 'PROD-005',
            'description' => 'Potassium-based fertilizer',
            'unit' => 'Kg',
            'base_price' => 900.00,
            'is_active' => true,
        ]);

        Product::create([
            'organization_id' => $org2->id,
            'name' => 'Pesticide - Neem Oil',
            'code' => 'PROD-006',
            'description' => 'Natural neem oil pesticide',
            'unit' => 'Litre',
            'base_price' => 450.00,
            'is_active' => true,
        ]);
    }
}

