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
     * Resolves a GraphQL query to retrieve members of a specific organization.
     *
     * Returns an array of organization memberships, optionally filtered by active status and role. Throws an exception if the user is unauthenticated or the organization does not exist.
     *
     * @param mixed $root The root value passed to the resolver.
     * @param array $args Arguments for the query, including 'organizationId' (required), and optionally 'active' and 'role'.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context The GraphQL context.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo GraphQL resolve information.
     * @return array The list of organization memberships matching the criteria.
     * @throws \Exception If the user is unauthenticated or the organization is not found.
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
