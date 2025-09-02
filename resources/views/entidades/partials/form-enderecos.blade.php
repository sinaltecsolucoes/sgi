<div class="flex justify-end mb-4">
    <button @click="adicionarNovoEndereco()" x-show="!exibindoFormularioEndereco"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Adicionar Novo Endereço
    </button>
</div>

<div x-show="exibindoFormularioEndereco" x-transition
    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border dark:border-gray-600 mb-6">
    <h4 class="text-lg font-semibold mb-4" x-text="editandoEndereco ? 'Editar Endereço' : 'Novo Endereço'"></h4>

    <form @submit.prevent="salvarEndereco">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input-label for="end_tipo_endereco" value="Tipo *" />
                <select x-model="formEndereco.end_tipo_endereco" id="end_tipo_endereco" required
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                    <option value="Principal">Principal</option>
                    <option value="Comercial">Comercial</option>
                    <option value="Entrega">Entrega</option>
                    <option value="Cobranca">Cobrança</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div>
                <x-input-label for="end_cep_adicional" value="CEP" />
                <div class="flex items-center space-x-2">
                    <x-text-input id="end_cep_adicional" class="block mt-1 w-full" type="text"
                        x-model="formEndereco.end_cep" x-mask="99999-999" />
                    <button @click.prevent="buscarCEP" type="button"
                        class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">Buscar</button>
                </div>
            </div>
        </div>
        <div x-text="feedbackCEP" class="text-sm mt-1 h-4"></div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <x-input-label for="end_logradouro_adicional" value="Logradouro" />
                <x-text-input id="end_logradouro_adicional" class="block mt-1 w-full" type="text"
                    x-model="formEndereco.end_logradouro" />
            </div>
            <div>
                <x-input-label for="end_numero_adicional" value="Número" />
                <x-text-input id="end_numero_adicional" class="block mt-1 w-full" type="text"
                    x-model="formEndereco.end_numero" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="end_complemento_adicional" value="Complemento" />
            <x-text-input id="end_complemento_adicional" class="block mt-1 w-full" type="text"
                x-model="formEndereco.end_complemento" />
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="md:col-span-2">
                <x-input-label for="end_bairro_adicional" :value="__('Bairro')" />
                <x-text-input id="end_bairro_adicional" class="block mt-1 w-full" type="text"
                    x-model="formEndereco.end_bairro" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="end_cidade_adicional" :value="__('Cidade')" />
                <x-text-input id="end_cidade_adicional" class="block mt-1 w-full" type="text"
                    x-model="formEndereco.end_cidade" />
            </div>
            <div>
                <x-input-label for="end_uf_adicional" :value="__('UF')" />
                <x-text-input id="end_uf_adicional" class="block mt-1 w-full" type="text"
                    x-model="formEndereco.end_uf" />
            </div>
        </div>

        <div class="flex justify-end mt-4 space-x-3">
            <button type="button" @click="cancelarFormularioEndereco()"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
            <x-primary-button type="submit" x-text="editandoEndereco ? 'Atualizar Endereço' : 'Salvar Endereço'">
            </x-primary-button>
        </div>
    </form>
</div>

<div class="mt-6 overflow-x-auto">
    <h4 class="text-lg font-semibold mb-3">Endereços Cadastrados</h4>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Tipo</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Logradouro</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Cidade/UF</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <template x-if="enderecos.length === 0">
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        Nenhum endereço cadastrado.
                    </td>
                </tr>
            </template>
            <template x-for="endereco in enderecos" :key="endereco.end_codigo">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="endereco.end_tipo_endereco"></td>
                    <td class="px-6 py-4 whitespace-nowrap"
                        x-text="`${endereco.end_logradouro || ''}, ${endereco.end_numero || ''}`"></td>
                    <td class="px-6 py-4 whitespace-nowrap"
                        x-text="`${endereco.end_cidade || ''}/${endereco.end_uf || ''}`"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="editarEndereco(endereco)"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Editar</button>
                        <button @click="excluirEndereco(endereco.end_codigo)"
                            class="text-red-600 dark:text-red-400 hover:text-red-900 ml-4">Excluir</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>