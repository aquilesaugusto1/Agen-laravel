<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\User;
use App\Models\Consultor;
use App\Models\EmpresaParceira;
use App\Models\Projeto;

class AlocacoesImport implements ToCollection, WithHeadingRow
{
    protected array $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }
    
    public function collection(Collection $rows)
    {
        $map = $this->mapping;

        DB::transaction(function () use ($rows, $map) {
            foreach ($rows as $row) {
                $consultorNome = trim($row[$map['coordenador']] ?? '');
                $clienteNome = trim($row[$map['cliente']] ?? '');

                if (empty($consultorNome) || empty($clienteNome)) {
                    continue;
                }

                $empresa = EmpresaParceira::updateOrCreate(
                    ['nome_empresa' => $clienteNome],
                    ['horas_contratadas' => $row[$map['baseline_total']] ?? 0]
                );

                $consultor = $this->findOrCreateConsultor($consultorNome);

                $projeto = Projeto::firstOrCreate(
                    ['nome_projeto' => 'Projeto ' . $empresa->nome_empresa],
                    ['empresa_parceira_id' => $empresa->id, 'tipo' => 'ams']
                );
                
                $consultor->projetos()->syncWithoutDetaching($projeto->id);

                $techLeadNomes = explode('/', $row[$map['tech_lead']] ?? '');
                
                foreach ($techLeadNomes as $nome) {
                    $nome = trim($nome);
                    if(empty($nome)) continue;

                    $techLeadUser = $this->findOrCreateTechLead($nome);
                    $projeto->techLeads()->syncWithoutDetaching($techLeadUser->id);
                    $consultor->techLeads()->syncWithoutDetaching($techLeadUser->id);
                }
            }
        });
    }

    private function findOrCreateConsultor(string $nome): Consultor
    {
        $consultor = Consultor::where('nome', $nome)->first();
        if ($consultor) {
            return $consultor;
        }

        $email = strtolower(str_replace(' ', '.', $nome)) . '@consultor.agen';
        $user = User::firstOrCreate(
            ['email' => $email],
            ['nome' => $nome, 'password' => Hash::make('password'), 'funcao' => 'consultor']
        );

        return Consultor::create([
            'usuario_id' => $user->id,
            'nome' => $user->nome,
            'email' => $user->email,
        ]);
    }
    
    private function findOrCreateTechLead(string $nome): User
    {
        $user = User::where('nome', $nome)->where('funcao', 'techlead')->first();
        if ($user) {
            return $user;
        }
        
        $email = strtolower(str_replace(' ', '.', $nome)) . '@techlead.agen';
        return User::firstOrCreate(
            ['email' => $email],
            ['nome' => $nome, 'password' => Hash::make('password'), 'funcao' => 'techlead']
        );
    }
}
