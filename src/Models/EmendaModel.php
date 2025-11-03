<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmendaModel extends Model {
    protected $table = 'emenda';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'numero',
        'ano',
        'valor',
        'descricao',
        'estado',
        'cidade',
        'situacao_id',
        'area_id',
        'tipo_id',
        'gabinete_id',
        'usuario_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relacionamento com a situação da emenda
     */
    public function situacao() {
        return $this->belongsTo(SituacaoEmendaModel::class, 'situacao_id', 'id');
    }

    /**
     * Relacionamento com a área da emenda
     */
    public function area() {
        return $this->belongsTo(AreaEmendaModel::class, 'area_id', 'id');
    }

    /**
     * Relacionamento com o tipo de emenda
     */
    public function tipo() {
        return $this->belongsTo(TipoEmendaModel::class, 'tipo_id', 'id');
    }

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
