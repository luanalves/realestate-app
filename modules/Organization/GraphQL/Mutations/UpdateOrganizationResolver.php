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
     * Constructor.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Update an existing organization.
     *
     * @param null $root
     * @param array $args The input arguments
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
