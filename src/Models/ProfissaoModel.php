<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfissaoModel extends Model {
    protected $table = 'profissao';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nome',
        'gabinete_id',
        'usuario_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relacionamento com o gabinete
     */
    public function gabinete() {
        return $this->belongsTo(GabineteModel::class, 'gabinete_id', 'id');
    }

    /**
     * Relacionamento com o usuário (opcional)
     */
    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id', 'id');
    }
}
