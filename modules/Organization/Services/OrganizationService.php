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
     * Creates a new organization and optionally its address within a database transaction.
     *
     * If address data is provided in the input, an associated organization address is also created.
     *
     * @param array $data Organization data, optionally including an 'address' key for address creation.
     * @return Organization The newly created organization instance.
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
     * Creates and saves a new address associated with the specified organization.
     *
     * @param int $organizationId The ID of the organization to associate with the address.
     * @param array $addressData Address details, including street, city, state, and optional fields such as type, number, complement, zip code, country, and active status.
     * @return OrganizationAddress The newly created organization address.
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
     * Updates the specified organization with provided data fields.
     *
     * Only fields present in the input array are updated. Returns the updated Organization instance.
     *
     * @param int $id The ID of the organization to update.
     * @param array $data Associative array of fields to update.
     * @return Organization The updated organization instance.
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

    /**
     * Deletes an organization by its ID, including all associated addresses and memberships.
     *
     * Returns a copy of the deleted organization, or null if the organization does not exist.
     *
     * @param int $id The ID of the organization to delete.
     * @return Organization|null The deleted organization instance, or null if not found.
     */
    public function deleteOrganization(int $id): ?Organization
    {
        return DB::transaction(function () use ($id) {
            $organization = Organization::find($id);
            
            if (!$organization) {
                return null;
            }
            
            // Armazena uma cópia dos dados da organização antes de deletar
            $deletedOrg = clone $organization;
            
            // Exclui os endereços associados
            $organization->addresses()->delete();
            
            // Exclui os membros associados
            $organization->memberships()->delete();
            
            // Exclui a organização
            $organization->delete();
            
            return $deletedOrg;
        });
    }
}
