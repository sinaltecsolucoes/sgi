<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Configuracao extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_configuracoes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'config_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'config_nome_empresa',
        'config_nome_fantasia',
        'config_cnpj',
        'config_inscricao_estadual',
        'config_logo_path',
        'config_cep',
        'config_logradouro',
        'config_numero',
        'config_bairro',
        'config_cidade',
        'config_uf',
    ];
}