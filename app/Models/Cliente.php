<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_clientes';
    protected $primaryKey = 'cli_codigo';
    public $timestamps = false; // A tabela original não parece ter timestamps

    protected $fillable = [
        'cli_entidade_id',
        'cli_status_cliente',
        'cli_limite_credito',
        'cli_usuario_cadastro_id',
    ];

    public function entidade()
    {
        return $this->belongsTo(Entidade::class, 'cli_entidade_id', 'ent_codigo');
    }
}