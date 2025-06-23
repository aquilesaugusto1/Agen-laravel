<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projeto;
use App\Models\Consultor;
use App\Models\User;
use App\Models\EmpresaParceira;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'projetos' => Projeto::count(),
            'consultores' => Consultor::count(),
            'tech_leads' => User::where('funcao', 'techlead')->count(),
            'empresas' => EmpresaParceira::count(),
        ];
        
        return view('dashboard', compact('stats'));
    }
}
