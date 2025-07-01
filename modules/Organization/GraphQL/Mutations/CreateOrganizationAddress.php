<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use GraphQL\Error\Error as GraphQLError;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationAddress;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateOrganizationAddress
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): OrganizationAddress
    {
        // Find the organization
        $organization = Organization::find($args['organizationId']);
        if (!$organization) {
            throw new GraphQLError('Organization with ID '.$args['organizationId'].' does not exist.');
        }

        // Validate required fields
        $this->validateAddressInput($args);

        // Create the address
        return OrganizationAddress::create([
            'organization_id' => $organization->id,
            'type' => trim($args['type']),
            'street' => trim($args['street']),
            'number' => isset($args['number']) ? trim($args['number']) : null,
            'complement' => isset($args['complement']) ? trim($args['complement']) : null,
            'neighborhood' => trim($args['neighborhood']),
            'city' => trim($args['city']),
            'state' => trim($args['state']),
            'zip_code' => $this->normalizeZipCode($args['zip_code']),
            'country' => trim($args['country']),
            'active' => true,
        ]);
    }

    /**
     * Validate address input fields.
     *
     * @param array<string, mixed> $args
     *
     * @throws GraphQLError
     */
    private function validateAddressInput(array $args): void
    {
        $errors = [];

        // Required fields validation
        $requiredFields = ['type', 'street', 'neighborhood', 'city', 'state', 'zip_code', 'country'];
        foreach ($requiredFields as $field) {
            if (empty($args[$field]) || !is_string($args[$field]) || trim($args[$field]) === '') {
                $errors[] = "Field '{$field}' is required and cannot be empty.";
            }
        }

        // Specific field validations
        if (!empty($args['type'])) {
            $validTypes = ['main', 'secondary', 'billing', 'shipping'];
            if (!in_array($args['type'], $validTypes, true)) {
                $errors[] = "Field 'type' must be one of: ".implode(', ', $validTypes).'.';
            }
        }

        if (!empty($args['zip_code'])) {
            $zipCode = preg_replace('/[^0-9]/', '', $args['zip_code']);
            if (strlen($zipCode) !== 8) {
                $errors[] = "Field 'zip_code' must be a valid Brazilian ZIP code (8 digits).";
            }
        }

        if (!empty($args['state'])) {
            if (strlen(trim($args['state'])) !== 2) {
                $errors[] = "Field 'state' must be a valid 2-letter state code.";
            }
        }

        if (!empty($args['country'])) {
            $country = trim($args['country']);
            if (strlen($country) < 2 || strlen($country) > 50) {
                $errors[] = "Field 'country' must be between 2 and 50 characters.";
            }
        }

        // Optional fields validation
        if (isset($args['number']) && $args['number'] !== null && trim($args['number']) === '') {
            $errors[] = "Field 'number' cannot be an empty string if provided.";
        }

        if (isset($args['complement']) && $args['complement'] !== null && trim($args['complement']) === '') {
            $errors[] = "Field 'complement' cannot be an empty string if provided.";
        }

        if (!empty($errors)) {
            throw new GraphQLError('Validation failed: '.implode(' ', $errors));
        }
    }

    /**
     * Normalize ZIP code to standard format.
     */
    private function normalizeZipCode(string $zipCode): string
    {
        // Remove any non-numeric characters and ensure 8 digits
        $cleaned = preg_replace('/[^0-9]/', '', $zipCode);

        if (strlen($cleaned) === 8) {
            // Format as XXXXX-XXX
            return substr($cleaned, 0, 5).'-'.substr($cleaned, 5);
        }

        return $cleaned;
    }
}
