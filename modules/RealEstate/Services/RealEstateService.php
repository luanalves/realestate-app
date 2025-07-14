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
            return DB::transaction(function () use ($realEstate, $data, $realEstateData) {
                // Update the organization data
                if (!empty($data)) {
                    $realEstate->organization->update($data);
                }

                // Update the real estate data
                if (!empty($realEstateData)) {
                    $realEstate->update($realEstateData);
                }

                // Note: Address handling is delegated to Organization module
                // if ($addressData) {
                //     // Address update should be handled by Organization module
                // }

                $realEstate->load(['organization']);

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
        $realEstate = RealEstate::with(['organization'])->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        // Store reference before deletion for return value
        $deletedRealEstate = clone $realEstate;

        try {
            // Delete in a transaction
            DB::transaction(function () use ($realEstate) {
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
     * Get a real estate agency by ID.
     */
    public function getRealEstateById(int $id, ?object $user = null): RealEstate
    {
        $user = $this->authorizeRealEstateAccess($user);
        $realEstate = RealEstate::with(['organization'])->findOrFail($id);

        // Check if user can access this real estate
        $this->authorizeRealEstateEntityAccess($realEstate, $user);

        return $realEstate;
    }

    /*
     * NOTE: Address management is delegated to the Organization module.
     * The following methods were removed as they are no longer needed:
     * - createRealEstateAddress()
     * - updateRealEstateAddress()
     * - createRealEstateAddressForExisting()
     * - updateRealEstateAddressById()
     * - deleteRealEstateAddress()
     *
     * NOTE: Real estate creation is delegated to the Organization module.
     * The createRealEstate() method was removed as specialized organizations
     * should be created through the Organization module with extension data.
     *
     * Address operations should be performed through the Organization module
     * since addresses are managed at the organization level.
     */
}
