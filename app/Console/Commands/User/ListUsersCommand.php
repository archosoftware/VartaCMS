<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:user:list')]
#[Description('List all users')]
class ListUsersCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::query()
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'email_verified_at', 'created_at']);

        if ($users->isEmpty()) {
            $this->components->info('No users found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Email Verified At', 'Created At'],
            $users->map(fn (User $user): array => [
                $user->id,
                $user->name,
                $user->email,
                $user->email_verified_at?->toDateTimeString() ?? '',
                $user->created_at?->toDateTimeString() ?? '',
            ]),
        );

        return self::SUCCESS;
    }
}
