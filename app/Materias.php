<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Materias extends Model
{
    protected $fillable = [
        'titulo', 'imagem', 'rid', 'post_slug',
        'post_url', 'post_cat', 'post_id',
        'dominio', 'importada', 'status',
    ];
}
