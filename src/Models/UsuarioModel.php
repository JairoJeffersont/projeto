<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'nome',
        'email',
        'senha',
        'telefone',
        'token',
        'data_nascimento',
        'foto',
        'gestor',
        'ativo',
        'tipo_usuario_id',
        'gabinete_id'
    ];
    protected $dates = ['created_at', 'updated_at'];

    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuarioModel::class, 'tipo_usuario_id', 'id');
    }

    public function gabinete()
    {
        return $this->belongsTo(GabineteModel::class, 'gabinete_id', 'id');
    }
}
