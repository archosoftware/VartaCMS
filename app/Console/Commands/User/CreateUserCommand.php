<?php

namespace App\Console\Commands\User;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:user:create {--name=} {--email=} {--password=}')]
#[Description('Create a new user')]
class CreateUserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CreateNewUser $createsNewUsers): int
    {
        $name = $this->stringOption('name') ?? text(label: 'Name', required: true);
        $email = $this->stringOption('email') ?? text(label: 'Email', required: true);
        $password = $this->stringOption('password') ?? password(label: 'Password', required: true);

        try {
            $user = $createsNewUsers->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
            ]);
        } catch (ValidationException $exception) {
            foreach ($exception->validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $this->components->info("User {$user->email} with ID {$user->id} has been created.");

        return self::SUCCESS;
    }

    private function stringOption(string $name): ?string
    {
        $value = $this->option($name);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return $value;
    }
}
