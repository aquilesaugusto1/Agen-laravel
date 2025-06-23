<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apontamento;
use App\Models\Consultor;
use App\Models\EmpresaParceira;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function index()
    {
        $consultores = Consultor::orderBy('nome')->get();
        $empresas = EmpresaParceira::orderBy('nome_empresa')->get();

        return view('relatorios.index', compact('consultores', 'empresas'));
    }

    public function gerar(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'tipo_relatorio' => 'required|in:detalhado,por_cliente,por_consultor',
            'consultor_id' => 'nullable|exists:consultores,id',
            'empresa_id' => 'nullable|exists:empresas_parceiras,id',
        ]);

        $query = Apontamento::query()
            ->whereBetween('data_apontamento', [$request->data_inicio, $request->data_fim]);

        if ($request->filled('consultor_id')) {
            $query->where('consultor_id', $request->consultor_id);
        }

        if ($request->filled('empresa_id')) {
            $query->whereHas('agenda', function ($q) use ($request) {
                $q->where('empresa_id', $request->empresa_id);
            });
        }

        $tipoRelatorio = $request->tipo_relatorio;
        $resultados = [];
        $totalGeralHoras = 0;

        if ($tipoRelatorio === 'detalhado') {
            $resultados = $query->with('consultor', 'agenda.empresaParceira')->latest('data_apontamento')->get();
            $totalGeralHoras = $resultados->sum('horas_gastas');
        } else {
            $groupField = $tipoRelatorio === 'por_cliente' ? 'empresas_parceiras.nome_empresa' : 'consultores.nome';
            $joinTable = $tipoRelatorio === 'por_cliente' ? 'agendas' : 'consultores';
            $joinOn1 = $tipoRelatorio === 'por_cliente' ? 'apontamentos.agenda_id' : 'apontamentos.consultor_id';
            $joinOn2 = $tipoRelatorio === 'por_cliente' ? 'agendas.id' : 'consultores.id';
            
            $query->join('agendas', 'apontamentos.agenda_id', '=', 'agendas.id')
                  ->join('consultores', 'apontamentos.consultor_id', '=', 'consultores.id')
                  ->join('empresas_parceiras', 'agendas.empresa_id', '=', 'empresas_parceiras.id');

            $resultados = $query->select($groupField . ' as nome', DB::raw('SUM(apontamentos.horas_gastas) as total_horas'))
                                ->groupBy('nome')
                                ->orderBy('nome')
                                ->get();
            $totalGeralHoras = $resultados->sum('total_horas');
        }
        
        $filtros = $request->only(['data_inicio', 'data_fim', 'consultor_id', 'empresa_id', 'tipo_relatorio']);
        if($request->consultor_id) $filtros['consultor_nome'] = Consultor::find($request->consultor_id)->nome;
        if($request->empresa_id) $filtros['empresa_nome'] = EmpresaParceira::find($request->empresa_id)->nome_empresa;

        return view('relatorios.resultado', compact('resultados', 'totalGeralHoras', 'filtros', 'tipoRelatorio'));
    }
}
