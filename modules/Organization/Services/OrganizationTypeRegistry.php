<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Services;

use Modules\Organization\Contracts\OrganizationTypeRegistryContract;

/**
 * Service for dynamic registration of organization types.
 */
class OrganizationTypeRegistry implements OrganizationTypeRegistryContract
{
    /**
     * Array mapping types to their classes.
     *
     * @var array<string, string>
     */
    protected array $types = [];

    /**
     * Registers or updates an organization type with its associated class name.
     *
     * @param string $type The name of the organization type to register.
     * @param string $class The fully qualified class name associated with the type.
     */
    public function registerType(string $type, string $class): void
    {
        $this->types[$type] = $class;
    }

    /**
     * Returns the class name associated with the specified organization type.
     *
     * @param string $type The organization type name.
     * @return string|null The class name if the type is registered, or null if not found.
     */
    public function getClass(string $type): ?string
    {
        return $this->types[$type] ?? null;
    }

    /**
     * Returns all registered organization types and their associated class names.
     *
     * @return array<string, string> An associative array mapping type names to class names.
     */
    public function getAllTypes(): array
    {
        return $this->types;
    }

    /**
     * Determines whether the specified organization type is registered.
     *
     * @param string $type The name of the organization type to check.
     * @return bool True if the type is registered; otherwise, false.
     */
    public function hasType(string $type): bool
    {
        return isset($this->types[$type]);
    }
}
