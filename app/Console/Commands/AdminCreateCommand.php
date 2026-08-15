<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Bootstraps the very first (or an additional) admin account from the server
 * terminal. This is the ONLY way to create a staff account now that the
 * public/admin-gated /register route has been removed entirely — see
 * project security audit, 2026-08-15. Intended to be run once during initial
 * server setup, not as a routine user-management tool (there is no "manage
 * users" admin UI yet; build one with proper policy checks before this
 * command needs to be run often).
 */
class AdminCreateCommand extends Command
{
    protected $signature = 'admin:create {--email=} {--name=}';

    protected $description = 'Create an admin user (one-off, run during initial server setup)';

    public function handle(): int
    {
        // Guards against an accidental run in an environment where it makes
        // no sense (staging/testing snapshots with unfamiliar data, etc.) —
        // only the two environments this command is actually meant for.
        if (!app()->environment(['local', 'production'])) {
            $this->error('This command can only run when APP_ENV is "local" or "production" (current: '.app()->environment().').');

            return self::FAILURE;
        }

        $email = $this->option('email') ?: $this->ask('Email');
        $name = $this->option('name') ?: $this->ask('Name');

        $validator = Validator::make(
            ['email' => $email, 'name' => $name],
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'name' => ['required', 'string', 'max:255'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        // Existing-admin guard: catches BOTH 'admin' and 'super_admin' —
        // AdminMiddleware treats them as equally privileged, so either one
        // already existing means this isn't really a "first admin" bootstrap
        // anymore and deserves an explicit confirmation, not a silent add.
        $existingAdmins = User::whereIn('role', ['admin', 'super_admin'])->count();

        if ($existingAdmins > 0) {
            $this->warn("Warning: {$existingAdmins} admin account(s) already exist in this database.");
            $this->warn('Creating another one is unusual outside initial setup — double-check this is intentional.');

            if (!$this->confirm('Continue and create a new admin anyway?', false)) {
                $this->info('Aborted. No user was created.');

                return self::SUCCESS;
            }
        }

        $password = $this->secret('Password (min. 8 characters)');
        $passwordConfirmation = $this->secret('Confirm password');

        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        $passwordValidator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:8']]
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Admin account created: {$user->email}");

        return self::SUCCESS;
    }
}
