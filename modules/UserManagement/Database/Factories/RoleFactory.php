<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Modules\UserManagement\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\UserManagement\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = RolesSeeder::ROLES;
        $roleName = $this->faker->randomElement(array_keys($roles));

        return [
            'name' => $roleName,
            'description' => $roles[$roleName],
        ];
    }

    /**
     * Create a super admin role.
     */
    public function superAdmin(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_SUPER_ADMIN,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_SUPER_ADMIN],
        ]);
    }

    /**
     * Create a real estate admin role.
     */
    public function realEstateAdmin(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_REAL_ESTATE_ADMIN],
        ]);
    }

    /**
     * Create a real estate agent role.
     */
    public function realEstateAgent(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_REAL_ESTATE_AGENT,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_REAL_ESTATE_AGENT],
        ]);
    }

    /**
     * Create a client role.
     */
    public function client(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_CLIENT,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_CLIENT],
        ]);
    }
}
