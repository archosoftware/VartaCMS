<?php

use App\Models\User;

test('shows public user information', function () {
    $this->freezeTime();

    $user = User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);

    $timestamp = now()->toDateTimeString();

    $this->artisan('app:user:info', ['id' => $user->id])
        ->expectsTable(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', 'Ada Lovelace'],
                ['Email', 'ada@example.com'],
                ['Email Verified At', $timestamp],
                ['Two Factor Confirmed At', ''],
                ['Created At', $timestamp],
                ['Updated At', $timestamp],
            ],
        )
        ->doesntExpectOutputToContain($user->password)
        ->assertSuccessful();
});

test('fails when the user does not exist', function () {
    $this->artisan('app:user:info', ['id' => 999])
        ->expectsOutputToContain('User [999] was not found.')
        ->assertFailed();
});
