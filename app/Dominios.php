<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dominios extends Model
{
    protected $fillable = [
        'nome_blog', 'slug_blog', 'url_blog', 'tipo_blog', 'status',
    ];
}
