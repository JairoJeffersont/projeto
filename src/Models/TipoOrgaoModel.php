<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoOrgaoModel extends Model
{
    protected $table = 'tipo_orgao';
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
     * Relacionamento com os Órgãos desse tipo
     */
    public function orgaos()
    {
        return $this->hasMany(OrgaoModel::class, 'tipo_id', 'id');
    }
}
