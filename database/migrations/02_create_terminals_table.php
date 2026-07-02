<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ movement_types (catálogo fijo)
        Schema::create('movement_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        // 2️⃣ terminals
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('status')->default(true);
        });

        // 3️⃣ series
        Schema::create('series', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terminal_id')
                  ->constrained('terminals')
                  ->cascadeOnDelete();

            $table->foreignId('movement_type_id')
                  ->constrained('movement_types')
                  ->restrictOnDelete(); // catálogo fijo

            $table->string('serie');
            $table->integer('number')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['terminal_id', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
        Schema::dropIfExists('terminals');
        Schema::dropIfExists('movement_types');
    }
};