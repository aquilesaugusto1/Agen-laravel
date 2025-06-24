<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Mapeamento de Colunas (Passo 2 de 2)
                    </h2>
                     <p class="text-sm text-gray-600 mb-6">Associe os campos do sistema Agen com as colunas da sua planilha. Abaixo, você vê uma pré-visualização dos seus dados. Use os menus suspensos acima de cada coluna para definir o que ela representa.</p>

                    <form action="{{ route('imports.process') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        @foreach($headings as $heading)
                                        <th class="px-3 py-3 text-left">
                                            <label for="map_{{ $loop->index }}" class="block text-xs font-medium text-slate-500 uppercase">{{ $heading }}</label>
                                            <select name="map[{{ $heading }}]" id="map_{{ $loop->index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                                @foreach($systemFields as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($preview as $row)
                                    <tr>
                                        @foreach($headings as $heading)
                                        <td class="px-3 py-2 text-slate-600 whitespace-nowrap">{{ $row[$heading] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex justify-end mt-8 border-t pt-6">
                            <a href="{{ route('imports.create') }}" class="text-sm text-gray-600 hover:text-gray-800 mr-4">Cancelar e Voltar</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Processar Importação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
