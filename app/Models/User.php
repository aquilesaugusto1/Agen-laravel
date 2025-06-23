<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'password',
        'funcao',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function consultoresLiderados()
    {
        return $this->belongsToMany(Consultor::class, 'consultor_tech_lead', 'tech_lead_id', 'consultor_id');
    }

    public function projetosLiderados()
    {
        return $this->belongsToMany(Projeto::class, 'projeto_tech_lead', 'tech_lead_id', 'projeto_id');
    }

    public function consultor()
    {
        return $this->hasOne(Consultor::class, 'usuario_id');
    }
}
