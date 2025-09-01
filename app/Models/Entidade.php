<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Entidade extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'tbl_entidades';
    protected $primaryKey = 'ent_codigo';

    // Mapeia a coluna de data de cadastro original para o padrão do Laravel
    const CREATED_AT = 'ent_data_cadastro';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ent_razao_social',
        'ent_nome_fantasia',
        'ent_tipo_pessoa',
        'ent_cpf',
        'ent_cnpj',
        'ent_inscricao_estadual',
        'ent_tipo_entidade',
        'ent_situacao',
        'ent_usuario_cadastro_id',
        'ent_codigo_interno',
    ];

    /**
     * Define o relacionamento de um para um com Cliente.
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'cli_entidade_id', 'ent_codigo');
    }

    /**
     * Define o relacionamento de um para um com Fornecedor.
     */
    public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class, 'forn_entidade_id', 'ent_codigo');
    }

    /**
     * Define o relacionamento de um para muitos com Endereco.
     */
    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'end_entidade_id', 'ent_codigo');
    }

    // Futuramente, adicionaremos os relacionamentos aqui (hasOne, hasMany)
}