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
        if ($user->role && !in_array($user->role->name, [
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
     */
    public function createRealEstate(array $data, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateWrite($user);

        try {
            // Set tenant_id if user is not a super admin
            if ($user->role->name !== RolesSeeder::ROLE_SUPER_ADMIN && $user->tenant_id) {
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
     * Update an existing real estate with address.
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
     * Delete a real estate agency.
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
}
