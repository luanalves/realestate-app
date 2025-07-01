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
     * Registers a new organization type.
     *
     * @param string $type  Organization type name
     * @param string $class Class that implements this type
     */
    public function registerType(string $type, string $class): void
    {
        $this->types[$type] = $class;
    }

    /**
     * Gets the class associated with an organization type.
     *
     * @param string $type Type name
     *
     * @return string|null Associated class or null if not found
     */
    public function getClass(string $type): ?string
    {
        return $this->types[$type] ?? null;
    }

    /**
     * Gets all registered types.
     *
     * @return array<string, string> Array with type => class
     */
    public function getAllTypes(): array
    {
        return $this->types;
    }

    /**
     * Checks if a type is registered.
     *
     * @param string $type Type name
     */
    public function hasType(string $type): bool
    {
        return isset($this->types[$type]);
    }
}
