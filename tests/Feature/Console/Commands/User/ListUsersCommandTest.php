<?php

use App\Models\User;

test('lists users in id order', function () {
    $this->freezeTime();

    $older = User::factory()->create([
        'name' => 'Second User',
        'email' => 'second@example.com',
    ]);
    $newer = User::factory()->create([
        'name' => 'First User',
        'email' => 'first@example.com',
    ]);

    $timestamp = now()->toDateTimeString();

    $this->artisan('app:user:list')
        ->expectsTable(
            ['ID', 'Name', 'Email', 'Email Verified At', 'Created At'],
            [
                [$older->id, 'Second User', 'second@example.com', $timestamp, $timestamp],
                [$newer->id, 'First User', 'first@example.com', $timestamp, $timestamp],
            ],
        )
        ->assertSuccessful();
});

test('reports when no users exist', function () {
    $this->artisan('app:user:list')
        ->expectsOutputToContain('No users found.')
        ->assertSuccessful();
});
