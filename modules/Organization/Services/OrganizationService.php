<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Services;

use Illuminate\Support\Facades\DB;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationAddress;

/**
 * Service for handling organization-related operations.
 */
class OrganizationService
{
    /**
     * Create a new organization.
     */
    public function createOrganization(array $data): Organization
    {
        return DB::transaction(function () use ($data) {
            // Create the organization
            $organization = new Organization();
            $organization->name = $data['name'];
            $organization->fantasy_name = $data['fantasy_name'] ?? null;
            $organization->cnpj = $data['cnpj'];
            $organization->description = $data['description'] ?? null;
            $organization->email = $data['email'];
            $organization->phone = $data['phone'] ?? null;
            $organization->website = $data['website'] ?? null;
            $organization->active = $data['active'] ?? true;
            $organization->save();

            // If an address was provided, create it
            if (isset($data['address'])) {
                $this->createOrganizationAddress(
                    $organization->id,
                    (array) $data['address']
                );
            }

            return $organization;
        });
    }

    /**
     * Create an address for an organization.
     */
    public function createOrganizationAddress(int $organizationId, array $addressData): OrganizationAddress
    {
        $address = new OrganizationAddress();
        $address->organization_id = $organizationId;
        $address->type = $addressData['type'] ?? 'headquarters';
        $address->street = $addressData['street'];
        $address->number = $addressData['number'] ?? null;
        $address->complement = $addressData['complement'] ?? null;
        $address->neighborhood = $addressData['neighborhood'];
        $address->city = $addressData['city'];
        $address->state = $addressData['state'];
        $address->zip_code = $addressData['zipCode'] ?? $addressData['zip_code'] ?? null;
        $address->country = $addressData['country'] ?? 'BR';
        $address->active = $addressData['active'] ?? true;
        $address->save();

        return $address;
    }

    /**
     * Update an organization.
     */
    public function updateOrganization(int $id, array $data): Organization
    {
        $organization = Organization::findOrFail($id);

        if (isset($data['name'])) {
            $organization->name = $data['name'];
        }

        if (isset($data['fantasy_name'])) {
            $organization->fantasy_name = $data['fantasy_name'];
        }

        if (isset($data['cnpj'])) {
            $organization->cnpj = $data['cnpj'];
        }

        if (isset($data['description'])) {
            $organization->description = $data['description'];
        }

        if (isset($data['email'])) {
            $organization->email = $data['email'];
        }

        if (isset($data['phone'])) {
            $organization->phone = $data['phone'];
        }

        if (isset($data['website'])) {
            $organization->website = $data['website'];
        }

        if (isset($data['active'])) {
            $organization->active = (bool) $data['active'];
        }

        $organization->save();

        return $organization;
    }
}
