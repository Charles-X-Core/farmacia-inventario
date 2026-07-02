<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('type_document_id')->nullable()->after('company_id')->constrained('type_documents')->onDelete('set null');
            $table->string('document')->nullable()->after('type_document_id');
            $table->string('last_name')->nullable()->after('name');
        });

        DB::table('clients')->where('id', 1)->update(['company_id' => 1]);
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropColumn('type_document_id');
            $table->dropColumn('document');
            $table->dropColumn('last_name');
        });
    }
};
