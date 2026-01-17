<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Seed zones and update cities with zone assignments.
     */
    public function run(): void
    {
        $maharashtra = State::where('code', 'MH')->first();
        $gujarat = State::where('code', 'GJ')->first();
        $karnataka = State::where('code', 'KA')->first();
        $tamilNadu = State::where('code', 'TN')->first();

        // Create zones
        $zone1 = Zone::create([
            'state_id' => $maharashtra->id,
            'name' => 'Maharashtra Zone 1',
            'code' => 'MH-Z1',
        ]);

        $zone2 = Zone::create([
            'state_id' => $gujarat->id,
            'name' => 'Gujarat Zone 1',
            'code' => 'GJ-Z1',
        ]);

        $zone3 = Zone::create([
            'state_id' => $karnataka->id,
            'name' => 'Karnataka Zone 1',
            'code' => 'KA-Z1',
        ]);

        $zone4 = Zone::create([
            'state_id' => $tamilNadu->id,
            'name' => 'Tamil Nadu Zone 1',
            'code' => 'TN-Z1',
        ]);

        // Assign cities to zones
        \App\Models\City::where('name', 'Mumbai')->update(['zone_id' => $zone1->id]);
        \App\Models\City::where('name', 'Pune')->update(['zone_id' => $zone1->id]);
        \App\Models\City::where('name', 'Ahmedabad')->update(['zone_id' => $zone2->id]);
        \App\Models\City::where('name', 'Surat')->update(['zone_id' => $zone2->id]);
        \App\Models\City::where('name', 'Bangalore')->update(['zone_id' => $zone3->id]);
        \App\Models\City::where('name', 'Chennai')->update(['zone_id' => $zone4->id]);
    }
}

