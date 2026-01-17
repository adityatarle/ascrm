<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed users with roles.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $accountantRole = Role::create(['name' => 'accountant', 'guard_name' => 'web']);
        $salesOfficerRole = Role::create(['name' => 'sales_officer', 'guard_name' => 'web']);
        $dispatchOfficerRole = Role::create(['name' => 'dispatch_officer', 'guard_name' => 'web']);

        $org1 = Organization::where('gstin', '27AABCU9601R1ZM')->first();
        $org2 = Organization::where('gstin', '24AABCU9601R1ZN')->first();

        // Organization 1 users
        $admin1 = User::create([
            'organization_id' => $org1->id,
            'name' => 'Admin User',
            'email' => 'admin@aglichemtech-mh.com',
            'mobile' => '9876543210',
            'password' => bcrypt('password'),
        ]);
        $admin1->assignRole($adminRole);

        $accountant1 = User::create([
            'organization_id' => $org1->id,
            'name' => 'Accountant User',
            'email' => 'accountant@aglichemtech-mh.com',
            'mobile' => '9876543211',
            'password' => bcrypt('password'),
        ]);
        $accountant1->assignRole($accountantRole);

        // Organization 2 users
        $salesOfficer2 = User::create([
            'organization_id' => $org2->id,
            'name' => 'Sales Officer',
            'email' => 'sales@aglichemtech-gj.com',
            'mobile' => '9876543212',
            'password' => bcrypt('password'),
        ]);
        $salesOfficer2->assignRole($salesOfficerRole);

        $dispatchOfficer2 = User::create([
            'organization_id' => $org2->id,
            'name' => 'Dispatch Officer',
            'email' => 'dispatch@aglichemtech-gj.com',
            'mobile' => '9876543213',
            'password' => bcrypt('password'),
        ]);
        $dispatchOfficer2->assignRole($dispatchOfficerRole);
    }
}

