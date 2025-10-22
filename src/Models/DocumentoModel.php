<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoModel extends Model {
    protected $table = 'documento';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'titulo',
        'ano',
        'arquivo',
        'resumo',
        'orgao_id',
        'tipo_id',
        'usuario_id',
        'gabinete_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relacionamento com o órgão
     */
    public function orgao() {
        return $this->belongsTo(OrgaoModel::class, 'orgao_id', 'id');
    }

    /**
     * Relacionamento com o tipo de documento
     */
    public function tipo() {
        return $this->belongsTo(TipoDocumentoModel::class, 'tipo_id', 'id');
    }

    /**
     * Relacionamento com o usuário
     */
    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id', 'id');
    }

    /**
     * Relacionamento com o gabinete
     */
    public function gabinete() {
        return $this->belongsTo(GabineteModel::class, 'gabinete_id', 'id');
    }
}
