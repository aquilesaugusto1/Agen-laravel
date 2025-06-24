<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Importar Planilha (Passo 1 de 2)
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Faça o upload da sua planilha de alocações. O sistema irá ler os cabeçalhos das colunas para que você possa mapeá-los no próximo passo.</p>
                    
                    <form action="{{ route('imports.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="file" class="block font-medium text-sm text-gray-700">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file" id="file" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Próximo Passo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
