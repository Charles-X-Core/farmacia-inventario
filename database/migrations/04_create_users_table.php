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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('terminal_id')
                  ->constrained('terminals')
                  ->onDelete('cascade');
            $table->boolean('is_admin')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            });
        
        Schema::create('branch_users',function(Blueprint $table){
            $table->id();
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->boolean('status')->default(true);
            
        });

        Schema::create('warehouses_users',function(Blueprint $table){
            $table->id();
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->boolean('status')->default(true);
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_users');
        Schema::dropIfExists('warehouses_users');
        Schema::dropIfExists('users');
    }
};
