<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Apontamento;
use App\Models\EmpresaParceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApontamentoController extends Controller
{
    public function index()
    {
        return view('apontamentos.index');
    }

    public function getAgendasAsEvents(Request $request)
    {
        $start = Carbon::parse($request->start)->toDateTimeString();
        $end = Carbon::parse($request->end)->toDateTimeString();

        $query = Agenda::with('consultor', 'empresaParceira', 'apontamento')
                       ->whereBetween('data_hora', [$start, $end]);

        $user = Auth::user();

        if ($user->funcao === 'consultor') {
            if ($user->consultor) {
                $query->where('consultor_id', $user->consultor->id);
            } else {
                return response()->json([]);
            }
        } elseif ($user->funcao === 'techlead') {
            $consultor_ids = $user->consultoresLiderados()->pluck('consultores.id');
            $query->whereIn('consultor_id', $consultor_ids);
        }

        $agendas = $query->get();

        $events = $agendas->map(function ($agenda) {
            $hasApontamento = $agenda->apontamento !== null;
            $isFaturado = $hasApontamento && $agenda->apontamento->faturado;

            $color = '#3B82F6';
            if ($agenda->status === 'Cancelada') $color = '#EF4444';
            if ($agenda->status === 'Realizada' && !$hasApontamento) $color = '#F59E0B';
            if ($isFaturado) $color = '#10B981';

            return [
                'id' => $agenda->id,
                'title' => $agenda->empresaParceira->nome_empresa,
                'start' => $agenda->data_hora,
                'color' => $color,
                'extendedProps' => [
                    'apontamento_id' => $hasApontamento ? $agenda->apontamento->id : null,
                    'consultor' => $agenda->consultor->nome,
                    'assunto' => $agenda->assunto,
                    'hora_inicio' => $hasApontamento ? Carbon::parse($agenda->apontamento->hora_inicio)->format('H:i') : '',
                    'hora_fim' => $hasApontamento ? Carbon::parse($agenda->apontamento->hora_fim)->format('H:i') : '',
                    'descricao' => $hasApontamento ? $agenda->apontamento->descricao : '',
                    'faturado' => $isFaturado,
                ]
            ];
        });

        return response()->json($events);
    }

    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'descricao' => 'required|string|max:1000',
            'faturar' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $agenda = Agenda::with('empresaParceira')->findOrFail($validated['agenda_id']);
        
        if ($user->funcao === 'consultor' && $agenda->consultor_id !== $user->consultor->id) {
            return response()->json(['message' => 'Você não tem permissão para apontar horas nesta agenda.'], 403);
        }

        $apontamento = Apontamento::firstOrNew(['agenda_id' => $agenda->id]);

        $inicio = Carbon::createFromTimeString($validated['hora_inicio']);
        $fim = Carbon::createFromTimeString($validated['hora_fim']);
        $horasGastas = round($fim->diffInMinutes($inicio) / 60, 2);

        $faturarAgora = $validated['faturar'] ?? false;
        $jaEraFaturado = $apontamento->faturado;

        try {
            DB::transaction(function () use ($apontamento, $agenda, $validated, $horasGastas, $faturarAgora, $jaEraFaturado) {
                
                if ($jaEraFaturado) {
                    throw new \Exception('Apontamento já faturado não pode ser alterado.');
                }

                $apontamento->consultor_id = $agenda->consultor_id;
                $apontamento->data_apontamento = $agenda->data_hora->format('Y-m-d');
                $apontamento->hora_inicio = $validated['hora_inicio'];
                $apontamento->hora_fim = $validated['hora_fim'];
                $apontamento->horas_gastas = $horasGastas;
                $apontamento->descricao = $validated['descricao'];
                $apontamento->faturado = $faturarAgora;
                
                $apontamento->save();

                if ($faturarAgora) {
                    EmpresaParceira::where('id', $agenda->empresa_parceira_id)
                       ->update(['horas_contratadas' => DB::raw("horas_contratadas - $horasGastas")]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Apontamento salvo com sucesso!']);
    }
}
