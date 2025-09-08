<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Produto extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'tbl_produtos';
    protected $primaryKey = 'prod_codigo';

    // Mapeia as colunas de data originais para o padrão do Laravel
    const CREATED_AT = 'prod_data_cadastro';
    const UPDATED_AT = 'prod_data_modificacao';

    protected $fillable = [
        'prod_codigo_interno',
        'prod_descricao',
        'prod_situacao',
        'prod_tipo',
        'prod_subtipo',
        'prod_classificacao',
        'prod_categoria',
        'prod_classe',
        'prod_especie',
        'prod_origem',
        'prod_conservacao',
        'prod_congelamento',
        'prod_fator_producao',
        'prod_tipo_embalagem',
        'prod_peso_embalagem',
        'prod_especie_embalagem',
        'prod_total_pecas',
        'prod_validade_meses',
        'prod_primario_id',
        'prod_ean13',
        'prod_dun14',
    ];

    /**
     * Relacionamento: O produto primário que este produto (secundário) contém.
     */
    public function produtoPrimario()
    {
        return $this->belongsTo(Produto::class, 'prod_primario_id', 'prod_codigo');
    }
   

    /**
     * Relacionamento: Os produtos secundários que contêm este produto (primário).
     */
    public function produtosSecundarios()
    {
        return $this->hasMany(Produto::class, 'prod_primario_id', 'prod_codigo');
    }
}