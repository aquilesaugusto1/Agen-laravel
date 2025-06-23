<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Consultor;
use App\Models\EmpresaParceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $agendasQuery = Agenda::with('consultor', 'empresaParceira')->latest();

        if ($user->funcao === 'consultor') {
            $agendasQuery->where('consultor_id', $user->consultor->id);
        } elseif ($user->funcao === 'techlead') {
            $consultoresLideradosIds = $user->consultoresLiderados()->pluck('consultores.id');
            $agendasQuery->whereIn('consultor_id', $consultoresLideradosIds);
        }
        
        $agendas = $agendasQuery->paginate(10);

        return view('agendas.index', compact('agendas'));
    }

    public function create()
    {
        $empresas = EmpresaParceira::all();
        $consultores = Consultor::where('status', 'Ativo')->get();
        return view('agendas.create', compact('empresas', 'consultores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_hora' => 'required|date',
            'assunto' => 'required|string|max:255',
            'status' => 'required|string|in:Agendada,Realizada,Cancelada',
            'consultor_id' => 'required|exists:consultores,id',
            'empresa_id' => 'required|exists:empresas_parceiras,id',
        ]);

        Agenda::create($request->all());

        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda criada com sucesso.');
    }

    public function show(Agenda $agenda)
    {
        return view('agendas.show', compact('agenda'));
    }

    public function edit(Agenda $agenda)
    {
        $empresas = EmpresaParceira::all();
        $consultores = Consultor::where('status', 'Ativo')->get();
        return view('agendas.edit', compact('agenda', 'empresas', 'consultores'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $request->validate([
            'data_hora' => 'required|date',
            'assunto' => 'required|string|max:255',
            'status' => 'required|string|in:Agendada,Realizada,Cancelada',
            'consultor_id' => 'required|exists:consultores,id',
            'empresa_id' => 'required|exists:empresas_parceiras,id',
        ]);

        $agenda->update($request->all());

        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda atualizada com sucesso.');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        return redirect()->route('agendas.index')
                         ->with('success', 'Agenda removida com sucesso.');
    }
}
