<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountryStateCitySeeder::class,
            ZoneSeeder::class,
            OrganizationSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ProductStateRateSeeder::class,
            DealerSeeder::class,
            DiscountSlabSeeder::class,
        ]);
    }
}
