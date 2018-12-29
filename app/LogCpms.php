<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogCpms extends Model
{
    protected $fillable = [
        'cid', 'valor_cpm',
    ];
}
