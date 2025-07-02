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
     * Creates the `real_estates` table with a primary key that is also a foreign key to `organizations`, establishing a one-to-one inheritance relationship.
     *
     * The table includes real estate-specific fields (`creci`, `state_registration`) and enforces referential integrity with cascading deletes. An index is added on the `creci` column to optimize queries.
     */
    public function up(): void
    {
        Schema::create('real_estates', function (Blueprint $table) {
            // Chave primária que é também uma foreign key para organizations
            // Implementando herança de tabela concreta
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('organizations')->onDelete('cascade');

            // Campos específicos de imobiliárias
            $table->string('creci')->nullable();
            $table->string('state_registration')->nullable();

            // Índices para campos frequentemente consultados
            $table->index('creci');
        });
    }

    /**
     * Drops the `real_estates` table, reversing the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estates');
    }
};
