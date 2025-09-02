<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $pageTitle }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100" x-data="formEntidade({{ json_encode($entidade) }})"
            x-init="init()">

            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#" @click.prevent="activeTab = 'principais'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'principais', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'principais' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Dados Principais
                    </a>
                    <a href="#" @click.prevent="activeTab = 'enderecos'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'enderecos', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'enderecos' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Gerenciar Endereços
                    </a>
                </nav>
            </div>

            <div x-show="activeTab === 'principais'">
                @include('entidades.partials.form-dados-principais')
            </div>

            <div x-show="activeTab === 'enderecos'">
                @include('entidades.partials.form-enderecos')
            </div>

        </div>
    </div>

    @push('scripts')
        {{-- O script completo e corrigido fica no arquivo principal --}}
        <script>
            function formEntidade(entidade = null) {
                const enderecoPrincipal = entidade && entidade.enderecos && entidade.enderecos.length > 0 ? entidade.enderecos.find(e => e.end_tipo_endereco === 'Principal') : {};

                return {
                    // ---- PROPRIEDADES DE CONTROLE ----
                    entidadeId: entidade ? entidade.ent_codigo : null,
                    activeTab: 'principais',
                    tipoPessoa: entidade ? entidade.ent_tipo_pessoa : 'J',
                    feedback: '',
                    feedbackCEP: '',
                    enderecos: [],
                    exibindoFormularioEndereco: false,
                    editandoEndereco: false,

                    // ---- DADOS DOS FORMULÁRIOS ----
                    form: {
                        razao_social: entidade ? entidade.ent_razao_social : '',
                        nome_fantasia: entidade ? entidade.ent_nome_fantasia : '',
                        cnpj_cpf: entidade ? (entidade.ent_tipo_pessoa === 'J' ? entidade.ent_cnpj : entidade.ent_cpf) : '',
                        cep: enderecoPrincipal?.end_cep || '',
                        logradouro: enderecoPrincipal?.end_logradouro || '',
                        numero: enderecoPrincipal?.end_numero || '',
                        complemento: enderecoPrincipal?.end_complemento || '',
                        bairro: enderecoPrincipal?.end_bairro || '',
                        cidade: enderecoPrincipal?.end_cidade || '',
                        uf: enderecoPrincipal?.end_uf || '',
                    },
                    formEndereco: {
                        end_codigo: null, end_tipo_endereco: 'Comercial', end_cep: '',
                        end_logradouro: '', end_numero: '', end_complemento: '',
                        end_bairro: '', end_cidade: '', end_uf: '',
                    },

                    // ---- FUNÇÕES ----
                    init() {
                        this.$watch('activeTab', (newTab) => {
                            if (newTab === 'enderecos' && this.enderecos.length === 0) {
                                this.carregarEnderecos();
                            }
                        });
                    },
                    carregarEnderecos() {
                        if (!this.entidadeId) return;
                        fetch(`/entidades/${this.entidadeId}/enderecos`)
                            .then(response => response.json())
                            .then(data => { this.enderecos = data; });
                    },
                    buscarCNPJ() {
                        const cnpj = this.form.cnpj_cpf.replace(/\D/g, '');
                        if (cnpj.length !== 14) { this.feedback = 'Por favor, digite um CNPJ válido.'; return; }
                        this.feedback = 'Buscando...';
                        fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`)
                            .then(response => { if (!response.ok) throw new Error('CNPJ não encontrado ou inválido.'); return response.json(); })
                            .then(data => {
                                this.feedback = 'Dados carregados!'; this.form.razao_social = data.razao_social;
                                this.form.nome_fantasia = data.nome_fantasia; this.form.cep = data.cep;
                                this.form.logradouro = data.logradouro; this.form.numero = data.numero;
                                this.form.complemento = data.complemento; this.form.bairro = data.bairro;
                                this.form.cidade = data.municipio; this.form.uf = data.uf;
                            })
                            .catch(error => { this.feedback = error.message; });
                    },
                    buscarCEP() {
                        const cep = this.formEndereco.end_cep.replace(/\D/g, '');
                        if (cep.length !== 8) { this.feedbackCEP = 'Digite um CEP válido com 8 dígitos.'; return; }
                        this.feedbackCEP = 'Buscando...';
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.erro) { throw new Error('CEP não encontrado.'); }
                                this.feedbackCEP = 'CEP encontrado!'; this.formEndereco.end_logradouro = data.logradouro;
                                this.formEndereco.end_bairro = data.bairro; this.formEndereco.end_cidade = data.localidade;
                                this.formEndereco.end_uf = data.uf;
                            })
                            .catch(error => { this.feedbackCEP = error.message; });
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
                    },
                    adicionarNovoEndereco() {
                        this.editandoEndereco = false;
                        this.formEndereco = { end_codigo: null, end_tipo_endereco: 'Comercial', end_cep: '', end_logradouro: '', end_numero: '', end_complemento: '', end_bairro: '', end_cidade: '', end_uf: '', };
                        this.exibindoFormularioEndereco = true;
                    },
                    cancelarFormularioEndereco() {
                        this.exibindoFormularioEndereco = false;
                    },
                    salvarEndereco() {
                        if (!this.entidadeId) return;
                        const isUpdating = !!this.formEndereco.end_codigo;
                        const url = isUpdating ? `/entidades/${this.entidadeId}/enderecos/${this.formEndereco.end_codigo}` : `/entidades/${this.entidadeId}/enderecos`;
                        const method = isUpdating ? 'PATCH' : 'POST';
                        fetch(url, {
                            method: method,
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                            body: JSON.stringify(this.formEndereco)
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Sucesso!', data.message, 'success');
                                    this.carregarEnderecos();
                                    this.cancelarFormularioEndereco();
                                } else {
                                    Swal.fire('Erro!', data.message, 'error');
                                }
                            })
                            .catch(() => { Swal.fire('Erro!', 'Ocorreu um erro de comunicação.', 'error'); });
                    },
                    editarEndereco(endereco) {
                        this.editandoEndereco = true;
                        let cepFormatado = endereco.end_cep || '';
                        if (cepFormatado && cepFormatado.length === 8) { cepFormatado = cepFormatado.replace(/(\d{5})(\d{3})/, '$1-$2'); }
                        this.formEndereco = {
                            end_codigo: endereco.end_codigo, end_tipo_endereco: endereco.end_tipo_endereco,
                            end_cep: cepFormatado, end_logradouro: endereco.end_logradouro,
                            end_numero: endereco.end_numero, end_complemento: endereco.end_complemento,
                            end_bairro: endereco.end_bairro, end_cidade: endereco.end_cidade,
                            end_uf: endereco.end_uf,
                        };
                        this.exibindoFormularioEndereco = true;
                    },
                    excluirEndereco(enderecoId) {
                        Swal.fire({
                            title: 'Você tem certeza?',
                            text: "Esta ação não poderá ser revertida!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sim, excluir!',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/entidades/${this.entidadeId}/enderecos/${enderecoId}`, {
                                    method: 'DELETE',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Excluído!', data.message, 'success');
                                            this.carregarEnderecos();
                                        } else {
                                            Swal.fire('Erro!', data.message, 'error');
                                        }
                                    })
                                    .catch(() => { Swal.fire('Erro!', 'Ocorreu um erro de comunicação.', 'error'); });
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>