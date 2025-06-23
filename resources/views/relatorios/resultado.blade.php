<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Resultado do Relatório
                    </h2>
                    
                    @if(isset($filtros))
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-bold text-lg mb-2">Filtros Aplicados:</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <p><strong>Tipo:</strong> {{ Str::title(str_replace('_', ' ', $filtros['tipo_relatorio'] ?? '')) }}</p>
                            <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($filtros['data_inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($filtros['data_fim'])->format('d/m/Y') }}</p>
                            <p><strong>Consultor:</strong> {{ $filtros['consultor_nome'] ?? 'Todos' }}</p>
                            <p><strong>Cliente:</strong> {{ $filtros['empresa_nome'] ?? 'Todos' }}</p>
                        </div>
                    </div>
                    @endif

                    @if($tipoRelatorio === 'detalhado')
                        @include('relatorios.partials.detalhado')
                    @else
                        @include('relatorios.partials.sumario')
                    @endif

                     <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('relatorios.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Gerar Novo Relatório</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
