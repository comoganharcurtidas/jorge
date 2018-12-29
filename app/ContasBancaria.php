<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContasBancaria extends Model
{
    protected $fillable = [
        'uid', 'nome_conta', 'tipo_pessoa',
        'cpf_cnpj', 'banco', 'ag', 'ag_digito',
        'acc', 'acc_digito', 'tipo_conta', 'observacao',
    ];
}
