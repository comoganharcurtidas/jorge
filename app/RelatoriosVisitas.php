<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatoriosVisitas extends Model
{
    protected $fillable = [
        'uid', 'mid', 'visitas_publisher',
        'ganhos_publisher', 'visitas_redator',
        'ganhos_redator', 'ganhos_total', 'periodo',
        'visitas_redator_do_publisher', 'ganhos_redator_do_publisher',
        'status',
    ];
}
