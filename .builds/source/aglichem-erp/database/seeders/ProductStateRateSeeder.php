<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStateRate;
use App\Models\State;
use Illuminate\Database\Seeder;

class ProductStateRateSeeder extends Seeder
{
    /**
     * Seed product state rates.
     */
    public function run(): void
    {
        $maharashtra = State::where('code', 'MH')->first();
        $gujarat = State::where('code', 'GJ')->first();
        $karnataka = State::where('code', 'KA')->first();

        $products = Product::all();

        foreach ($products as $product) {
            // Set state-specific rates (different from base price for some states)
            if ($product->organization_id == 1) {
                // For org1 products
                ProductStateRate::create([
                    'product_id' => $product->id,
                    'state_id' => $maharashtra->id,
                    'rate' => $product->base_price * 1.0, // Same as base
                ]);

                ProductStateRate::create([
                    'product_id' => $product->id,
                    'state_id' => $gujarat->id,
                    'rate' => $product->base_price * 1.05, // 5% higher
                ]);

                ProductStateRate::create([
                    'product_id' => $product->id,
                    'state_id' => $karnataka->id,
                    'rate' => $product->base_price * 1.08, // 8% higher
                ]);
            } else {
                // For org2 products
                ProductStateRate::create([
                    'product_id' => $product->id,
                    'state_id' => $gujarat->id,
                    'rate' => $product->base_price * 1.0, // Same as base
                ]);

                ProductStateRate::create([
                    'product_id' => $product->id,
                    'state_id' => $maharashtra->id,
                    'rate' => $product->base_price * 1.06, // 6% higher
                ]);
            }
        }
    }
}

