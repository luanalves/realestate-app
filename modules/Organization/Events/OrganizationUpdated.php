<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Organization\Models\Organization;

/**
 * Event triggered when an organization is updated.
 */
class OrganizationUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The organization that was updated.
     */
    public Organization $organization;

    /**
     * Extension data for module-specific processing.
     */
    public array $extensionData;

    /**
     * The user who performed the update.
     */
    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Organization $organization, array $extensionData = [], int $userId = 0)
    {
        $this->organization = $organization;
        $this->extensionData = $extensionData;
        $this->userId = $userId;
    }
}
