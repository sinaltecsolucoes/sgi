<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestão de Produtos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Gerenciar Registros</h3>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                        <div>
                            <a href="{{ route('produtos.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Produto
                            </a>
                        </div>

                        <div class="flex items-center">
                            <x-input-label class="mr-4" value="Filtrar por Situação:" />
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center"><input type="radio" name="filtro_situacao"
                                        value="Todos" checked class="dark:bg-gray-900 dark:border-gray-700"><span
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table id="produtos-table"
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 responsive"
                            style="width:100%">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Situação</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cód. Interno</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descrição</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Embalagem</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Peso (kg)</th>
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
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#produtos-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('produtos.index') }}",
                        type: 'GET',
                        data: function (d) {
                            d.filtro_situacao = $('input[name="filtro_situacao"]:checked').val();
                        }
                    },
                    columns: [
                        { data: 'prod_situacao', name: 'prod_situacao' },
                        { data: 'prod_codigo_interno', name: 'prod_codigo_interno' },
                        { data: 'prod_descricao', name: 'prod_descricao' },
                        { data: 'prod_tipo', name: 'prod_tipo' },
                        { data: 'prod_tipo_embalagem', name: 'prod_tipo_embalagem' },
                        { data: 'prod_peso_embalagem', name: 'prod_peso_embalagem' },
                        { data: 'acoes', name: 'acoes', orderable: false, searchable: false },
                    ],
                    language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' }
                });

                // Listener para o filtro de situação
                $('input[name="filtro_situacao"]').on('change', function () {
                    table.ajax.reload();
                });
            });
        </script>
    @endpush
</x-app-layout>