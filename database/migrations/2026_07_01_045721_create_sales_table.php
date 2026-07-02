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
        Schema::create('sales', function (Blueprint $table) {

            $table->id();


            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();


            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients');


            $table->foreignId('payment_method_id')
                ->constrained('payment_methods');


            $table->foreignId('warehouse_id')
                ->constrained('warehouses');


            $table->foreignId('terminal_id')
                ->constrained('terminals');


            $table->foreignId('user_id')
                ->constrained('users');


            $table->dateTime('date');


            $table->decimal('subtotal',10,2);

            $table->decimal('discount',10,2)
                ->default(0);

            $table->decimal('total',10,2);


            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
