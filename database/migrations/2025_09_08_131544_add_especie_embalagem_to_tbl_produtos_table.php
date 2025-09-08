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
        Schema::table('tbl_produtos', function (Blueprint $table) {
            // Adiciona o novo campo após a coluna de peso da embalagem
            $table->string('prod_especie_embalagem', 50)->nullable()->after('prod_peso_embalagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_produtos', function (Blueprint $table) {
            //
        });
    }
};
