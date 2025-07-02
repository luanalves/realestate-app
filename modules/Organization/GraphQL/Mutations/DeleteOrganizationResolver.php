<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use Modules\Organization\Models\Organization;
use Modules\Organization\Services\OrganizationService;

/**
 * Resolver for deleting an organization.
 */
class DeleteOrganizationResolver
{
    /**
     * The organization service.
     */
    private OrganizationService $organizationService;

    /**
     * Initializes the resolver with the given organization service.
     *
     * @param OrganizationService $organizationService Service used to manage organizations.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Deletes an organization by its ID.
     *
     * @param null $root Unused root value from GraphQL resolver signature.
     * @param array $args Arguments containing the 'id' of the organization to delete.
     * @return Organization|null The deleted Organization object, or null if not found or deletion failed.
     */
    public function __invoke($root, array $args): ?Organization
    {
        $id = $args['id'];

        return $this->organizationService->deleteOrganization((int) $id);
    }
}
