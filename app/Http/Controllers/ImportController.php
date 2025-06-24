<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AlocacoesImport;
use Maatwebsite\Excel\HeadingRowImport;

class ImportController extends Controller
{
    public function create()
    {
        return view('imports.create');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $path = $file->store('temp_imports');

        $headings = (new HeadingRowImport)->toArray($path);
        $previewRows = Excel::toCollection(new \stdClass(), $path)->first()->take(5);
        
        session([
            'import_file_path' => $path,
            'import_headings' => $headings[0][0] ?? [],
            'import_preview' => $previewRows
        ]);

        return redirect()->route('imports.mapping');
    }

    public function mapping()
    {
        if (!session()->has('import_file_path')) {
            return redirect()->route('imports.create')->withErrors(['geral' => 'Nenhum ficheiro para mapear. Por favor, faça o upload primeiro.']);
        }
        
        $headings = session('import_headings', []);
        $preview = session('import_preview', collect());

        $systemFields = [
            '' => '-- Ignorar esta coluna --',
            'coordenador' => 'Nome do Consultor (Coordenador)',
            'tech_lead' => 'Tech Lead',
            'cliente' => 'Cliente',
            'baseline_total' => 'Horas Contratadas (Baseline Total)',
        ];

        return view('imports.mapping', compact('headings', 'preview', 'systemFields'));
    }
    
    public function process(Request $request)
    {
        $path = session('import_file_path');
        $mapping = $request->input('map');

        if (empty($path) || !is_array($mapping)) {
            return redirect()->route('imports.create')->withErrors(['geral' => 'Sessão de importação expirada. Por favor, tente novamente.']);
        }
        
        $flippedMapping = array_flip(array_filter($mapping));

        try {
            Excel::import(new AlocacoesImport($flippedMapping), $path);
        } catch (\Exception $e) {
            return redirect()->route('imports.create')->withErrors(['geral' => 'Não foi possível importar a planilha. Verifique se o mapeamento está correto e se os dados são válidos. Erro: ' . $e->getMessage()]);
        } finally {
            session()->forget(['import_file_path', 'import_headings', 'import_preview']);
            \Illuminate\Support\Facades\Storage::delete($path);
        }
        
        return redirect()->route('empresas.index')->with('success', 'Planilha importada e dados processados com sucesso!');
    }
}
