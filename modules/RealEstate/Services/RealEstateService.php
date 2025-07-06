<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Models\RealEstateAddress;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class RealEstateService
{
    /**
     * Check if user has permission to access real estate management.
     */
    public function authorizeRealEstateAccess(?object $user = null): object
    {
        $user = $user ?: Auth::guard('api')->user();

        if (!$user) {
            throw new AuthenticationException('You need to be authenticated to access real estate data');
        }

        return $user;
    }

    /**
     * Check if user has permission to modify real estate entities.
     */
    public function authorizeRealEstateWrite(?object $user = null): object
    {
        $user = $this->authorizeRealEstateAccess($user);

        // Only users with appropriate roles can modify real estate data
        if (!$user->role || !in_array($user->role->name, [
            RolesSeeder::ROLE_SUPER_ADMIN,
            RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
        ])) {
            throw new AuthenticationException('You do not have permission to modify real estate agencies');
        }

        return $user;
    }

    /**
     * Check if user can access a specific real estate entity (multi-tenant check).
     */
    public function authorizeRealEstateEntityAccess(RealEstate $realEstate, ?object $user = null): void
    {
        $user = $this->authorizeRealEstateAccess($user);

        // Super admins can access all entities
        if ($user->role && $user->role->name === RolesSeeder::ROLE_SUPER_ADMIN) {
            return;
        }

        // For other users, check tenant restrictions
        if ($user->tenant_id && $user->tenant_id !== $realEstate->tenant_id) {
            throw new AuthenticationException('You do not have permission to access this real estate agency');
        }
    }

    /**
     * Create a new real estate with address.
     *
     * This method handles the creation of both the Organization and RealEstate records
     * in a single database transaction, ensuring data consistency.
     */
    public function createRealEstate(array $data, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);

        try {
            // Set tenant_id if user is not a super admin
            if ($user->role && $user->role->name !== RolesSeeder::ROLE_SUPER_ADMIN && $user->tenant_id) {
                $data['tenant_id'] = $user->tenant_id;
            }

            // Extract address data if present
            $addressData = $data['address'] ?? null;
            unset($data['address']);

            // Extract RealEstate specific fields
            $realEstateData = [
                'creci' => $data['creci'] ?? null,
                'state_registration' => $data['stateRegistration'] ?? null,
            ];

            // Remove RealEstate specific fields from Organization data
            unset($data['creci'], $data['stateRegistration']);

            // Create both Organization and RealEstate in a transaction
            return DB::transaction(function () use ($data, $addressData, $realEstateData) {
                // First create the organization
                $organization = Organization::create($data);

                // Then create the real estate com a referência para organization_id
                $realEstateData['organization_id'] = $organization->id;
                $realEstate = RealEstate::create($realEstateData);

                // Create address if provided
                if ($addressData) {
                    $this->createRealEstateAddress($realEstate->id, $addressData);
                }

                // Load the organization data and addresses for the return value
                $realEstate->load(['organization', 'addresses']);

                return $realEstate;
            });
        } catch (\Exception $e) {
            Log::error('Error creating real estate agency', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing real estate with address.
     *
     * This method updates both the Organization and RealEstate records
     * in a single database transaction, ensuring data consistency.
     */
    public function updateRealEstate(int $id, array $data, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);
        $realEstate = RealEstate::with('organization')->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        try {
            // Extract address data if present
            $addressData = $data['address'] ?? null;
            unset($data['address']);

            // Don't allow changing tenant_id for non-super-admin users
            if ($user->role && $user->role->name !== RolesSeeder::ROLE_SUPER_ADMIN) {
                unset($data['tenant_id']);
            }

            // Separate Organization data from RealEstate data
            $realEstateData = [
                'creci' => $data['creci'] ?? null,
                'state_registration' => $data['stateRegistration'] ?? null,
            ];

            // Remove RealEstate specific fields from Organization data
            unset($data['creci'], $data['stateRegistration']);

            // Update both Organization and RealEstate in a transaction
            return DB::transaction(function () use ($realEstate, $data, $addressData, $realEstateData) {
                // Update the organization data
                if (!empty($data)) {
                    $realEstate->organization->update($data);
                }

                // Update the real estate data
                if (!empty($realEstateData)) {
                    $realEstate->update($realEstateData);
                }

                // Update address if provided
                if ($addressData) {
                    $this->updateRealEstateAddress($realEstate, $addressData);
                }

                $realEstate->load(['organization', 'addresses']);

                return $realEstate;
            });
        } catch (\Exception $e) {
            Log::error('Error updating real estate agency', [
                'id' => $realEstate->id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Delete a real estate agency.
     *
     * This method deletes both the RealEstate and Organization records
     * in a single database transaction, relying on foreign key cascade for consistency.
     * The Organization deletion will automatically cascade to the RealEstate record
     * due to the foreign key constraint.
     */
    public function deleteRealEstate(int $id, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);
        $realEstate = RealEstate::with(['organization', 'addresses'])->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        // Store reference before deletion for return value
        $deletedRealEstate = clone $realEstate;

        try {
            // Delete in a transaction
            DB::transaction(function () use ($realEstate) {
                // Delete the addresses first
                if ($realEstate->addresses) {
                    $realEstate->addresses()->delete();
                }

                // Get the organization ID before deleting
                $organizationId = $realEstate->organization_id;

                // Primeiro exclua o registro RealEstate
                $realEstate->delete();

                // Depois exclua a organização (se necessário)
                Organization::destroy($organizationId);
            });

            return $deletedRealEstate;
        } catch (\Exception $e) {
            Log::error('Error deleting real estate agency', [
                'id' => $realEstate->id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Create an address for a real estate agency.
     */
    private function createRealEstateAddress(int $realEstateId, array $addressData): RealEstateAddress
    {
        $addressData['real_estate_id'] = $realEstateId;
        $addressData['type'] = $addressData['type'] ?? 'headquarters';
        $addressData['active'] = $addressData['active'] ?? true;

        if (isset($addressData['zip_code'])) {
            $addressData['zip_code'] = str_replace('-', '', $addressData['zip_code']);
        }

        return RealEstateAddress::create($addressData);
    }

    /**
     * Update an existing address or create a new one if none exists.
     */
    private function updateRealEstateAddress(RealEstate $realEstate, array $addressData): RealEstateAddress
    {
        $address = $realEstate->headquarters ?? $realEstate->addresses()->first();

        if ($address) {
            // Update existing address
            $address->update($addressData);

            return $address;
        } else {
            // Create new address if none exists
            return $this->createRealEstateAddress($realEstate->id, $addressData);
        }
    }

    /**
     * Create a new address for an existing real estate.
     */
    public function createRealEstateAddressForExisting(int $realEstateId, array $addressData, ?object $user = null): RealEstateAddress
    {
        $user = $this->authorizeRealEstateWrite($user);
        $realEstate = RealEstate::findOrFail($realEstateId);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        try {
            $address = $this->createRealEstateAddress($realEstateId, $addressData);

            return $address;
        } catch (\Exception $e) {
            Log::error('Error creating real estate address', [
                'error' => $e->getMessage(),
                'real_estate_id' => $realEstateId,
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing real estate address.
     */
    public function updateRealEstateAddressById(int $id, array $addressData, ?object $user = null): RealEstateAddress
    {
        $user = $this->authorizeRealEstateWrite($user);
        $address = RealEstateAddress::findOrFail($id);

        // Check if user can access the real estate this address belongs to
        $this->authorizeRealEstateEntityAccess($address->realEstate, $user);

        try {
            // Processando os dados do endereço
            if (isset($addressData['zip_code'])) {
                $addressData['zip_code'] = str_replace('-', '', $addressData['zip_code']);
            }

            $address->update($addressData);

            return $address;
        } catch (\Exception $e) {
            Log::error('Error updating real estate address', [
                'error' => $e->getMessage(),
                'address_id' => $id,
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Delete an existing real estate address.
     */
    public function deleteRealEstateAddress(int $id, ?object $user = null): RealEstateAddress
    {
        $user = $this->authorizeRealEstateWrite($user);
        $address = RealEstateAddress::findOrFail($id);

        // Check if user can access the real estate this address belongs to
        $this->authorizeRealEstateEntityAccess($address->realEstate, $user);

        // Store reference before deletion for return value
        $deletedAddress = clone $address;

        try {
            // Delete the address
            $address->delete();

            return $deletedAddress;
        } catch (\Exception $e) {
            Log::error('Error deleting real estate address', [
                'error' => $e->getMessage(),
                'address_id' => $id,
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Get a real estate agency by ID.
     */
    public function getRealEstateById(int $id, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateAccess($user);
        $realEstate = RealEstate::with(['organization.addresses'])->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        return $realEstate;
    }
}
