<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Resolvers;

use Modules\Organization\Events\OrganizationDataRequested;
use Modules\Organization\Models\Organization;
use Illuminate\Support\Facades\Event;

class OrganizationExtensionDataResolver
{
    /**
     * Resolve extension data for an organization.
     * This method fires an event that allows other modules to inject data.
     *
     * @param Organization $organization
     * @param array $args
     * @return array
     */
    public function __invoke(Organization $organization, array $args): array
    {
        // Fire event to allow other modules to inject data
        $event = new OrganizationDataRequested($organization);
        Event::dispatch($event);

        return $event->getAllExtensionData();
    }
}
