<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test dealer registration via API.
     */
    public function test_dealer_can_register(): void
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);

        $response = $this->postJson('/api/dealers/register', [
            'name' => 'Test Dealer',
            'mobile' => '9876543210',
            'email' => 'test@dealer.com',
            'gstin' => '27ABCDE1234F1Z1',
            'address' => '123 Test Street',
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'dealer' => [
                    'id',
                    'name',
                    'mobile',
                    'email',
                ],
            ]);

        $this->assertDatabaseHas('dealers', [
            'mobile' => '9876543210',
            'email' => 'test@dealer.com',
        ]);
    }

    /**
     * Test dealer login via API.
     */
    public function test_dealer_can_login(): void
    {
        $organization = \App\Models\Organization::factory()->create();
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);

        $dealer = \App\Models\Dealer::factory()->create([
            'mobile' => '9876543210',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $response = $this->postJson('/api/login', [
            'mobile' => '9876543210',
            'password' => 'password123',
            'organization_id' => $organization->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'dealer',
                'organization',
                'token',
            ]);

        $this->assertNotEmpty($response->json('token'));
    }
}

