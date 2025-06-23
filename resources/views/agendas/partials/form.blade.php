<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="data_hora" class="block font-medium text-sm text-gray-700">Data e Hora</label>
        <input type="datetime-local" name="data_hora" id="data_hora" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('data_hora', isset($agenda) ? $agenda->data_hora->format('Y-m-d\TH:i') : '') }}" required>
    </div>

    <div>
        <label for="assunto" class="block font-medium text-sm text-gray-700">Assunto</label>
        <input type="text" name="assunto" id="assunto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('assunto', $agenda->assunto ?? '') }}" required>
    </div>

    <div>
        <label for="consultor_id" class="block font-medium text-sm text-gray-700">Consultor</label>
        <select name="consultor_id" id="consultor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Selecione um consultor</option>
            @foreach ($consultores as $consultor)
                <option value="{{ $consultor->id }}" {{ old('consultor_id', $agenda->consultor_id ?? '') == $consultor->id ? 'selected' : '' }}>
                    {{ $consultor->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="empresa_id" class="block font-medium text-sm text-gray-700">Empresa (Cliente)</label>
        <select name="empresa_id" id="empresa_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Selecione uma empresa</option>
            @foreach ($empresas as $empresa)
                <option value="{{ $empresa->id }}" {{ old('empresa_id', $agenda->empresa_id ?? '') == $empresa->id ? 'selected' : '' }}>
                    {{ $empresa->nome_empresa }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="md:col-span-2">
        <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="Agendada" {{ old('status', $agenda->status ?? 'Agendada') == 'Agendada' ? 'selected' : '' }}>Agendada</option>
            <option value="Realizada" {{ old('status', $agenda->status ?? '') == 'Realizada' ? 'selected' : '' }}>Realizada</option>
            <option value="Cancelada" {{ old('status', $agenda->status ?? '') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('agendas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        {{ isset($agenda) ? 'Atualizar' : 'Salvar' }}
    </button>
</div>
