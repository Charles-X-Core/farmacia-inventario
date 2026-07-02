<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('warehouse_branches',function(Blueprint $table){
            $table->id();
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses')
                  ->onDelete('cascade');
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->onDelete('cascade');
            $table->boolean('status')->default(true);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_branches');
        Schema::dropIfExists('warehouses');
    }
};
