<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Apontamento;
use App\Models\EmpresaParceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApontamentoController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('apontamentos.index');
    }

    public function getAgendasAsEvents(Request $request)
    {
        $start = Carbon::parse($request->start)->toDateTimeString();
        $end = Carbon::parse($request->end)->toDateTimeString();

        $query = Agenda::with('consultor', 'projeto.empresaParceira', 'apontamento')
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
                'title' => $agenda->projeto->empresaParceira->nome_empresa,
                'start' => $agenda->data_hora,
                'color' => $color,
                'extendedProps' => [
                    'consultor' => $agenda->consultor->nome,
                    'assunto' => $agenda->assunto . ' (Projeto: ' . $agenda->projeto->nome_projeto . ')',
                    'faturado' => $isFaturado,
                    'hora_inicio' => $hasApontamento ? Carbon::parse($agenda->apontamento->hora_inicio)->format('H:i') : '',
                    'hora_fim' => $hasApontamento ? Carbon::parse($agenda->apontamento->hora_fim)->format('H:i') : '',
                    'descricao' => $hasApontamento ? $agenda->apontamento->descricao : '',
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

        $agenda = Agenda::with('projeto.empresaParceira')->findOrFail($validated['agenda_id']);
        
        $this->authorize('update', $agenda);

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
                
                $apontamento->fill([
                    'consultor_id' => $agenda->consultor_id,
                    'data_apontamento' => $agenda->data_hora->format('Y-m-d'),
                    'hora_inicio' => $validated['hora_inicio'],
                    'hora_fim' => $validated['hora_fim'],
                    'horas_gastas' => $horasGastas,
                    'descricao' => $validated['descricao'],
                    'faturado' => $faturarAgora
                ])->save();
                
                if ($faturarAgora) {
                    $empresa = $agenda->projeto->empresaParceira;
                    $empresa->update(['horas_contratadas' => DB::raw("horas_contratadas - $horasGastas")]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Apontamento salvo com sucesso!']);
    }
}
