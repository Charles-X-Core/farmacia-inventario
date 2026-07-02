<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allotments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')
                  ->constrained('movements')
                  ->onDelete('cascade');
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses')
                  ->onDelete('cascade');
            $table->string('code')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('flag_closed')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allotments');
    }
};
