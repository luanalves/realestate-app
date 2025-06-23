<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\Organization\Models\OrganizationMembership;

class AddOrganizationMember
{
    /**
     * Obtém o mapeamento de tipos de organização da configuração
     * 
     * @return array<string, string>
     */
    protected function getOrganizationTypeMap(): array
    {
        return \Modules\Organization\Support\OrganizationConstants::ORGANIZATION_TYPE_MAP;
    }
    
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return bool
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): bool
    {
        try {
            // Resolver o modelo de organização baseado no tipo
            $organizationClass = $this->resolveOrganizationClass($args['organizationType']);
            
            // Encontrar a organização
            $organization = $organizationClass::findOrFail($args['organizationId']);
            
            // Encontrar o usuário
            $user = User::findOrFail($args['userId']);
            
            // Verificar se o usuário já está associado à organização
            $existingMembership = OrganizationMembership::where([
                'user_id' => $user->id,
                'organization_type' => $organizationClass,
                'organization_id' => $organization->id,
            ])->first();
            
            if ($existingMembership) {
                // Atualizar a associação existente
                $existingMembership->update([
                    'role' => $args['role'],
                    'position' => $args['position'] ?? $existingMembership->position,
                    'is_active' => true,
                    'joined_at' => $args['joinedAt'] ?? $existingMembership->joined_at ?? now(),
                ]);
            } else {
                // Criar uma nova associação
                OrganizationMembership::create([
                    'user_id' => $user->id,
                    'organization_type' => $organizationClass,
                    'organization_id' => $organization->id,
                    'role' => $args['role'],
                    'position' => $args['position'] ?? null,
                    'is_active' => true,
                    'joined_at' => $args['joinedAt'] ?? now(),
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar membro à organização: ' . $e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);
            
            return false;
        }
    }
    
    /**
     * Resolve o nome completo da classe baseado no tipo de organização
     *
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function resolveOrganizationClass(string $type): string
    {
        $organizationTypeMap = $this->getOrganizationTypeMap();
        
        if (!isset($organizationTypeMap[$type])) {
            throw new \InvalidArgumentException("Tipo de organização inválido: {$type}");
        }
        
        return $organizationTypeMap[$type];
    }
}
