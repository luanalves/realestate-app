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
     * Run the migrations.
     *
     * Esta migração cria uma tabela que estende a tabela 'organizations'
     * usando uma relação 1-1 com chave primária compartilhada.
     *
     * A relação de herança é implementada no nível de modelagem de dados,
     * onde cada RealEstate tem uma Organization correspondente
     * e a chave primária da RealEstate é também uma chave estrangeira
     * para a tabela organizations.
     *
     * Esta abordagem permite:
     * 1. Reutilizar campos comuns na tabela 'organizations'
     * 2. Adicionar campos específicos para imobiliárias na tabela 'real_estates'
     * 3. Manter integridade referencial com exclusão em cascata
     * 4. Implementar o padrão de herança de tabela concreta
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estates');
    }
};
