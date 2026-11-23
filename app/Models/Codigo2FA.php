<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codigo2FA extends Model
{
    protected $table = 'codigos_2fa';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'codigo_hash',
        'expira_em',
        'used'
    ];

    protected $casts = [
        'expira_em' => 'datetime',
        'used' => 'boolean'
    ];
}
