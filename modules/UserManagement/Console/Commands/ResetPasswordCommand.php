<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Models\User;

class ResetPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password 
                           {email : The email of the user}
                           {--password= : Custom password (if not provided, will be generated)}
                           {--show-password : Display the new password in output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset user password by email address';

    /**
     * Resets a user's password by email via the console command.
     *
     * Prompts for confirmation before updating the password. Uses a provided password or generates a secure one if not specified. Displays user details, handles error conditions such as user not found or password validation failure, and provides security recommendations after a successful reset.
     *
     * @return int Exit code: 0 on success, 1 on failure or cancellation.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $customPassword = $this->option('password');
        $showPassword = $this->option('show-password');

        $this->info('🔐 User Password Reset Tool');
        $this->line('');

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User with email '{$email}' not found.");
            $this->line('');
            $this->comment('💡 Available users:');
            $this->showAvailableUsers();

            return 1;
        }

        // Generate or use custom password
        $newPassword = $customPassword ?: $this->generateSecurePassword();

        // Validate password strength
        if (!$this->validatePassword($newPassword)) {
            return 1;
        }

        // Show user information
        $this->info('👤 User found:');
        $this->line("   Name: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line('   Current Role: '.($user->role->name ?? 'No role assigned'));
        $this->line('');

        // Confirm action
        if (!$this->confirm('Do you want to reset the password for this user?')) {
            $this->info('❌ Password reset cancelled.');

            return 0;
        }

        // Update password
        try {
            $user->password = Hash::make($newPassword);
            $user->save();

            $this->info('✅ Password successfully reset!');
            $this->line('');

            if ($showPassword || !$customPassword) {
                $this->comment("🔑 New password: {$newPassword}");
                $this->line('');
                $this->warn('⚠️  Please save this password securely and share it with the user through a secure channel.');
            } else {
                $this->comment('🔑 Password has been set to the value you provided.');
            }

            // Additional security recommendations
            $this->line('');
            $this->comment('🛡️  Security recommendations:');
            $this->comment('   • User should change this password on first login');
            $this->comment('   • Consider implementing password expiration policies');
            $this->comment('   • Monitor login attempts for this user');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to reset password: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Generates a secure random password containing at least one lowercase letter, one uppercase letter, one number, and one symbol.
     *
     * @param int $length The desired length of the generated password. Defaults to 12.
     * @return string The generated secure password.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        // Ensure at least one character from each type
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest randomly
        $allChars = $lowercase.$uppercase.$numbers.$symbols;
        for ($i = 4; $i < $length; ++$i) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password securely
        $passwordArray = str_split($password);
        $length = count($passwordArray);

        // Fisher-Yates shuffle with cryptographically secure randomness
        for ($i = $length - 1; $i > 0; --$i) {
            $j = random_int(0, $i);
            $temp = $passwordArray[$i];
            $passwordArray[$i] = $passwordArray[$j];
            $passwordArray[$j] = $temp;
        }

        return implode('', $passwordArray);
    }

    /**
     * Checks whether the given password meets minimum strength requirements.
     *
     * Validates that the password is at least 8 characters long and contains at least one lowercase letter, one uppercase letter, and one number. Outputs error messages for any failed criteria.
     *
     * @param string $password The password to validate.
     * @return bool True if the password meets all requirements, false otherwise.
     */
    private function validatePassword(string $password): bool
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!empty($errors)) {
            $this->error('❌ Password validation failed:');
            foreach ($errors as $error) {
                $this->line("   • {$error}");
            }

            return false;
        }

        return true;
    }

    /**
     * Displays up to 10 users with their roles in a table for operator reference.
     *
     * If no users exist, outputs a message indicating the absence of users.
     * If exactly 10 users are shown, notes that only the first 10 are displayed.
     */
    private function showAvailableUsers(): void
    {
        $users = User::with('role')
            ->select(['id', 'name', 'email', 'role_id'])
            ->limit(10)
            ->get();

        if ($users->isEmpty()) {
            $this->comment('   No users found in the system.');

            return;
        }

        $tableData = [];
        foreach ($users as $user) {
            $tableData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role->name ?? 'No role',
            ];
        }

        $this->table(['ID', 'Name', 'Email', 'Role'], $tableData);

        if ($users->count() === 10) {
            $this->comment('   (Showing first 10 users only)');
        }
    }
}
