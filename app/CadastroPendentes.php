<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadastroPendentes extends Model
{
    protected $fillable = [
        'nome', 'email', 'cpf_or_cnpj',
        'info', 'id_indicado',
    ];
}
