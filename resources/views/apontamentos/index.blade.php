<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Apontamento -->
    <div id="apontamentoModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="apontamentoForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Lançar Apontamento</h3>
                        <div class="mt-4 space-y-4">
                            <input type="hidden" id="agenda_id" name="agenda_id">
                            <div>
                                <p><strong>Consultor:</strong> <span id="modal_consultor"></span></p>
                                <p><strong>Assunto:</strong> <span id="modal_assunto"></span></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700">De</label>
                                    <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="hora_fim" class="block text-sm font-medium text-gray-700">Até</label>
                                    <input type="time" name="hora_fim" id="hora_fim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                            </div>
                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição das Atividades</label>
                                <textarea id="descricao" name="descricao" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required></textarea>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="faturar" name="faturar" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="faturar" class="font-medium text-gray-700">Faturar estas horas?</label>
                                    <p class="text-gray-500">Ao marcar, as horas serão debitadas do saldo do cliente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="saveButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                        <button type="button" id="closeModalButton" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('apontamentoModal');
            const form = document.getElementById('apontamentoForm');
            const closeModalButton = document.getElementById('closeModalButton');
            
            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                events: '{{ route("api.agendas") }}',
                editable: false, 
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    
                    form.reset();
                    document.getElementById('agenda_id').value = info.event.id;
                    document.getElementById('modal_consultor').textContent = props.consultor;
                    document.getElementById('modal_assunto').textContent = props.assunto;
                    document.getElementById('hora_inicio').value = props.hora_inicio.substring(0,5);
                    document.getElementById('hora_fim').value = props.hora_fim.substring(0,5);
                    document.getElementById('descricao').value = props.descricao;
                    document.getElementById('faturar').checked = props.faturado;

                    const saveButton = document.getElementById('saveButton');
                    if (props.faturado) {
                        form.querySelectorAll('input, textarea, button[type=submit]').forEach(el => el.disabled = true);
                         saveButton.textContent = 'Faturado';
                    } else {
                        form.querySelectorAll('input, textarea, button[type=submit]').forEach(el => el.disabled = false);
                        saveButton.textContent = 'Salvar';
                    }

                    modal.classList.remove('hidden');
                }
            });

            calendar.render();

            closeModalButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                data.faturar = document.getElementById('faturar').checked;

                fetch('{{ route("apontamentos.storeOrUpdate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    alert(result.message);
                    if(result.message.includes('sucesso')) {
                        modal.classList.add('hidden');
                        calendar.refetchEvents();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocorreu um erro.');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
