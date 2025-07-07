<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganizationCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $organizationType,
        public readonly array $baseData,
        public readonly ?array $extensionData,
        public readonly int $userId,
    ) {
    }
}
