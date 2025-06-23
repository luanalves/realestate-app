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
        Schema::create('organization_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('organization'); // Permite relacionar com qualquer modelo de organização (real_estates, companies, etc.)
            $table->string('role')->nullable(); // Papel do usuário na organização (mais abstrato que cargos específicos)
            $table->string('position')->nullable(); // Cargo/posição na organização
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Cria um índice único para evitar membros duplicados
            $table->unique(['user_id', 'organization_type', 'organization_id'], 'org_membership_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_memberships');
    }
};
