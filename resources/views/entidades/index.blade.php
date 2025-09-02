<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $pageTitle }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-medium mb-4">Gerenciar Registros</h3>

            <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                <div>
                    @if(Str::contains($routeName, 'clientes'))
                        <a href="{{ route('clientes.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Novo Cliente
                        </a>
                    @else
                        <a href="{{ route('fornecedores.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Novo Fornecedor
                        </a>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-4 sm:space-y-0">
                    <div>
                        <x-input-label for="filtro_tipo_entidade" value="Filtrar por Tipo:" />
                        <select id="filtro_tipo_entidade"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="Todos">Todos
                                ({{ Str::contains($routeName, 'clientes') ? 'Clientes' : 'Fornecedores' }} e Ambos)
                            </option>
                            @if(Str::contains($routeName, 'clientes'))
                                <option value="Cliente">Apenas Clientes</option>
                            @else
                                <option value="Fornecedor">Apenas Fornecedores</option>
                            @endif
                            <option value="Cliente e Fornecedor">Clientes e Fornecedores</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Filtrar por Situação:" />
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center"><input type="radio" name="filtro_situacao" value="Todos"
                                    checked class="dark:bg-gray-900 dark:border-gray-700"><span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">Todos</span></label>
                            <label class="flex items-center"><input type="radio" name="filtro_situacao" value="A"
                                    class="dark:bg-gray-900 dark:border-gray-700"><span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ativo</span></label>
                            <label class="flex items-center"><input type="radio" name="filtro_situacao" value="I"
                                    class="dark:bg-gray-900 dark:border-gray-700"><span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">Inativo</span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="overflow-x-auto">
                <table id="entidades-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Situação</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Código Interno</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Razão Social</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nome Fantasia</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                CNPJ/CPF</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Endereço Principal</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- O DataTables vai preencher esta área --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializa a tabela DataTables
                const table = $('#entidades-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ request()->url() }}", // Usa a URL atual (clientes ou fornecedores)
                        type: 'GET',
                        data: function (d) {
                            // Envia os valores dos filtros junto com a requisição
                            d.filtro_situacao = $('input[name="filtro_situacao"]:checked').val();
                            d.filtro_tipo_entidade = $('#filtro_tipo_entidade').val();
                            d.routeName = "{{ $routeName }}"; // Envia o nome da rota para o controller saber o contexto
                        }
                    },
                    columns: [
                        {
                            data: 'ent_situacao', name: 'ent_situacao', render: function (data) {
                                return data === 'A' ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>' : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>';
                            }
                        },
                        { data: 'ent_tipo_entidade', name: 'ent_tipo_entidade' },
                        { data: 'ent_codigo_interno', name: 'ent_codigo_interno' },
                        { data: 'ent_razao_social', name: 'ent_razao_social' },
                        { data: 'ent_nome_fantasia', name: 'ent_nome_fantasia' },
                        { data: 'documento_formatado', name: 'documento_formatado', orderable: false, searchable: false },
                        { data: 'endereco_principal', name: 'endereco_principal', orderable: false, searchable: false },
                        { data: 'acoes', name: 'acoes', orderable: false, searchable: false },
                    ],
                    language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' }
                });

                // Adiciona um listener para os filtros recarregarem a tabela
                $('#filtro_tipo_entidade, input[name="filtro_situacao"]').on('change', function () {
                    table.ajax.reload();
                });

                // Script para a confirmação de inativação (necessário aqui também)
                $(document).on('submit', '.form-inativar', function (event) {
                    event.preventDefault();
                    const form = this;
                    Swal.fire({
                        title: 'Você tem certeza?',
                        text: "Deseja realmente inativar este registro?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim, inativar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>