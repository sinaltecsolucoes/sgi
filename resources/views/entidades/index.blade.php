<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $pageTitle }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Mensagem de Sucesso --}}
                    @if (session('success'))
                        {{-- O SweetAlert já vai cuidar disso, mas podemos manter para o caso de o JS falhar --}}
                    @endif

                    <div class="flex justify-end mb-4">
                        @if(Str::contains($routeName, 'clientes'))
                            <a href="{{ route('clientes.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo
                                Cliente</a>
                        @else
                            <a href="{{ route('fornecedores.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo
                                Fornecedor</a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Razão Social</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        CNPJ/CPF</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Situação</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($entidades as $entidade)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $entidade->ent_codigo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $entidade->ent_razao_social }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $entidade->ent_tipo_pessoa == 'J' ? $entidade->ent_cnpj : $entidade->ent_cpf }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $entidade->ent_tipo_entidade }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $entidade->ent_situacao == 'A' ? 'Ativo' : 'Inativo' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('entidades.edit', $entidade) }}"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Editar</a>
                                            {{-- Futuramente adicionaremos o form de exclusão aqui --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Nenhuma entidade cadastrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $entidades->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>