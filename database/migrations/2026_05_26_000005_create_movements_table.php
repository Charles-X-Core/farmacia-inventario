<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses')
                  ->onDelete('cascade');
            $table->foreignId('terminal_id')
                  ->constrained('terminals')
                  ->onDelete('cascade');
            $table->string('serie');
            $table->integer('number');
            $table->foreignId('employee_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('movement_type_id')
                  ->constrained('movement_types')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
