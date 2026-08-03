<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'admin:create-super-admin
                            {--name=Super Admin : The name of the super admin}
                            {--email= : The email address}
                            {--password= : The plaintext password}';

    protected $description = 'Create the first Super Admin account or update an existing one';

    public function handle(): int
    {
        $email = $this->resolveEmail($this->option('email'));
        $password = $this->resolvePassword($this->option('password'));

        if ($email === null || $password === null) {
            return Command::FAILURE;
        }

        $name = $this->option('name') ?: 'Super Admin';

        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (! $superAdminRole) {
            $this->error('The "super_admin" role does not exist. Run "php artisan db:seed --class=RolesSeeder" first.');

            return Command::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'password' => Hash::make($password),
            ]);
            $this->info('Existing user updated to Super Admin.');
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
            ]);
            $this->info('Super Admin account created successfully.');
        }

        $this->line('');
        $this->line('--- Super Admin ---');
        $this->line("Name:  {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line('-------------------');

        return Command::SUCCESS;
    }

    protected function resolveEmail(?string $email): ?string
    {
        while (! $this->isValidEmail($email)) {
            if ($email === null || $email === '') {
                $this->error('The email address is required.');
            } else {
                $this->error('The email address is not valid.');
            }

            if (! $this->input->isInteractive()) {
                return null;
            }

            $email = $this->ask('Email address:');
        }

        return $email;
    }

    protected function resolvePassword(?string $password): ?string
    {
        while (! $this->isStrongPassword($password)) {
            if ($password === null || $password === '') {
                $this->error('The password is required.');
            } else {
                $this->error('The password is too weak. Use at least 12 characters with upper and lower case letters and numbers.');
            }

            if (! $this->input->isInteractive()) {
                return null;
            }

            $password = $this->secret('Password (input is hidden):');
        }

        return $password;
    }

    protected function isValidEmail(?string $email): bool
    {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function isStrongPassword(?string $password): bool
    {
        return is_string($password)
            && strlen($password) >= 12
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }
}
