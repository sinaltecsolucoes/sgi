<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Adicionar Novo Produto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                {{-- A MÁGICA COMEÇA AQUI com x-data e x-init --}}
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="formProduto()" x-init="init()">

                    <form method="POST" action="{{ route('produtos.store') }}">
                        @csrf

                        @include('produtos.partials._form-identificacao')

                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        @include('produtos.partials._form-caracteristicas')

                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        @include('produtos.partials._form-embalagem')

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

    @push('scripts')
        <script>
            function select2Component(config) {
                return {
                    value: config.initialValue || null,
                    initialValue: config.initialValue || null,
                    initialText: config.initialText || 'Selecione...',
                    init() {
                        const select = $(this.$refs.select);
                        select.select2({
                            placeholder: 'Digite para buscar um produto...',
                            language: 'pt-BR',
                            ajax: {
                                url: '{{ route("produtos.buscar.primarios") }}',
                                dataType: 'json',
                                delay: 250,
                                processResults: function (data) {
                                    return { results: data.results };
                                },
                                cache: true
                            }
                        }).on('select2:select', (event) => {
                            this.value = event.params.data.id;
                            // Dispara um evento para o form principal saber da mudança
                            this.$dispatch('input', event.params.data.id);
                        });

                        this.$watch('value', (newValue) => {
                            select.val(newValue).trigger('change');
                        });
                    }
                }
            }

            function formProduto() {
                return {
                    // ---- PROPRIEDADES DE DADOS ----
                    tipoEmbalagem: 'PRIMARIA',
                    produtosPrimarios: [],
                    form: { // Dados para o cálculo da classe
                        // Campos do formulário
                        descricao: '',
                        tipo: 'CAMARAO',
                        subtipo: '',
                        especie: '',
                        origem: 'CULTIVO',
                        classificacao: '',
                        conservacao: 'CRU',
                        congelamento: 'BLOCO',
                        categoria: '',
                        fator_producao: '',
                        validade_meses: '',
                        classe: ''
                    },

                    // ---- FUNÇÕES ----
                    init() {
                        // "Observa" a mudança do tipo de embalagem
                        this.$watch('tipoEmbalagem', (newVal) => {
                            if (newVal === 'SECUNDARIA' && this.produtosPrimarios.length === 0) {
                                this.carregarProdutosPrimarios();
                            }
                        });
                        // "Observa" qualquer mudança nos campos que afetam a classe
                        this.$watch('form', () => this.atualizarClasseProduto(), { deep: true });
                        this.atualizarClasseProduto(); // Roda uma vez no início
                    },

                    carregarProdutosPrimarios() {
                        fetch('{{ route("produtos.primarios") }}')
                            .then(response => response.json())
                            .then(data => {
                                this.produtosPrimarios = data;
                            });
                    },

                    selecionarProdutoPrimario(event) {
                        const produtoId = event.target.value;
                        if (!produtoId) { return; }
                        const produtoSelecionado = this.produtosPrimarios.find(p => p.prod_codigo == produtoId);

                        if (produtoSelecionado) {
                            this.form.descricao = produtoSelecionado.prod_descricao + ' (Caixa)';
                            this.form.tipo = produtoSelecionado.prod_tipo;
                            this.form.especie = produtoSelecionado.prod_especie;
                            this.form.origem = produtoSelecionado.prod_origem;
                            this.form.subtipo = produtoSelecionado.prod_subtipo;
                            this.form.classificacao = produtoSelecionado.prod_classificacao;
                            this.form.conservacao = produtoSelecionado.prod_conservacao;
                            this.form.congelamento = produtoSelecionado.prod_congelamento;
                            this.form.categoria = produtoSelecionado.prod_categoria;
                            this.form.fator_producao = produtoSelecionado.prod_fator_producao;
                            this.form.validade_meses = produtoSelecionado.prod_validade_meses;
                        }
                    },

                    atualizarClasseProduto() {
                        const f = this.form;
                        let partes = [];
                        if (f.tipo) partes.push(f.tipo);
                        if (f.tipo === 'CAMARAO') partes.push('CINZA');

                        let subtipoFormatado = '';
                        const subtipoUpper = f.subtipo ? f.subtipo.toUpperCase() : '';
                        if (['PUD'].includes(subtipoUpper)) subtipoFormatado = 'DESCASCADO';
                        else if (['P&D', 'PPV'].includes(subtipoUpper)) subtipoFormatado = 'DESCASCADO EVISCERADO';
                        else if (['P&D C/ CAUDA', 'PPV C/ CAUDA'].includes(subtipoUpper)) subtipoFormatado = 'DESCASCADO EVISCERADO COM CAUDA';
                        else if (['PUD C/ CAUDA'].includes(subtipoUpper)) subtipoFormatado = 'DESCASCADO COM CAUDA';
                        else subtipoFormatado = f.subtipo;
                        if (subtipoFormatado) partes.push(subtipoFormatado);

                        if (f.conservacao === 'COZIDO') partes.push('COZIDO');
                        else if (f.conservacao === 'PARC. COZIDO') partes.push('PARCIALMENTE COZIDO');

                        if (['IQF', 'BLOCO'].includes(f.congelamento)) partes.push('CONGELADO');

                        this.form.classe = partes.join(' ').toUpperCase();
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>