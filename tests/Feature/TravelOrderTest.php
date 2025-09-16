<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
        
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_user_can_create_travel_order()
    {
        $travelOrderData = [
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/travel-orders', $travelOrderData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'order_id',
                        'user_id',
                        'requester_name',
                        'destination',
                        'departure_date',
                        'return_date',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('travel_orders', [
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'status' => TravelOrder::STATUS_REQUESTED
        ]);
    }

    public function test_user_can_view_their_travel_orders()
    {
        // Create a travel order
        $travelOrder = TravelOrder::create([
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/travel-orders');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'order_id',
                                'user_id',
                                'requester_name',
                                'destination',
                                'departure_date',
                                'return_date',
                                'status'
                            ]
                        ]
                    ]
                ]);
    }

    public function test_user_can_view_specific_travel_order()
    {
        $travelOrder = TravelOrder::create([
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/travel-orders/{$travelOrder->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'user_id',
                        'requester_name',
                        'destination',
                        'departure_date',
                        'return_date',
                        'status'
                    ]
                ]);
    }

    public function test_user_cannot_view_other_users_travel_orders()
    {
        $otherUser = User::factory()->create();
        
        $travelOrder = TravelOrder::create([
            'user_id' => $otherUser->id,
            'requester_name' => 'Other User',
            'destination' => 'Los Angeles',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/travel-orders/{$travelOrder->id}");

        $response->assertStatus(404);
    }

    public function test_user_can_cancel_their_travel_order()
    {
        $travelOrder = TravelOrder::create([
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10',
            'status' => TravelOrder::STATUS_REQUESTED
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/travel-orders/{$travelOrder->id}/cancel", [
            'cancellation_reason' => 'Change of plans'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => TravelOrder::STATUS_CANCELLED,
            'cancellation_reason' => 'Change of plans'
        ]);
    }

    public function test_admin_can_approve_travel_order()
    {
        $travelOrder = TravelOrder::create([
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10',
            'status' => TravelOrder::STATUS_REQUESTED
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson("/api/admin/travel-orders/{$travelOrder->id}/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => TravelOrder::STATUS_APPROVED
        ]);
    }

    public function test_user_cannot_approve_their_own_travel_order()
    {
        $travelOrder = TravelOrder::create([
            'user_id' => $this->user->id,
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-10',
            'status' => TravelOrder::STATUS_REQUESTED
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson("/api/admin/travel-orders/{$travelOrder->id}/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'You cannot change the status of your own travel order'
                ]);
    }

    public function test_travel_order_validation()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/travel-orders', [
            'requester_name' => '',
            'destination' => '',
            'departure_date' => '2024-01-01', // Past date
            'return_date' => '2024-01-01' // Same as departure
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }

    public function test_unauthenticated_user_cannot_access_travel_orders()
    {
        $response = $this->getJson('/api/travel-orders');
        
        $response->assertStatus(401);
    }
}