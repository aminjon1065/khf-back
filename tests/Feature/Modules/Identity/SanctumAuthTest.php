<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns the authenticated user for a valid API token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.uuid', $user->uuid);
});

it('rejects unauthenticated API requests', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});

it('authenticates through the Sanctum guard via actingAs', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/user')->assertOk()->assertJsonPath('data.email', $user->email);
});
