<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaModel extends Model {
    protected $table = 'pessoa';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nome',
        'email',
        'telefone',
        'cidade',
        'estado',
        'data_nascimento',
        'profissao',
        'partido',
        'instagram',
        'facebook',
        'importancia',
        'foto',
        'informacoes_adicionais',
        'orgao_id',
        'tipo_id',
        'gabinete_id',
        'usuario_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relacionamento com a profissão
     */
    public function profissao() {
        return $this->belongsTo(ProfissaoModel::class, 'profissao', 'id');
    }

    /**
     * Relacionamento com o órgão
     */
    public function orgao() {
        return $this->belongsTo(OrgaoModel::class, 'orgao_id', 'id');
    }

    /**
     * Relacionamento com o tipo de pessoa
     */
    public function tipo() {
        return $this->belongsTo(TipoPessoaModel::class, 'tipo_id', 'id');
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
