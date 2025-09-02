<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $pageTitle }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ activeTab: 'principais' }">

            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                        Dados Principais
                    </a>
                    <span
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 dark:text-gray-600 cursor-not-allowed"
                        title="Salve a entidade para poder adicionar mais endereços.">
                        Endereços Adicionais
                    </span>
                </nav>
            </div>

            <div>
                <form method="POST"
                    action="{{ Str::contains($routeName, 'clientes') ? route('clientes.store') : route('fornecedores.store') }}"
                    x-data="formEntidade()">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label value="Tipo de Entidade *" />
                            <div class="flex items-center mt-2 space-x-4">
                                @if($tipoEntidade === 'Cliente')
                                    <label class="flex items-center"><input type="radio" value="Cliente"
                                            name="ent_tipo_entidade" checked
                                            class="dark:bg-gray-900 dark:border-gray-700"><span
                                            class="ms-2 text-sm text-gray-600 dark:text-gray-400">Cliente</span></label>
                                    <label class="flex items-center"><input type="radio" value="Cliente e Fornecedor"
                                            name="ent_tipo_entidade" class="dark:bg-gray-900 dark:border-gray-700"><span
                                            class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ambos</span></label>
                                @else
                                    <label class="flex items-center"><input type="radio" value="Fornecedor"
                                            name="ent_tipo_entidade" checked
                                            class="dark:bg-gray-900 dark:border-gray-700"><span
                                            class="ms-2 text-sm text-gray-600 dark:text-gray-400">Fornecedor</span></label>
                                    <label class="flex items-center"><input type="radio" value="Cliente e Fornecedor"
                                            name="ent_tipo_entidade" class="dark:bg-gray-900 dark:border-gray-700"><span
                                            class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ambos</span></label>
                                @endif
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Tipo de Pessoa *" />
                            <div class="flex items-center mt-2 space-x-4">
                                <label class="flex items-center"><input type="radio" x-model="tipoPessoa" value="J"
                                        name="ent_tipo_pessoa" checked
                                        class="dark:bg-gray-900 dark:border-gray-700"><span
                                        class="ms-2 text-sm text-gray-600 dark:text-gray-400">Jurídica</span></label>
                                <label class="flex items-center"><input type="radio" x-model="tipoPessoa" value="F"
                                        name="ent_tipo_pessoa" class="dark:bg-gray-900 dark:border-gray-700"><span
                                        class="ms-2 text-sm text-gray-600 dark:text-gray-400">Física</span></label>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="ent_situacao" value="Situação" />
                            <label for="ent_situacao" class="inline-flex items-center mt-2 cursor-pointer">
                                <input id="ent_situacao" type="checkbox" name="ent_situacao" class="sr-only peer"
                                    value="A" checked>
                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                </div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Ativo</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="ent_cnpj_cpf" x-text="tipoPessoa === 'J' ? 'CNPJ *' : 'CPF *'" />
                            <div class="flex items-center space-x-2 mt-1">
                                <x-text-input id="ent_cnpj_cpf" class="block w-full" type="text"
                                    x-bind:name="tipoPessoa === 'J' ? 'ent_cnpj' : 'ent_cpf'"
                                    x-mask:dynamic="tipoPessoa === 'J' ? '99.999.999/9999-99' : '999.999.999-99'"
                                    x-model="form.cnpj_cpf" @blur="verificarDocumento" required />
                                <button x-show="tipoPessoa === 'J'" @click.prevent="buscarCNPJ" type="button"
                                    class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">Buscar</button>
                            </div>
                            <div x-text="feedback" class="text-sm mt-1 h-4"></div>
                        </div>
                        <div>
                            <x-input-label for="ent_inscricao_estadual"
                                x-text="tipoPessoa === 'J' ? 'Inscrição Estadual' : 'RG'" />
                            <x-text-input id="ent_inscricao_estadual" class="block mt-1 w-full" type="text"
                                name="ent_inscricao_estadual" />
                        </div>
                        <div>
                            <x-input-label for="ent_codigo_interno" :value="__('Código Interno')" />
                            <x-text-input id="ent_codigo_interno" class="block mt-1 w-full" type="text"
                                name="ent_codigo_interno" />
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="ent_razao_social"
                                x-text="tipoPessoa === 'J' ? 'Razão Social *' : 'Nome Completo *'" />
                            <x-text-input id="ent_razao_social" class="block mt-1 w-full" type="text"
                                name="ent_razao_social" x-model="form.razao_social" required />
                        </div>
                        <div>
                            <x-input-label for="ent_nome_fantasia"
                                x-text="tipoPessoa === 'J' ? 'Nome Fantasia' : 'Apelido'" />
                            <x-text-input id="ent_nome_fantasia" class="block mt-1 w-full" type="text"
                                name="ent_nome_fantasia" x-model="form.nome_fantasia" />
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Endereço Principal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-1">
                            <x-input-label for="end_cep" value="CEP" />
                            <x-text-input id="end_cep" class="block mt-1 w-full" type="text" name="end_cep"
                                x-mask="99999-999" x-model="form.cep" />
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-3">
                            <x-input-label for="end_logradouro" value="Logradouro" />
                            <x-text-input id="end_logradouro" class="block mt-1 w-full" type="text"
                                name="end_logradouro" x-model="form.logradouro" />
                        </div>
                        <div>
                            <x-input-label for="end_numero" value="Número" />
                            <x-text-input id="end_numero" class="block mt-1 w-full" type="text" name="end_numero"
                                x-model="form.numero" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="end_complemento" value="Complemento" />
                        <x-text-input id="end_complemento" class="block mt-1 w-full" type="text" name="end_complemento"
                            x-model="form.complemento" />
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="end_bairro" :value="__('Bairro')" />
                            <x-text-input id="end_bairro" class="block mt-1 w-full" type="text" name="end_bairro"
                                x-model="form.bairro" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="end_cidade" :value="__('Cidade')" />
                            <x-text-input id="end_cidade" class="block mt-1 w-full" type="text" name="end_cidade"
                                x-model="form.cidade" />
                        </div>
                        <div>
                            <x-input-label for="end_uf" :value="__('UF')" />
                            <x-text-input id="end_uf" class="block mt-1 w-full" type="text" name="end_uf"
                                x-model="form.uf" />
                        </div>
                    </div>



                    <div class="flex items-center justify-end mt-6 space-x-4">
                        {{-- Botão de Cancelar --}}
                        <a href="{{ Str::contains($routeName, 'clientes') ? route('clientes.index') : route('fornecedores.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Cancelar') }}
                        </a>
                        {{-- Botão Principal (Salvar) --}}
                        <x-primary-button>
                            {{ __('Salvar') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function formEntidade() {
                return {
                    tipoPessoa: 'J',
                    feedback: '',
                    form: {
                        cnpj_cpf: '',
                        razao_social: '',
                        nome_fantasia: '',
                        cep: '',
                        logradouro: '',
                        numero: '',
                        complemento: '',
                        bairro: '',
                        cidade: '',
                        uf: '',
                    },
                    buscarCNPJ() {
                        const cnpj = this.form.cnpj_cpf.replace(/\D/g, '');
                        if (cnpj.length !== 14) {
                            this.feedback = 'Por favor, digite um CNPJ válido.';
                            return;
                        }
                        this.feedback = 'Buscando...';
                        fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`)
                            .then(response => {
                                if (!response.ok) throw new Error('CNPJ não encontrado ou inválido.');
                                return response.json();
                            })
                            .then(data => {
                                this.feedback = 'Dados carregados!';
                                this.form.razao_social = data.razao_social;
                                this.form.nome_fantasia = data.nome_fantasia;
                                this.form.cep = data.cep;
                                this.form.logradouro = data.logradouro;
                                this.form.numero = data.numero;
                                this.form.complemento = data.complemento;
                                this.form.bairro = data.bairro;
                                this.form.cidade = data.municipio;
                                this.form.uf = data.uf;

                            })
                            .catch(error => {
                                this.feedback = error.message;
                            });
                    },
                    verificarDocumento() {
                        const tipo = this.tipoPessoa === 'J' ? 'cnpj' : 'cpf';
                        const documento = this.form.cnpj_cpf;

                        // Se o campo estiver vazio, não faz nada
                        if (!documento) {
                            return;
                        }

                        fetch('{{ route("entidades.verificarDuplicidade") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                documento: documento,
                                tipo: tipo
                            })
                        }).then(response => response.json())
                            .then(data => {
                                if (data.existe) {
                                    Swal.fire({
                                        title: 'Entidade já Cadastrada!',
                                        text: "Este CPF/CNPJ já existe no sistema. Deseja editar o cadastro existente?",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sim, ir para edição',
                                        cancelButtonText: 'Não, cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = data.edit_url;
                                        }
                                    })
                                }
                            });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>