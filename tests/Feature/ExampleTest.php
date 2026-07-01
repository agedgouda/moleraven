<?php

use App\Models\User;

test('home is publicly accessible', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('home is accessible to authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
});
