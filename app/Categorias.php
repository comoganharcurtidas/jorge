<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $fillable = [
        'id_categoria', 'nome_categoria', 'dominio_categoria',
        'valor_cpm', 'status',
    ];
}
