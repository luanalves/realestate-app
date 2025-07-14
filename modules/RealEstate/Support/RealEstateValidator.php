<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Support;

/**
 * Helper class for RealEstate validation.
 */
class RealEstateValidator
{
    /**
     * Validate RealEstate data.
     *
     * @param array $data       The data to validate
     * @param bool  $isCreating Whether this is being called during creation (which requires CRECI)
     *
     * @throws \InvalidArgumentException if validation fails
     */
    public static function validate(array $data, bool $isCreating = false): void
    {
        if ($isCreating && empty($data['creci'])) {
            throw new \InvalidArgumentException('CRECI is required for real estate');
        }
    }
}
