<?php

use App\Models\User;

test('guests are redirected to the admin login page', function () {
    $this->get(route('filament.admin.pages.dashboard'))
        ->assertRedirectToRoute('filament.admin.auth.login');
});

test('admin users can visit the admin panel', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertOk();
});

test('non-admin users cannot visit the admin panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertForbidden();
});
