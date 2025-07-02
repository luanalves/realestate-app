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

return new class extends Migration {
    /**
     * Creates the "organizations" table with columns for organization details and relevant indexes.
     *
     * Defines columns for name, optional fantasy name, unique optional CNPJ, description, contact information, an active flag, and timestamps. Adds indexes on the "active" and "cnpj" columns to optimize queries.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fantasy_name')->nullable();
            $table->string('cnpj', 14)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Índices
            $table->index('active');
            $table->index('cnpj');
        });
    }

    /**
     * Drops the "organizations" table if it exists, reversing the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
