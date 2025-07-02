<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds 'preferences' and 'tenant_id' columns to the 'users' table.
     *
     * The 'preferences' column is a nullable JSON field added after 'role_id'. The 'tenant_id' column is a nullable unsigned big integer, indexed, and added after 'preferences'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('preferences')->nullable()->after('role_id');
            $table->unsignedBigInteger('tenant_id')->nullable()->after('preferences')->index();
        });
    }

    /**
     * Removes the 'preferences' and 'tenant_id' columns from the 'users' table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['preferences', 'tenant_id']);
        });
    }
};
