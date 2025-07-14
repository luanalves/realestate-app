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
     * Esta migração cria a tabela real_estates seguindo boas práticas:
     * 1. Usando seu próprio ID como chave primária
     * 2. Adicionando uma coluna organization_id como chave estrangeira
     * 3. Incluindo timestamps para controle de data de criação e atualização
     */
    public function up(): void
    {
        Schema::create('real_estates', function (Blueprint $table) {
            // Usar um id próprio como chave primária
            $table->id();

            // Relacionamento com a tabela organizations via FK
            $table->unsignedBigInteger('organization_id')->unique();
            $table->foreign('organization_id')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');

            // Campos específicos de imobiliárias
            $table->string('creci')->nullable();
            $table->string('state_registration')->nullable();

            // Adicionar timestamps
            $table->timestamps();

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
