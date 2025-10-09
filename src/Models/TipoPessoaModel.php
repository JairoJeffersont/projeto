<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPessoaModel extends Model
{
    protected $table = 'tipo_pessoa';
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
     * Relacionamento com o Gabinete
     */
    public function gabinete()
    {
        return $this->belongsTo(GabineteModel::class, 'gabinete_id', 'id');
    }

    /**
     * Relacionamento com o Usuário (opcional)
     */
    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id', 'id');
    }

    /**
     * Relacionamento com as Pessoas desse tipo
     */
    public function pessoas()
    {
        return $this->hasMany(PessoaModel::class, 'tipo_id', 'id');
    }
}
