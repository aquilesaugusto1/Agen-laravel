<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        Gerar Relatório de Apontamentos
                    </h2>
                    <form action="{{ route('relatorios.gerar') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tipo_relatorio" class="block font-medium text-sm text-gray-700">Tipo de Relatório</label>
                                <select name="tipo_relatorio" id="tipo_relatorio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="detalhado">Detalhado</option>
                                    <option value="por_cliente">Sumário por Cliente</option>
                                    <option value="por_consultor">Sumário por Consultor</option>
                                </select>
                            </div>
                            <div></div>
                            <div>
                                <label for="data_inicio" class="block font-medium text-sm text-gray-700">Data de Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="data_fim" class="block font-medium text-sm text-gray-700">Data de Fim</label>
                                <input type="date" name="data_fim" id="data_fim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="consultor_id" class="block font-medium text-sm text-gray-700">Filtrar por Consultor (Opcional)</label>
                                <select name="consultor_id" id="consultor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Todos os consultores</option>
                                    @foreach ($consultores as $consultor)
                                        <option value="{{ $consultor->id }}">{{ $consultor->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="empresa_id" class="block font-medium text-sm text-gray-700">Filtrar por Cliente (Opcional)</label>
                                <select name="empresa_id" id="empresa_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Todos os clientes</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->nome_empresa }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Gerar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
