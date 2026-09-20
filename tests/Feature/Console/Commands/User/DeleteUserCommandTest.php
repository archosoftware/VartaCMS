<?php

use App\Models\User;

test('deletes a user when forced', function () {
    $user = User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('app:user:delete', [
        'id' => $user->id,
        '--force' => true,
    ])
        ->expectsOutputToContain("User [{$user->id}] ada@example.com deleted.")
        ->assertSuccessful();

    $this->assertModelMissing($user);
});

test('deletes a user after confirmation', function () {
    $user = User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('app:user:delete', ['id' => $user->id])
        ->expectsConfirmation('Delete user [ada@example.com]?', 'yes')
        ->expectsOutputToContain("User [{$user->id}] ada@example.com deleted.")
        ->assertSuccessful();

    $this->assertModelMissing($user);
});

test('does not delete a user when confirmation is declined', function () {
    $user = User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('app:user:delete', ['id' => $user->id])
        ->expectsConfirmation('Delete user [ada@example.com]?', 'no')
        ->expectsOutputToContain('User was not deleted.')
        ->assertSuccessful();

    $this->assertModelExists($user);
});

test('fails when the user does not exist', function () {
    $this->artisan('app:user:delete', ['id' => 999])
        ->expectsOutputToContain('User [999] was not found.')
        ->assertFailed();
});
