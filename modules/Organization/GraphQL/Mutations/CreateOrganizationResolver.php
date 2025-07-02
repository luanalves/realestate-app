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
 * Resolver for creating a new generic organization.
 */
class CreateOrganizationResolver
{
    /**
     * The organization service.
     */
    private OrganizationService $organizationService;

    /**
     * Initializes the resolver with the provided organization service.
     *
     * @param OrganizationService $organizationService Service for organization-related operations.
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Handles the GraphQL mutation to create a new organization with optional address information.
     *
     * Accepts input data for the organization, creates the organization record within a database transaction,
     * and, if address data is provided, associates an address with the new organization.
     *
     * @param null $root
     * @param array $args Arguments containing the 'input' data for the organization.
     * @return Organization The newly created organization instance.
     */
    public function __invoke($root, array $args): Organization
    {
        $input = $args['input'];

        // Execute in a transaction to ensure data consistency
        return DB::transaction(function () use ($input) {
            // 1. Create the base organization
            $organization = new Organization();
            $organization->name = $input['name'];
            $organization->fantasy_name = $input['fantasy_name'] ?? null;
            $organization->cnpj = $input['cnpj'];
            $organization->description = $input['description'] ?? null;
            $organization->email = $input['email'];
            $organization->phone = $input['phone'] ?? null;
            $organization->website = $input['website'] ?? null;
            $organization->active = $input['active'] ?? true;
            $organization->save();

            // 2. If an address was provided, create it
            if (isset($input['address'])) {
                $this->organizationService->createOrganizationAddress(
                    $organization->id,
                    (array) $input['address']
                );
            }

            return $organization;
        });
    }
}
