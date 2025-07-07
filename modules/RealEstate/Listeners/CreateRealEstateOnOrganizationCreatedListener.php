<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Listeners;

use Modules\Organization\Events\OrganizationCreated;
use Modules\RealEstate\Services\RealEstateCreationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener that creates RealEstate records when organizations are created with realEstate extensionData.
 */
class CreateRealEstateOnOrganizationCreatedListener
{
    private RealEstateCreationService $realEstateService;

    public function __construct(RealEstateCreationService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Handle the event.
     *
     * @param OrganizationCreated $event
     * @return void
     * @throws \Exception
     */
    public function handle(OrganizationCreated $event): void
    {
        // Verificar se há dados de RealEstate no extensionData
        if (!isset($event->extensionData['realEstate'])) {
            return;
        }

        $realEstateData = $event->extensionData['realEstate'];
        
        // Extrair o organization_id dos baseData
        $organizationId = $event->baseData['id'];

        try {
            // Criar o registro RealEstate
            $this->realEstateService->createFromOrganization(
                $organizationId,
                $realEstateData,
                $event->userId
            );
        } catch (\Exception $e) {
            // Log error e re-throw para falhar a transação principal
            \Log::error('Failed to create RealEstate record', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
                'real_estate_data' => $realEstateData,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw para que a transação principal seja revertida
            throw new \Exception("Failed to create RealEstate extension: " . $e->getMessage(), 0, $e);
        }
    }
}
