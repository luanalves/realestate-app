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
     * Constructor.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Delete an organization.
     *
     * @param null  $root
     * @param array $args The input arguments
     */
    public function __invoke($root, array $args): ?Organization
    {
        $id = $args['id'];

        return $this->organizationService->deleteOrganization((int) $id);
    }
}
