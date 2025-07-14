<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop a tabela antiga se existir
        Schema::dropIfExists('organization_memberships');

        // Cria a nova tabela com o nome correto e estrutura atualizada
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'organization_id'], 'org_member_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_members');
    }
};
