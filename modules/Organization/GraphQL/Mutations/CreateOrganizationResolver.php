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

/**
 * Resolver for creating a new generic organization.
 */
class CreateOrganizationResolver
{
    /**
     * Create a new organization with extension data support.
     *
     * @param null  $root
     * @param array $args The input arguments
     */
    public function __invoke($root, array $args): array
    {
        $input = $args['input'];
        $user = auth()->user();

        // Usar usuário fake se não autenticado (apenas para teste)
        if (!$user) {
            $user = (object) ['id' => 1];
        }

        return DB::transaction(function () use ($input, $user) {
            try {
                // 1. Criar organização base (sem extensionData)
                $organization = Organization::create([
                    'name' => $input['name'],
                    'fantasy_name' => $input['fantasy_name'] ?? null,
                    'cnpj' => $input['cnpj'] ?? null,
                    'description' => $input['description'] ?? null,
                    'email' => $input['email'] ?? null,
                    'phone' => $input['phone'] ?? null,
                    'website' => $input['website'] ?? null,
                    'active' => $input['active'] ?? true,
                ]);

                // 2. Criar endereço se fornecido
                if (isset($input['address'])) {
                    $addressData = $input['address'];
                    \Modules\Organization\Models\OrganizationAddress::create([
                        'organization_id' => $organization->id,
                        'type' => $addressData['type'] ?? 'headquarters',
                        'street' => $addressData['street'],
                        'number' => $addressData['number'] ?? null,
                        'complement' => $addressData['complement'] ?? null,
                        'neighborhood' => $addressData['neighborhood'] ?? null,
                        'city' => $addressData['city'] ?? null,
                        'state' => $addressData['state'] ?? null,
                        'zip_code' => substr($addressData['zip_code'] ?? '', 0, 8), // Limitar a 8 caracteres
                        'country' => $addressData['country'] ?? 'BR',
                        'active' => $addressData['active'] ?? true,
                    ]);
                }

                // 3. Disparar evento para módulos processarem extensionData
                if (isset($input['extensionData']) && !empty($input['extensionData'])) {
                    event(new \Modules\Organization\Events\OrganizationCreated(
                        'generic',
                        $organization->toArray(),
                        $input['extensionData'],
                        $user->id
                    ));
                }

                // 4. Recarregar organização com relacionamentos
                $organization->load(['addresses']);

                return [
                    'organization' => $organization,
                    'success' => true,
                    'message' => 'Organization created successfully',
                ];
            } catch (\Exception $e) {
                // Log do erro para debugging
                \Log::error('Error creating organization', [
                    'input' => $input,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Re-throw para que a transação seja revertida
                throw $e;
            }
        });
    }
}
