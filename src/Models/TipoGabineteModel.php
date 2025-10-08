<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGabineteModel extends Model
{
    protected $table = 'tipo_gabinete';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'nome'];
    protected $dates = ['created_at', 'updated_at'];
}
