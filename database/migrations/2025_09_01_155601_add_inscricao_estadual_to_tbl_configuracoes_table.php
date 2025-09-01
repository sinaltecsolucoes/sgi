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
        Schema::table('tbl_configuracoes', function (Blueprint $table) {
            $table->string('config_inscricao_estadual', 50)->nullable()->after('config_cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_configuracoes', function (Blueprint $table) {
            //
        });
    }
};
