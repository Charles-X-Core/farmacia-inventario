<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')
                  ->constrained('movements')
                  ->onDelete('cascade');
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('quantity_now')->default(0);
            $table->decimal('price_cost', 10, 2)->nullable();
            $table->decimal('price_cost_now', 10, 2)->nullable();
            $table->decimal('utilites', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_details');
    }
};
