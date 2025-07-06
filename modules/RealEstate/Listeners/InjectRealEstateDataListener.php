<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Listeners;

use Modules\Organization\Events\OrganizationDataRequested;
use Modules\RealEstate\Models\RealEstate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener that injects Real Estate data into Organization GraphQL queries.
 */
class InjectRealEstateDataListener
{
    /**
     * Handle the event.
     *
     * @param OrganizationDataRequested $event
     * @return void
     */
    public function handle(OrganizationDataRequested $event): void
    {
        $organization = $event->organization;
        
        // Check if this organization is a real estate
        $realEstate = RealEstate::where('organization_id', $organization->id)->first();
        
        if ($realEstate) {
            $event->addExtensionData('realEstate', [
                'id' => $realEstate->id,
                'creci' => $realEstate->creci,
                'state_registration' => $realEstate->state_registration,
                'created_at' => $realEstate->created_at,
                'updated_at' => $realEstate->updated_at,
            ]);
        }
    }
}
