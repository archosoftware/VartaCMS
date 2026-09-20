<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:user:delete {id} {--force : Delete the user without confirmation}')]
#[Description('Delete a user by ID')]
class DeleteUserCommand extends Command
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

        if (! $this->option('force') && ! $this->confirm("Delete user [{$user->email}]?")) {
            $this->components->warn('User was not deleted.');

            return self::SUCCESS;
        }

        $user->delete();

        $this->components->info("User [{$user->id}] {$user->email} deleted.");

        return self::SUCCESS;
    }
}
