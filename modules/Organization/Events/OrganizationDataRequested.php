<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Events;

use Modules\Organization\Models\Organization;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when organization data is requested through GraphQL.
 * This allows other modules to inject additional data.
 */
class OrganizationDataRequested
{
    use Dispatchable, SerializesModels;

    /**
     * @var Organization
     */
    public $organization;

    /**
     * @var array
     */
    public $extensionData;

    /**
     * Create a new event instance.
     *
     * @param Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        $this->extensionData = [];
    }

    /**
     * Add extension data from a module.
     *
     * @param string $moduleName
     * @param array $data
     */
    public function addExtensionData(string $moduleName, array $data): void
    {
        $this->extensionData[$moduleName] = $data;
    }

    /**
     * Get extension data for a specific module.
     *
     * @param string $moduleName
     * @return array|null
     */
    public function getExtensionData(string $moduleName): ?array
    {
        return $this->extensionData[$moduleName] ?? null;
    }

    /**
     * Get all extension data.
     *
     * @return array
     */
    public function getAllExtensionData(): array
    {
        return $this->extensionData;
    }
}
