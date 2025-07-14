<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Organization\Events\OrganizationUpdated;
use Modules\Organization\Models\Organization;
use Modules\Organization\Services\OrganizationService;

/**
 * Resolver for updating an existing organization.
 */
class UpdateOrganizationResolver
{
    /**
     * The organization service.
     */
    private OrganizationService $organizationService;

    /**
     * Constructor.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Update an existing organization.
     *
     * @param null  $root
     * @param array $args The input arguments
     */
    public function __invoke($root, array $args): Organization
    {
        $id = $args['id'];
        $input = $args['input'];

        // Extract extension data if present
        $extensionData = $input['extensionData'] ?? [];
        unset($input['extensionData']); // Remove from organization data

        // Execute in a transaction to ensure data consistency
        return DB::transaction(function () use ($id, $input, $extensionData) {
            try {
                // Update the organization
                $organization = $this->organizationService->updateOrganization((int) $id, $input);

                // Dispatch event for module extensions if extensionData was provided
                if (!empty($extensionData)) {
                    $userId = Auth::id() ?? 0;
                    Event::dispatch(new OrganizationUpdated($organization, $extensionData, $userId));
                }

                return $organization;
            } catch (\Exception $e) {
                // Log the error and re-throw to trigger rollback
                \Log::error('Failed to update organization', [
                    'organization_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e;
            }
        });
    }
}
