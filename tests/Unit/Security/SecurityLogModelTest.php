<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Security;

use Modules\UserManagement\Models\User;
use Modules\Security\Models\LogDetail;
use Modules\Security\Models\SecurityLog;
use Tests\TestCase;

class SecurityLogModelTest extends TestCase
{
    /**
     * Test SecurityLog model relationships.
     */
    public function testUserRelationship(): void
    {
        $securityLog = new SecurityLog();
        $relation = $securityLog->user();
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
        $this->assertEquals(User::class, $relation->getRelated()::class);
    }

    /**
     * Test SecurityLog fillable attributes.
     */
    public function testFillableAttributes(): void
    {
        $securityLog = new SecurityLog();
        $expected = [
            'uuid',
            'user_id',
            'email',
            'operation',
            'module',
            'ip_address',
            'status',
        ];
        
        $this->assertEquals($expected, $securityLog->getFillable());
    }

    /**
     * Test SecurityLog table name.
     */
    public function testTableName(): void
    {
        $securityLog = new SecurityLog();
        $this->assertEquals('security_logs', $securityLog->getTable());
    }

    /**
     * Test SecurityLog casts.
     */
    public function testCasts(): void
    {
        $securityLog = new SecurityLog();
        $casts = $securityLog->getCasts();
        
        $this->assertArrayHasKey('user_id', $casts);
        $this->assertArrayHasKey('created_at', $casts);
        $this->assertArrayHasKey('updated_at', $casts);
        
        $this->assertEquals('integer', $casts['user_id']);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }
}
