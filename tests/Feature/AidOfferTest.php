<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AidOfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_aid_offer_without_login()
    {
        $payload = [
            'provider_name' => 'Juan Perez',
            'dni' => 'V-12345678',
            'phone' => '+584141234567',
            'title' => 'Ropa de invierno',
            'description' => '5 cajas de ropa limpia.',
            'category' => 'ropa',
            'latitude' => 10.480593,
            'longitude' => -66.903606,
            'address_description' => 'Plaza Venezuela',
        ];

        $response = $this->postJson('/api/v1/aid-offers', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'secure_token'
                     ]
                 ]);

        $this->assertDatabaseHas('aid_offers', [
            'dni' => 'V-12345678'
        ]);
    }

    public function test_can_update_aid_offer_with_valid_secure_token()
    {
        $payload = [
            'provider_name' => 'Juan Perez',
            'dni' => 'V-12345678',
            'phone' => '+584141234567',
            'title' => 'Ropa de invierno',
            'description' => '5 cajas de ropa.',
            'category' => 'ropa',
            'latitude' => 10.480593,
            'longitude' => -66.903606,
            'address_description' => 'Plaza Venezuela',
        ];

        $createResponse = $this->postJson('/api/v1/aid-offers', $payload);
        $offer = $createResponse->json('data');
        $token = $offer['secure_token'];

        // Actualizar estado a reservado
        $updateResponse = $this->withHeaders([
            'X-Secure-Token' => $token
        ])->putJson('/api/v1/aid-offers/' . $offer['id'], [
            'status' => 'reservado'
        ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('aid_offers', [
            'id' => $offer['id'],
            'status' => 'reservado'
        ]);
    }

    public function test_cannot_update_aid_offer_without_token()
    {
        $payload = [
            'provider_name' => 'Juan Perez',
            'dni' => 'V-12345678',
            'phone' => '+584141234567',
            'title' => 'Ropa de invierno',
            'description' => '5 cajas de ropa.',
            'category' => 'ropa',
            'latitude' => 10.480593,
            'longitude' => -66.903606,
            'address_description' => 'Plaza Venezuela',
        ];

        $createResponse = $this->postJson('/api/v1/aid-offers', $payload);
        $offer = $createResponse->json('data');

        $updateResponse = $this->putJson('/api/v1/aid-offers/' . $offer['id'], [
            'status' => 'reservado'
        ]);

        $updateResponse->assertStatus(403);
    }
}
