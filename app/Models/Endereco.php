<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $table = 'tbl_enderecos';
    protected $primaryKey = 'end_codigo';

    // A tabela original não tem created_at/updated_at, então desativamos
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'end_entidade_id',
        'end_tipo_endereco',
        'end_cep',
        'end_logradouro',
        'end_numero',
        'end_complemento',
        'end_bairro',
        'end_cidade',
        'end_uf',
        'end_usuario_cadastro_id',
    ];

    /**
     * Define o relacionamento inverso (um endereço pertence a uma entidade).
     */
    public function entidade()
    {
        return $this->belongsTo(Entidade::class, 'end_entidade_id', 'ent_codigo');
    }
}