<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\DiscountSlab;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     * Calculate GST and totals before saving.
     */
    public function creating(Order $order): void
    {
        if ($order->isDirty('subtotal') || !$order->exists) {
            $this->calculateOrderTotals($order);
        }
    }

    /**
     * Handle the Order "updating" event.
     * Recalculate GST and totals if subtotal changed.
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty('subtotal')) {
            $this->calculateOrderTotals($order);
        }
    }

    /**
     * Calculate order totals including discount, GST, and grand total.
     */
    protected function calculateOrderTotals(Order $order): void
    {
        $subtotal = $order->subtotal;

        // Get discount percent from slabs
        $discountPercent = DiscountSlab::getDiscountPercent($subtotal);
        $discountAmount = $subtotal * ($discountPercent / 100);
        $taxableAmount = $subtotal - $discountAmount;

        // Get organization and dealer states for GST calculation
        $organization = $order->organization;
        $dealer = $order->dealer;

        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;

        if ($organization && $dealer && $organization->state_id && $dealer->state_id) {
            if ($organization->state_id === $dealer->state_id) {
                // Same state: CGST + SGST (each 9% = 18% total)
                $cgstAmount = $taxableAmount * 0.09;
                $sgstAmount = $taxableAmount * 0.09;
            } else {
                // Different state: IGST (18%)
                $igstAmount = $taxableAmount * 0.18;
            }
        }

        $grandTotal = $taxableAmount + $cgstAmount + $sgstAmount + $igstAmount;

        // Update order attributes
        $order->discount_amount = round($discountAmount, 2);
        $order->taxable_amount = round($taxableAmount, 2);
        $order->cgst_amount = round($cgstAmount, 2);
        $order->sgst_amount = round($sgstAmount, 2);
        $order->igst_amount = round($igstAmount, 2);
        $order->grand_total = round($grandTotal, 2);
    }
}

