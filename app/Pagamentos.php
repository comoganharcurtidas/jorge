<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pagamentos extends Model
{
    protected $fillable = [
        'uid', 'valor',
        'periodos', 'observacao', 'comprovante',
        'status',
    ];

}
