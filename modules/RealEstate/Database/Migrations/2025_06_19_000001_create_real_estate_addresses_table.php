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
        Schema::create('real_estate_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('real_estate_id')->constrained('real_estates')->onDelete('cascade');
            $table->string('type')->default('branch'); // 'headquarters' or 'branch'
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code', 8);
            $table->string('country')->default('BR');
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('real_estate_id');
            $table->index('type');
            $table->index(['real_estate_id', 'type']);
            $table->index(['real_estate_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_addresses');
    }
};
