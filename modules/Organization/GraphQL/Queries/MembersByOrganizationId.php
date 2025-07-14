<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Modules\Organization\Models\Organization;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class MembersByOrganizationId
{
    /**
     * Retorna os membros de uma organização específica
     *
     * @param  mixed  $root
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array
     * @throws \Exception Se o usuário não estiver autenticado ou a organização não for encontrada
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // A autenticação já é verificada pelo middleware da rota, mas para garantir:
        $user = Auth::guard('api')->user();
        if (!$user) {
            throw new \Exception('Unauthenticated');
        }
        
        // Busca a organização pelo ID
        $organization = Organization::find($args['organizationId']);
        if (!$organization) {
            throw new \Exception('Organization not found');
        }
        
        // Carrega os membros da organização com seus usuários
        $memberships = $organization->memberships()
            ->with('user')
            ->when(isset($args['active']), function ($query) use ($args) {
                return $query->where('is_active', $args['active']);
            })
            ->when(isset($args['role']), function ($query) use ($args) {
                return $query->where('role', $args['role']);
            })
            ->get();
            
        return $memberships;
    }
}
