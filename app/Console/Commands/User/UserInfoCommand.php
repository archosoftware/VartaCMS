<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:user:info {id}')]
#[Description('Show information about a user')]
class UserInfoCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = $this->argument('id');
        $user = User::query()->find($id);

        if ($user === null) {
            $this->components->error("User [{$id}] was not found.");

            return self::FAILURE;
        }

        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Email Verified At', $user->email_verified_at?->toDateTimeString() ?? ''],
                ['Two Factor Confirmed At', $user->two_factor_confirmed_at?->toDateTimeString() ?? ''],
                ['Created At', $user->created_at?->toDateTimeString() ?? ''],
                ['Updated At', $user->updated_at?->toDateTimeString() ?? ''],
            ],
        );

        return self::SUCCESS;
    }
}
