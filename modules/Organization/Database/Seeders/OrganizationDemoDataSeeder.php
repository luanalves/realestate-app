<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Database\Seeders;

use Modules\UserManagement\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Organization\Models\Organization;
use Modules\Organization\Support\OrganizationConstants;

class OrganizationDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo organizations with members and addresses...');

        // Create demo users if they don't exist
        $adminUser = $this->createUserIfNotExists([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $managerUser = $this->createUserIfNotExists([
            'name' => 'Demo Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
        ]);

        $memberUser = $this->createUserIfNotExists([
            'name' => 'Demo Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create demo organizations
        $organizations = [
            [
                'name' => 'Demo Organization 1',
                'description' => 'This is a demo organization for testing purposes',
                'addresses' => [
                    [
                        'type' => OrganizationConstants::ADDRESS_TYPE_HEADQUARTERS,
                        'street' => 'Main Street',
                        'number' => '123',
                        'complement' => 'Suite 100',
                        'neighborhood' => 'Downtown',
                        'city' => 'Demo City',
                        'state' => 'DS',
                        'country' => 'Demo Country',
                        'zip_code' => '12345678',
                    ],
                ],
                'members' => [
                    ['user_id' => $adminUser->id, 'role' => OrganizationConstants::ROLE_ADMIN],
                    ['user_id' => $managerUser->id, 'role' => OrganizationConstants::ROLE_MANAGER],
                    ['user_id' => $memberUser->id, 'role' => OrganizationConstants::ROLE_MEMBER],
                ],
            ],
            [
                'name' => 'Demo Organization 2',
                'description' => 'Another demo organization with branch offices',
                'addresses' => [
                    [
                        'type' => OrganizationConstants::ADDRESS_TYPE_HEADQUARTERS,
                        'street' => 'Corporate Avenue',
                        'number' => '500',
                        'complement' => 'Floor 20',
                        'neighborhood' => 'Business District',
                        'city' => 'Enterprise City',
                        'state' => 'EC',
                        'country' => 'Demo Country',
                        'zip_code' => '54321876',
                    ],
                    [
                        'type' => OrganizationConstants::ADDRESS_TYPE_BRANCH,
                        'street' => 'Branch Street',
                        'number' => '50',
                        'complement' => '',
                        'neighborhood' => 'Suburban Area',
                        'city' => 'Branch Town',
                        'state' => 'BT',
                        'country' => 'Demo Country',
                        'zip_code' => '98765432',
                    ],
                ],
                'members' => [
                    ['user_id' => $adminUser->id, 'role' => OrganizationConstants::ROLE_ADMIN],
                    ['user_id' => $memberUser->id, 'role' => OrganizationConstants::ROLE_MEMBER],
                ],
            ],
        ];

        foreach ($organizations as $organizationData) {
            $addresses = $organizationData['addresses'];
            $members = $organizationData['members'];

            unset($organizationData['addresses'], $organizationData['members']);

            // Check if organization already exists
            $organization = Organization::where('name', $organizationData['name'])->first();

            if (!$organization) {
                // Create the organization if it doesn't exist
                $organization = Organization::create($organizationData);
                $this->command->info("Created organization: {$organization->name}");
            } else {
                $this->command->info("Organization already exists: {$organization->name}");
            }            // Check if addresses already exist
            if ($organization->addresses()->count() === 0) {
                // Add addresses
                foreach ($addresses as $addressData) {
                    $organization->addresses()->create($addressData);
                    $this->command->info("Added address to {$organization->name}");
                }
            } else {
                $this->command->info("Addresses already exist for {$organization->name}");
            }

            // Check if members already exist
            foreach ($members as $memberData) {
                $exists = $organization->members()->where('user_id', $memberData['user_id'])->exists();

                if (!$exists) {
                    $organization->members()->attach($memberData['user_id'], ['role' => $memberData['role']]);
                    $this->command->info("Added member with role {$memberData['role']} to {$organization->name}");
                } else {
                    $this->command->info("Member already exists in {$organization->name}");
                }
            }
        }

        $this->command->info('Demo organizations created successfully!');
    }

    /**
     * Create a user if it doesn't exist.
     */
    private function createUserIfNotExists(array $userData): User
    {
        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            $user = User::create($userData);
            $this->command->info("Created user: {$user->email}");
        } else {
            $this->command->info("User already exists: {$user->email}");
        }

        return $user;
    }
}
