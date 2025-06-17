<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Security;

use Modules\Security\Models\LogDetail;
use Tests\TestCase;

class LogDetailModelTest extends TestCase
{
    /**
     * Test LogDetail MongoDB connection.
     */
    public function testMongoDbConnection(): void
    {
        $logDetail = new LogDetail();
        $this->assertEquals('mongodb', $logDetail->getConnectionName());
    }

    /**
     * Test LogDetail collection name.
     */
    public function testCollectionName(): void
    {
        $logDetail = new LogDetail();
        $this->assertEquals('log_details', $logDetail->getTable());
    }

    /**
     * Test LogDetail fillable attributes.
     */
    public function testFillableAttributes(): void
    {
        $logDetail = new LogDetail();
        $expected = [
            'security_log_id',
            'details',
            'created_at',
            'updated_at',
        ];
        
        $this->assertEquals($expected, $logDetail->getFillable());
    }

    /**
     * Test LogDetail casts.
     */
    public function testCasts(): void
    {
        $logDetail = new LogDetail();
        $casts = $logDetail->getCasts();
        
        $this->assertArrayHasKey('details', $casts);
        $this->assertEquals('array', $casts['details']);
    }
}
