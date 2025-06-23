<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-6">Bem-vindo(a) ao Painel Agen!</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-3xl font-bold">{{ $stats['projetos'] }}</h3>
                            <p class="mt-2 text-lg">Projetos Ativos</p>
                        </div>

                        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-3xl font-bold">{{ $stats['consultores'] }}</h3>
                            <p class="mt-2 text-lg">Consultores</p>
                        </div>
                        
                        <div class="bg-purple-500 text-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-3xl font-bold">{{ $stats['tech_leads'] }}</h3>
                            <p class="mt-2 text-lg">Tech Leads</p>
                        </div>
                        
                        <div class="bg-orange-500 text-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-3xl font-bold">{{ $stats['empresas'] }}</h3>
                            <p class="mt-2 text-lg">Empresas Parceiras</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
