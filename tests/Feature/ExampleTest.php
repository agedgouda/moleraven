<?php

use App\Models\User;

test('home redirects guests to login', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect();
});

test('home redirects authenticated users to party', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('party'));
});
