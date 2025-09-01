<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'tbl_fornecedores';
    protected $primaryKey = 'forn_codigo';
    public $timestamps = false;

    protected $fillable = [
        'forn_entidade_id',
        'forn_categoria',
        'forn_condicoes_pagamento',
        'forn_usuario_cadastro_id',
    ];

    public function entidade()
    {
        return $this->belongsTo(Entidade::class, 'forn_entidade_id', 'ent_codigo');
    }
}