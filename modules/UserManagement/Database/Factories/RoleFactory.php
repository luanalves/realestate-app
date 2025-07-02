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
     * Returns the default attributes for a Role model instance with a randomly selected role name and its corresponding description.
     *
     * @return array<string, mixed> An associative array containing the 'name' and 'description' attributes for the Role.
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
     * Configures the factory to generate a "super admin" role with the appropriate name and description.
     *
     * @return self The factory instance with the "super admin" state applied.
     */
    public function superAdmin(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_SUPER_ADMIN,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_SUPER_ADMIN],
        ]);
    }

    /**
     * Configures the factory to generate a role with the "real estate admin" attributes.
     *
     * @return self The factory instance with the "real estate admin" state applied.
     */
    public function realEstateAdmin(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_REAL_ESTATE_ADMIN],
        ]);
    }

    /**
     * Configures the factory to generate a role with the "real estate agent" attributes.
     *
     * @return self The factory instance with the "real estate agent" state applied.
     */
    public function realEstateAgent(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_REAL_ESTATE_AGENT,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_REAL_ESTATE_AGENT],
        ]);
    }

    /**
     * Configures the factory to generate a role with the "client" designation.
     *
     * @return self The factory instance with the "client" role state applied.
     */
    public function client(): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => RolesSeeder::ROLE_CLIENT,
            'description' => RolesSeeder::ROLES[RolesSeeder::ROLE_CLIENT],
        ]);
    }
}
