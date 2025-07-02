<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;
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
     * Initializes the resolver with the given organization service.
     *
     * @param OrganizationService $organizationService Service used to perform organization updates.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Updates an existing organization with the provided input data.
     *
     * Executes the update operation within a database transaction to ensure data consistency.
     *
     * @param null $root Unused GraphQL root value.
     * @param array $args Arguments containing the organization ID and input data.
     * @return Organization The updated organization model instance.
     */
    public function __invoke($root, array $args): Organization
    {
        $id = $args['id'];
        $input = $args['input'];

        // Execute in a transaction to ensure data consistency
        return DB::transaction(function () use ($id, $input) {
            return $this->organizationService->updateOrganization((int) $id, $input);
        });
    }
}
