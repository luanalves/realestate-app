<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Organization\Events\OrganizationUpdated;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateUpdateService;

/**
 * Listener to update real estate data when organization is updated.
 */
class UpdateRealEstateOnOrganizationUpdatedListener
{
    /**
     * The real estate update service.
     */
    private RealEstateUpdateService $realEstateUpdateService;

    /**
     * Constructor.
     */
    public function __construct(RealEstateUpdateService $realEstateUpdateService)
    {
        $this->realEstateUpdateService = $realEstateUpdateService;
    }

    /**
     * Handle the organization updated event.
     */
    public function handle(OrganizationUpdated $event): void
    {
        // Check if this organization has real estate data
        $realEstate = RealEstate::where('organization_id', $event->organization->id)->first();

        if (!$realEstate) {
            // Not a real estate organization, nothing to do
            return;
        }

        // Check if extension data contains real estate updates
        if (!isset($event->extensionData['realEstate'])) {
            // No real estate data to update
            return;
        }

        try {
            $realEstateData = $event->extensionData['realEstate'];
            $this->realEstateUpdateService->updateFromOrganization(
                $realEstate->id,
                $realEstateData,
                $event->userId
            );

            Log::info('RealEstate extension updated successfully', [
                'organization_id' => $event->organization->id,
                'real_estate_id' => $realEstate->id,
                'user_id' => $event->userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update RealEstate extension', [
                'organization_id' => $event->organization->id,
                'real_estate_id' => $realEstate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to cause rollback
            throw new \RuntimeException(
                'Failed to update RealEstate extension: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
