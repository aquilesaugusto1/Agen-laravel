<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaParceira extends Model
{
    use HasFactory;

    protected $table = 'empresas_parceiras';

    protected $fillable = [
        'nome_empresa',
        'contato_principal',
        'telefone',
        'email',
        'ramo_atividade',
        'horas_contratadas',
    ];

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'empresa_id');
    }

    public function projetos()
    {
        return $this->hasMany(Projeto::class);
    }
}
