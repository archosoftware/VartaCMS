<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('creates a user from options', function () {
    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
    ])
        ->expectsOutputToContain('User [1] ada@example.com created.')
        ->assertSuccessful();

    $user = User::query()->where('email', 'ada@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user?->name)->toBe('Ada Lovelace');

    $this->assertTrue(Hash::check('password', $user?->password));
});

test('rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
    ])
        ->expectsOutputToContain('The email has already been taken.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 1);
});

test('rejects an invalid email address', function () {
    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'not-an-email',
        '--password' => 'password',
    ])
        ->expectsOutputToContain('The email field must be a valid email address.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
