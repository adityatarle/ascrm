<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Dealer;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    /**
     * Seed dealers across different zones.
     */
    public function run(): void
    {
        $mumbai = City::where('name', 'Mumbai')->first();
        $pune = City::where('name', 'Pune')->first();
        $ahmedabad = City::where('name', 'Ahmedabad')->first();
        $surat = City::where('name', 'Surat')->first();
        $bangalore = City::where('name', 'Bangalore')->first();
        $chennai = City::where('name', 'Chennai')->first();

        $zone1 = Zone::where('code', 'MH-Z1')->first();
        $zone2 = Zone::where('code', 'GJ-Z1')->first();
        $zone3 = Zone::where('code', 'KA-Z1')->first();
        $zone4 = Zone::where('code', 'TN-Z1')->first();

        Dealer::create([
            'name' => 'Mumbai Agro Traders',
            'mobile' => '9876543210',
            'email' => 'mumbai@agrotraders.com',
            'gstin' => '27ABCDE1234F1Z1',
            'address' => 'Shop No. 5, Market Street',
            'zone_id' => $zone1->id,
            'state_id' => $mumbai->state_id,
            'city_id' => $mumbai->id,
            'pincode' => '400001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Dealer::create([
            'name' => 'Pune Farm Supplies',
            'mobile' => '9876543211',
            'email' => 'pune@farmsupplies.com',
            'gstin' => '27ABCDE1234F1Z2',
            'address' => '123 Agricultural Market',
            'zone_id' => $zone1->id,
            'state_id' => $pune->state_id,
            'city_id' => $pune->id,
            'pincode' => '411001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Dealer::create([
            'name' => 'Ahmedabad Crop Care',
            'mobile' => '9876543212',
            'email' => 'ahmedabad@cropcare.com',
            'gstin' => '24ABCDE1234F1Z3',
            'address' => '456 Industrial Estate',
            'zone_id' => $zone2->id,
            'state_id' => $ahmedabad->state_id,
            'city_id' => $ahmedabad->id,
            'pincode' => '380001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Dealer::create([
            'name' => 'Surat Agri Solutions',
            'mobile' => '9876543213',
            'email' => 'surat@agrisolutions.com',
            'gstin' => '24ABCDE1234F1Z4',
            'address' => '789 Business Hub',
            'zone_id' => $zone2->id,
            'state_id' => $surat->state_id,
            'city_id' => $surat->id,
            'pincode' => '395001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Dealer::create([
            'name' => 'Bangalore Green Fields',
            'mobile' => '9876543214',
            'email' => 'bangalore@greenfields.com',
            'gstin' => '29ABCDE1234F1Z5',
            'address' => '321 Farm Road',
            'zone_id' => $zone3->id,
            'state_id' => $bangalore->state_id,
            'city_id' => $bangalore->id,
            'pincode' => '560001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Dealer::create([
            'name' => 'Chennai Harvest Mart',
            'mobile' => '9876543215',
            'email' => 'chennai@harvestmart.com',
            'gstin' => '33ABCDE1234F1Z6',
            'address' => '654 Trade Center',
            'zone_id' => $zone4->id,
            'state_id' => $chennai->state_id,
            'city_id' => $chennai->id,
            'pincode' => '600001',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }
}

