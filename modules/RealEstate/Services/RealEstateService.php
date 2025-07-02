<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Models\RealEstateAddress;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class RealEstateService
{
    /**
     * Ensures the user is authenticated for real estate management access.
     *
     * Returns the authenticated user object if access is granted.
     *
     * @param object|null $user The user to authorize, or null to use the current API user.
     * @return object The authenticated user.
     * @throws \Illuminate\Auth\AuthenticationException If no user is authenticated.
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
     * Ensures the user has write permissions for real estate entities.
     *
     * Verifies that the user is authenticated and has a role of either super admin or real estate admin. Throws an AuthenticationException if the user lacks the required permissions.
     *
     * @param object|null $user The user to authorize, or null to use the currently authenticated user.
     * @return object The authorized user object.
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
     * Ensures the user has access to a specific real estate entity, enforcing multi-tenant restrictions.
     *
     * Grants access to super admins for all entities. For other users, access is allowed only if their tenant ID matches the real estate entity's tenant ID. Throws an AuthenticationException if access is denied.
     *
     * @param RealEstate $realEstate The real estate entity to check access for.
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
     * Creates a new real estate entity, optionally with an associated address.
     *
     * If the user is not a super admin, the real estate is assigned the user's tenant ID. Address data, if provided, is extracted and used to create an address linked to the new real estate entity. Returns the created real estate entity with its addresses loaded.
     *
     * @param array $data The data for the new real estate entity, optionally including an 'address' key with address details.
     * @return RealEstate The created real estate entity with addresses loaded.
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

            // Create the real estate agency
            $realEstate = RealEstate::create($data);

            // Create address if provided
            if ($addressData) {
                $this->createRealEstateAddress($realEstate->id, $addressData);
            }
            // Load addresses relationship for return value
            $realEstate->load('addresses');

            return $realEstate;
        } catch (\Exception $e) {
            Log::error('Error creating real estate agency', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Updates an existing real estate entity and its address.
     *
     * If address data is provided, updates the associated address or creates one if none exists. Only super admin users can change the tenant ID; for other users, tenant ID changes are ignored. Loads and returns the updated real estate entity with its addresses.
     *
     * @param int $id The ID of the real estate entity to update.
     * @param array $data The data to update, optionally including address information under the 'address' key.
     * @return RealEstate The updated real estate entity with addresses loaded.
     */
    public function updateRealEstate(int $id, array $data, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);
        $realEstate = RealEstate::findOrFail($id);

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

            // Update real estate attributes
            $realEstate->update($data);

            // Update address if provided
            if ($addressData) {
                $this->updateRealEstateAddress($realEstate, $addressData);
            }

            $realEstate->load('addresses');

            return $realEstate;
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
     * Deletes a real estate agency and its associated addresses after verifying user authorization.
     *
     * @param int $id The ID of the real estate agency to delete.
     * @return RealEstate The deleted real estate agency instance.
     */
    public function deleteRealEstate(int $id, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);
        $realEstate = RealEstate::with('addresses')->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        // Store reference before deletion for return value
        $deletedRealEstate = clone $realEstate;

        try {
            // Delete the addresses first (should cascade but being explicit)
            $realEstate->addresses()->delete();

            // Delete the real estate agency
            $realEstate->delete();

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
     * Creates a new address for the specified real estate entity.
     *
     * Sets default values for address type ('headquarters') and active status (true) if not provided, and normalizes the zip code by removing dashes.
     *
     * @param int $realEstateId The ID of the real estate entity to associate with the address.
     * @param array $addressData The address data to be stored.
     * @return RealEstateAddress The newly created address.
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
     * Updates the headquarters address of a real estate entity if it exists, or creates a new address if none is found.
     *
     * @param RealEstate $realEstate The real estate entity whose address will be updated or created.
     * @param array $addressData The address data to update or create.
     * @return RealEstateAddress The updated or newly created address.
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
     * Creates a new address for an existing real estate entity after verifying user authorization and access.
     *
     * @param int $realEstateId The ID of the real estate entity to associate the new address with.
     * @param array $addressData The address details to be created.
     * @return RealEstateAddress The newly created real estate address.
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
     * Updates an existing real estate address by its ID.
     *
     * Normalizes the zip code if provided and applies the updates to the address after verifying user authorization and access to the related real estate entity.
     *
     * @param int $id The ID of the real estate address to update.
     * @param array $addressData The address fields to update.
     * @return RealEstateAddress The updated real estate address.
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
     * Deletes a real estate address by its ID after verifying user authorization.
     *
     * @param int $id The ID of the real estate address to delete.
     * @return RealEstateAddress The deleted address instance.
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
}
