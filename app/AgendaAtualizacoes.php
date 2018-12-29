<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaAtualizacoes extends Model
{
    protected $fillable = [
        'data', 'hora'
    ];
}
