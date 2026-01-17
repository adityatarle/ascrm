<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class CountryStateCitySeeder extends Seeder
{
    /**
     * Seed countries, states, and cities.
     */
    public function run(): void
    {
        // Create India
        $india = Country::create([
            'name' => 'India',
            'code' => 'IN',
        ]);

        // Create states
        $maharashtra = State::create([
            'country_id' => $india->id,
            'name' => 'Maharashtra',
            'code' => 'MH',
        ]);

        $gujarat = State::create([
            'country_id' => $india->id,
            'name' => 'Gujarat',
            'code' => 'GJ',
        ]);

        $karnataka = State::create([
            'country_id' => $india->id,
            'name' => 'Karnataka',
            'code' => 'KA',
        ]);

        $tamilNadu = State::create([
            'country_id' => $india->id,
            'name' => 'Tamil Nadu',
            'code' => 'TN',
        ]);

        // Create cities for Maharashtra
        City::create(['state_id' => $maharashtra->id, 'name' => 'Mumbai']);
        City::create(['state_id' => $maharashtra->id, 'name' => 'Pune']);
        City::create(['state_id' => $maharashtra->id, 'name' => 'Nagpur']);
        City::create(['state_id' => $maharashtra->id, 'name' => 'Nashik']);

        // Create cities for Gujarat
        City::create(['state_id' => $gujarat->id, 'name' => 'Ahmedabad']);
        City::create(['state_id' => $gujarat->id, 'name' => 'Surat']);
        City::create(['state_id' => $gujarat->id, 'name' => 'Vadodara']);

        // Create cities for Karnataka
        City::create(['state_id' => $karnataka->id, 'name' => 'Bangalore']);
        City::create(['state_id' => $karnataka->id, 'name' => 'Mysore']);

        // Create cities for Tamil Nadu
        City::create(['state_id' => $tamilNadu->id, 'name' => 'Chennai']);
        City::create(['state_id' => $tamilNadu->id, 'name' => 'Coimbatore']);
    }
}

