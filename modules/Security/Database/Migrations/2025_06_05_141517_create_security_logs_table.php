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
     */
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('UUID para correlação com MongoDB');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID do usuário que fez a requisição');
            $table->string('email')->nullable()->comment('Email do usuário que fez a requisição');
            $table->string('operation')->comment('Nome da operação GraphQL (query/mutation)');
            $table->string('module')->nullable()->comment('Módulo da operação GraphQL');
            $table->string('ip_address', 45)->comment('Endereço IP da requisição');
            $table->enum('status', ['success', 'error', 'unauthorized', 'graphql_error', 'client_error', 'server_error', 'unknown'])->comment('Status da execução');
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index(['user_id', 'created_at']);
            $table->index(['operation', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('uuid');
            $table->index(['module', 'created_at']);

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
