<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Organization;
use App\Models\State;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed organizations.
     */
    public function run(): void
    {
        $maharashtra = State::where('code', 'MH')->first();
        $mumbai = City::where('name', 'Mumbai')->first();

        $gujarat = State::where('code', 'GJ')->first();
        $ahmedabad = City::where('name', 'Ahmedabad')->first();

        // Organization 1 in Maharashtra
        Organization::create([
            'name' => 'AgriChemTech Maharashtra Pvt Ltd',
            'gstin' => '27AABCU9601R1ZM',
            'address' => '123 Industrial Area, Andheri East',
            'state_id' => $maharashtra->id,
            'city_id' => $mumbai->id,
            'pincode' => '400069',
            'phone' => '+91-22-12345678',
            'email' => 'info@aglichemtech-mh.com',
        ]);

        // Organization 2 in Gujarat
        Organization::create([
            'name' => 'AgriChemTech Gujarat Industries',
            'gstin' => '24AABCU9601R1ZN',
            'address' => '456 Business Park, Vastrapur',
            'state_id' => $gujarat->id,
            'city_id' => $ahmedabad->id,
            'pincode' => '380015',
            'phone' => '+91-79-87654321',
            'email' => 'info@aglichemtech-gj.com',
        ]);
    }
}

