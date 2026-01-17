<?php

namespace Tests\Unit;

use App\Models\DiscountSlab;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Dealer;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GstCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test GST calculation for same state (CGST + SGST).
     */
    public function test_gst_calculation_same_state(): void
    {
        // Create states
        $state = State::factory()->create(['name' => 'Maharashtra', 'code' => 'MH']);

        // Create organization in Maharashtra
        $organization = Organization::factory()->create([
            'state_id' => $state->id,
        ]);

        // Create dealer in same state
        $dealer = Dealer::factory()->create([
            'state_id' => $state->id,
        ]);

        // Create order with subtotal
        $order = new Order([
            'organization_id' => $organization->id,
            'dealer_id' => $dealer->id,
            'order_number' => 'ORD-TEST-001',
            'subtotal' => 10000.00,
            'status' => 'pending',
        ]);

        // Trigger observer calculation
        $order->save();

        // Assert CGST and SGST are calculated (each 9% = 18% total)
        // After discount: 10000 - 0 = 10000 (0% discount for < 12000)
        // Taxable: 10000
        // CGST: 10000 * 0.09 = 900
        // SGST: 10000 * 0.09 = 900
        // IGST: 0
        $this->assertEquals(900.00, round($order->cgst_amount, 2));
        $this->assertEquals(900.00, round($order->sgst_amount, 2));
        $this->assertEquals(0.00, round($order->igst_amount, 2));
        $this->assertEquals(11800.00, round($order->grand_total, 2));
    }

    /**
     * Test GST calculation for different state (IGST).
     */
    public function test_gst_calculation_different_state(): void
    {
        // Create states
        $state1 = State::factory()->create(['name' => 'Maharashtra', 'code' => 'MH']);
        $state2 = State::factory()->create(['name' => 'Gujarat', 'code' => 'GJ']);

        // Create organization in Maharashtra
        $organization = Organization::factory()->create([
            'state_id' => $state1->id,
        ]);

        // Create dealer in different state
        $dealer = Dealer::factory()->create([
            'state_id' => $state2->id,
        ]);

        // Create order with subtotal
        $order = new Order([
            'organization_id' => $organization->id,
            'dealer_id' => $dealer->id,
            'order_number' => 'ORD-TEST-002',
            'subtotal' => 10000.00,
            'status' => 'pending',
        ]);

        // Trigger observer calculation
        $order->save();

        // Assert IGST is calculated (18%)
        // After discount: 10000 - 0 = 10000
        // Taxable: 10000
        // IGST: 10000 * 0.18 = 1800
        // CGST: 0, SGST: 0
        $this->assertEquals(0.00, round($order->cgst_amount, 2));
        $this->assertEquals(0.00, round($order->sgst_amount, 2));
        $this->assertEquals(1800.00, round($order->igst_amount, 2));
        $this->assertEquals(11800.00, round($order->grand_total, 2));
    }
}

