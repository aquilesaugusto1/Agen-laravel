<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';

    protected $fillable = [
        'data_hora',
        'assunto',
        'status',
        'consultor_id',
        'empresa_id',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function consultor()
    {
        return $this->belongsTo(Consultor::class);
    }

    public function empresaParceira()
    {
        return $this->belongsTo(EmpresaParceira::class, 'empresa_id');
    }

    public function apontamento()
    {
        return $this->hasOne(Apontamento::class);
    }
}
