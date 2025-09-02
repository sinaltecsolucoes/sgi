<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Adicionar Novo Produto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('produtos.store') }}">
                        @csrf

                        {{-- Incluindo o formulário de Identificação --}}
                        @include('produtos.partials._form-identificacao')

                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        {{-- Futuramente, incluiremos os outros parciais aqui --}}
                        {{--  @include('produtos.partials._form-caracteristicas') --}}
                        {{-- @include('produtos.partials._form-embalagem') --}}

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('produtos.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancelar
                            </a>
                            <x-primary-button>
                                Salvar
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>