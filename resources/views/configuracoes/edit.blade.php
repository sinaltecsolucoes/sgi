<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configurações Gerais') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Exibe a mensagem de sucesso --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 rounded relative"
                            role="alert">
                            <strong class="font-bold">Sucesso!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('configuracoes.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="config_nome_empresa" :value="__('Nome da Empresa')" />
                                <x-text-input id="config_nome_empresa" class="block mt-1 w-full" type="text"
                                    name="config_nome_empresa" :value="old('config_nome_empresa', $configuracao->config_nome_empresa)" required autofocus />
                                <x-input-error :messages="$errors->get('config_nome_empresa')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="config_nome_fantasia" :value="__('Nome Fantasia')" />
                                <x-text-input id="config_nome_fantasia" class="block mt-1 w-full" type="text"
                                    name="config_nome_fantasia" :value="old('config_nome_fantasia', $configuracao->config_nome_fantasia)" required />
                                <x-input-error :messages="$errors->get('config_nome_fantasia')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="config_cnpj" :value="__('CNPJ')" />
                                <x-text-input id="config_cnpj" class="block mt-1 w-full" type="text" name="config_cnpj"
                                    :value="old('config_cnpj', $configuracao->config_cnpj)"
                                    x-mask="99.999.999/9999-99" />
                                <x-input-error :messages="$errors->get('config_cnpj')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="config_inscricao_estadual" :value="__('Inscrição Estadual')" />
                                <x-text-input id="config_inscricao_estadual" class="block mt-1 w-full" type="text"
                                    name="config_inscricao_estadual" :value="old('config_inscricao_estadual', $configuracao->config_inscricao_estadual)" />
                                <x-input-error :messages="$errors->get('config_inscricao_estadual')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="config_logradouro" :value="__('Logradouro (Rua/Avenida)')" />
                                <x-text-input id="config_logradouro" class="block mt-1 w-full" type="text"
                                    name="config_logradouro" :value="old('config_logradouro', $configuracao->config_logradouro)" />
                                <x-input-error :messages="$errors->get('config_logradouro')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="config_numero" :value="__('Número')" />
                                <x-text-input id="config_numero" class="block mt-1 w-full" type="text"
                                    name="config_numero" :value="old('config_numero', $configuracao->config_numero)" />
                                <x-input-error :messages="$errors->get('config_numero')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="config_complemento" :value="__('Complemento')" />
                                <x-text-input id="config_complemento" class="block mt-1 w-full" type="text"
                                    name="config_complemento" :value="old('config_complemento', $configuracao->config_complemento)" />
                                <x-input-error :messages="$errors->get('config_complemento')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="config_cep" :value="__('CEP')" />
                                <x-text-input id="config_cep" class="block mt-1 w-full" type="text" name="config_cep"
                                    :value="old('config_cep', $configuracao->config_cep)" x-mask="99999-999" />
                                <x-input-error :messages="$errors->get('config_cep')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="config_bairro" :value="__('Bairro')" />
                                <x-text-input id="config_bairro" class="block mt-1 w-full" type="text"
                                    name="config_bairro" :value="old('config_bairro', $configuracao->config_bairro)" />
                                <x-input-error :messages="$errors->get('config_bairro')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="config_cidade" :value="__('Cidade')" />
                                <x-text-input id="config_cidade" class="block mt-1 w-full" type="text"
                                    name="config_cidade" :value="old('config_cidade', $configuracao->config_cidade)" />
                                <x-input-error :messages="$errors->get('config_cidade')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="config_uf" :value="__('UF')" />
                                <x-text-input id="config_uf" class="block mt-1 w-full" type="text" name="config_uf"
                                    :value="old('config_uf', $configuracao->config_uf)" />
                                <x-input-error :messages="$errors->get('config_uf')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="config_logo" :value="__('Logo da Empresa (deixe em branco para não alterar)')" />

                            {{-- Bloco para mostrar o logo atual, se ele existir --}}
                            @if ($configuracao->config_logo_path)
                                <div class="mt-2 mb-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2"></p>
                                    <img src="{{ asset('storage/' . $configuracao->config_logo_path) }}" alt="Logo Atual"
                                        class="h-20 w-auto rounded-md bg-white p-1 border">
                                </div>
                            @endif
                            {{-- Fim do Bloco --}}

                            <input id="config_logo"
                                class="block mt-1 w-full text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 rounded-md p-2"
                                type="file" name="config_logo">
                            <x-input-error :messages="$errors->get('config_logo')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Salvar Configurações') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>