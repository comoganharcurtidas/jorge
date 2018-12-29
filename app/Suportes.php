<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suportes extends Model
{
    protected $fillable = [
        'uid', 'assunto', 'setor', 'mensagem', 'resposta', 'status',
    ];
}
