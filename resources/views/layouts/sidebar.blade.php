<div class="w-64 bg-gray-800 text-white flex-shrink-0">
    <div class="flex items-center justify-center h-16 bg-gray-900">
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold">Agen</a>
    </div>
    <nav class="mt-4">
        <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('dashboard') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
            Início
        </a>
        
        <a href="{{ route('agendas.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('agendas.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
            Agendas
        </a>
        
        <a href="{{ route('apontamentos.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('apontamentos.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
            Apontamentos
        </a>
        
        <a href="{{ route('relatorios.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('relatorios.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
            Relatórios
        </a>

        @if(auth()->user()->funcao == 'admin')
            <hr class="my-4 border-gray-600">
            <a href="{{ route('projetos.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('projetos.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
                Projetos
            </a>
            <a href="{{ route('consultores.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('consultores.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
                Consultores
            </a>
            <a href="{{ route('empresas.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('empresas.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
                Empresas
            </a>
            <a href="{{ route('techleads.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('techleads.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
                Tech Leads
            </a>
        @endif
        
        @if(auth()->user()->funcao == 'techlead')
            <hr class="my-4 border-gray-600">
            <a href="{{ route('consultores.index') }}" class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4 {{ request()->routeIs('consultores.*') ? 'bg-gray-700 border-gray-100' : 'border-gray-800' }} hover:bg-gray-700">
                Consultores
            </a>
        @endif
    </nav>
</div>
