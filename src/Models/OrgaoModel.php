<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgaoModel extends Model
{
    protected $table = 'orgaos';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nome',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'site',
        'instagram',
        'facebook',
        'informacoes_adicionais',
        'tipo_id',
        'gabinete_id',
        'usuario_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relacionamento com o Tipo de Órgão
     */
    public function tipo()
    {
        return $this->belongsTo(TipoOrgaoModel::class, 'tipo_id', 'id');
    }

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
}
