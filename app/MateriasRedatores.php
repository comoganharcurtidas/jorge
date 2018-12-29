<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MateriasRedatores extends Model
{
    protected $fillable = [
        'uid', 'cid', 'mid', 'did', 'titulo',
        'foto_destaque', 'texto', 'revisado',
        'status',
    ];
}
