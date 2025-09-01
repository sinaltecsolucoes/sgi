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
            $table->string('config_cep', 8)->nullable()->after('config_cnpj');
            $table->string('config_logradouro', 255)->nullable()->after('config_cep');
            $table->string('config_numero', 50)->nullable()->after('config_logradouro');
            $table->string('config_bairro', 100)->nullable()->after('config_numero');
            $table->string('config_cidade', 100)->nullable()->after('config_bairro');
            $table->string('config_uf', 2)->nullable()->after('config_cidade');
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
