<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GabineteModel extends Model {

    protected $table = 'gabinete';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'nome', 'nome_slug', 'email', 'estado', 'cidade', 'ativo', 'tipo_gabinete_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function tipoGabinete() {
        return $this->belongsTo(TipoGabineteModel::class, 'tipo_gabinete_id', 'id');
    }
}
