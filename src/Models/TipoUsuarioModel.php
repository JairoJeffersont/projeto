<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuarioModel extends Model
{
    protected $table = 'tipo_usuario';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'nome'];
    protected $dates = ['created_at', 'updated_at'];
}
