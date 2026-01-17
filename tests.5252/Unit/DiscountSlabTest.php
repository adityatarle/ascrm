<?php

namespace Tests\Unit;

use App\Models\DiscountSlab;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Dealer;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountSlabTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test discount slab calculation for different amounts.
     */
    public function test_discount_slab_calculation(): void
    {
        // Create discount slabs
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
            'max_amount' => null,
            'discount_percent' => 8,
            'is_active' => true,
        ]);

        // Test 0% discount for amount < 12000
        $discountPercent1 = DiscountSlab::getDiscountPercent(10000);
        $this->assertEquals(0, $discountPercent1);

        // Test 5% discount for amount between 12000-49999
        $discountPercent2 = DiscountSlab::getDiscountPercent(25000);
        $this->assertEquals(5, $discountPercent2);

        // Test 8% discount for amount >= 50000
        $discountPercent3 = DiscountSlab::getDiscountPercent(60000);
        $this->assertEquals(8, $discountPercent3);
    }

    /**
     * Test order discount calculation with 5% slab.
     */
    public function test_order_discount_calculation_5_percent(): void
    {
        // Create discount slab
        DiscountSlab::create([
            'min_amount' => 12000,
            'max_amount' => 49999.99,
            'discount_percent' => 5,
            'is_active' => true,
        ]);

        $state = State::factory()->create();
        $organization = Organization::factory()->create(['state_id' => $state->id]);
        $dealer = Dealer::factory()->create(['state_id' => $state->id]);

        $order = new Order([
            'organization_id' => $organization->id,
            'dealer_id' => $dealer->id,
            'order_number' => 'ORD-TEST-003',
            'subtotal' => 20000.00,
            'status' => 'pending',
        ]);

        $order->save();

        // Discount: 20000 * 0.05 = 1000
        // Taxable: 20000 - 1000 = 19000
        $this->assertEquals(1000.00, round($order->discount_amount, 2));
        $this->assertEquals(19000.00, round($order->taxable_amount, 2));
    }

    /**
     * Test order discount calculation with 8% slab.
     */
    public function test_order_discount_calculation_8_percent(): void
    {
        // Create discount slab
        DiscountSlab::create([
            'min_amount' => 50000,
            'max_amount' => null,
            'discount_percent' => 8,
            'is_active' => true,
        ]);

        $state = State::factory()->create();
        $organization = Organization::factory()->create(['state_id' => $state->id]);
        $dealer = Dealer::factory()->create(['state_id' => $state->id]);

        $order = new Order([
            'organization_id' => $organization->id,
            'dealer_id' => $dealer->id,
            'order_number' => 'ORD-TEST-004',
            'subtotal' => 60000.00,
            'status' => 'pending',
        ]);

        $order->save();

        // Discount: 60000 * 0.08 = 4800
        // Taxable: 60000 - 4800 = 55200
        $this->assertEquals(4800.00, round($order->discount_amount, 2));
        $this->assertEquals(55200.00, round($order->taxable_amount, 2));
    }
}

