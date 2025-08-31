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
    Schema::table('tbl_usuarios', function (Blueprint $table) {
        // Renomeia as colunas que vamos manter e adaptar
        $table->renameColumn('usu_codigo', 'id');
        $table->renameColumn('usu_nome', 'name');
        $table->renameColumn('usu_login', 'email');
        $table->renameColumn('usu_senha', 'password');
        
        // 'usu_situacao' (A/I) é útil, vamos manter como 'status' ou 'ativo'
        // para poder ativar/desativar usuários.
        $table->renameColumn('usu_situacao', 'status');

        // Adiciona as colunas padrão do Laravel
        $table->timestamp('email_verified_at')->nullable()->after('email');
        $table->rememberToken();
        $table->timestamps(); // Adiciona created_at e updated_at
        
        // Remove as colunas que não usaremos mais
        $table->dropColumn(['usu_session_token', 'usu_tipo']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_usuarios', function (Blueprint $table) {
            //
        });
    }
};
