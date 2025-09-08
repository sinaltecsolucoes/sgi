<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 mt-6">
    Detalhes da Embalagem
</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <x-input-label for="prod_tipo_embalagem" value="Tipo de Embalagem *" />
        <select id="prod_tipo_embalagem" name="prod_tipo_embalagem" x-model="tipoEmbalagem" required
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="PRIMARIA">Primária</option>
            <option value="SECUNDARIA">Secundária</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_especie_embalagem" value="Espécie da Embalagem" />
        <select id="prod_especie_embalagem" name="prod_especie_embalagem"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="">Selecione...</option>
            <option value="SCS">Sacos (SCS)</option>
            <option value="CXS">Caixas (CXS)</option>
            <option value="PCT">Pacotes (PCT)</option>
            <option value="UN">Unidade (UN)</option>
            <option value="OUTRO">Outro</option>
        </select>
    </div>
</div>

{{-- Bloco Secundário: usa x-if para ser REMOVIDO do DOM quando inativo --}}
<template x-if="tipoEmbalagem === 'SECUNDARIA'">
    <div x-transition class="mt-4 p-4 border rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-input-label for="prod_primario_id" value="Produto Primário Contido *" />
                <select id="prod_primario_id" name="prod_primario_id" x-bind:required="tipoEmbalagem === 'SECUNDARIA'"
                    @change="selecionarProdutoPrimario($event)"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                    <option value="">Selecione...</option>
                    <template x-for="produto in produtosPrimarios" :key="produto.prod_codigo">
                        <option :value="produto.prod_codigo"
                            x-text="`${produto.prod_codigo_interno} - ${produto.prod_descricao} (${produto.prod_peso_embalagem} kg)`">
                        </option>
                    </template>
                </select>
                <x-input-error :messages="$errors->get('prod_primario_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="prod_peso_embalagem_sec" value="Peso da Caixa (kg) *" />
                <x-text-input id="prod_peso_embalagem_sec" class="block mt-1 w-full" type="text"
                    name="prod_peso_embalagem" x-bind:required="tipoEmbalagem === 'SECUNDARIA'" />
            </div>
            <div>
                <x-input-label for="prod_dun14" value="Cód. Barras Caixa (DUN-14)" />
                <x-text-input id="prod_dun14" class="block mt-1 w-full" type="text" name="prod_dun14" maxlength="14" />
            </div>
        </div>
    </div>
</template>

{{-- Bloco Primário: usa x-if para ser REMOVIDO do DOM quando inativo --}}
<template x-if="tipoEmbalagem === 'PRIMARIA'">
    <div x-transition class="mt-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-input-label for="prod_peso_embalagem_prim" value="Peso da Embalagem (kg) *" />
                <x-text-input id="prod_peso_embalagem_prim" class="block mt-1 w-full" type="text"
                    name="prod_peso_embalagem" x-bind:required="tipoEmbalagem === 'PRIMARIA'" />
            </div>
            <div>
                <x-input-label for="prod_total_pecas" value="Total de Peças (Ex: 55 a 60)" />
                <x-text-input id="prod_total_pecas" class="block mt-1 w-full" type="text" name="prod_total_pecas" />
            </div>
            <div>
                <x-input-label for="prod_ean13" value="Cód. Barras Unidade (EAN-13)" />
                <x-text-input id="prod_ean13" class="block mt-1 w-full" type="text" name="prod_ean13" maxlength="13" />
            </div>
        </div>
    </div>
</template>