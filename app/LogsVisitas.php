<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogsVisitas extends Model
{
    protected $fillable = [
        'link_id', 'total_visitas', 'data_visitas', 'periodo',
    ];
}
