<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('creates a user from options', function () {
    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
    ])
        ->expectsConfirmation('Is this user an admin?', 'no')
        ->expectsOutputToContain('User ada@example.com with ID 1 has been created.')
        ->assertSuccessful();

    $user = User::query()->where('email', 'ada@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user?->name)->toBe('Ada Lovelace')
        ->and($user?->is_admin)->toBeFalse();

    $this->assertTrue(Hash::check('password', $user?->password));
});

test('creates an admin user when is_admin is passed', function () {
    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
        '--is_admin' => true,
    ])
        ->expectsOutputToContain('User ada@example.com with ID 1 has been created.')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'email' => 'ada@example.com',
        'is_admin' => true,
    ]);
});

test('creates an admin user when the is_admin prompt is accepted', function () {
    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
    ])
        ->expectsConfirmation('Is this user an admin?', 'yes')
        ->expectsOutputToContain('User ada@example.com with ID 1 has been created.')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'email' => 'ada@example.com',
        'is_admin' => true,
    ]);
});

test('rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('app:user:create', [
        '--name' => 'Ada Lovelace',
        '--email' => 'ada@example.com',
        '--password' => 'password',
    ])
        ->expectsConfirmation('Is this user an admin?', 'no')
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
        ->expectsConfirmation('Is this user an admin?', 'no')
        ->expectsOutputToContain('The email field must be a valid email address.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
