<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
        <x-input-label for="prod_codigo_interno" value="Código Interno" />
        <x-text-input id="prod_codigo_interno" class="block mt-1 w-full" type="text" name="prod_codigo_interno"
            :value="old('prod_codigo_interno')" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="prod_descricao" value="Descrição do Produto *" />
        <x-text-input id="prod_descricao" class="block mt-1 w-full" type="text" name="prod_descricao"
            :value="old('prod_descricao')" required autofocus />
    </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="prod_tipo" value="Tipo *" />
        <select id="prod_tipo" name="prod_tipo" required
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="CAMARAO">Camarão</option>
            <option value="PEIXE">Peixe</option>
            <option value="POLVO">Polvo</option>
            <option value="LAGOSTA">Lagosta</option>
            <option value="OUTRO">Outro</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_subtipo" value="Subtipo (Ex: Sem Cabeça, P&D, Posta...)" />
        <x-text-input id="prod_subtipo" class="block mt-1 w-full" type="text" name="prod_subtipo"
            :value="old('prod_subtipo')" />
    </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="prod_origem" value="Origem" />
        <select id="prod_origem" name="prod_origem"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="CULTIVO">Cultivo</option>
            <option value="PESCA EXTRATIVA">Pesca Extrativa</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_especie" value="Espécie" />
        <x-text-input id="prod_especie" class="block mt-1 w-full" type="text" name="prod_especie"
            :value="old('prod_especie')" />
    </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
    <div>
        <x-input-label for="prod_conservacao" value="Conservação" />
        <select id="prod_conservacao" name="prod_conservacao"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="CRU">Cru</option>
            <option value="COZIDO">Cozido</option>
            <option value="PARC. COZIDO">Parc. Cozido</option>
            <option value="EMPANADO">Empanado</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_congelamento" value="Congelamento" />
        <select id="prod_congelamento" name="prod_congelamento"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="BLOCO">Bloco</option>
            <option value="IQF">IQF</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_categoria" value="Categoria (IQF)" />
        <select class="form-select" id="prod_categoria" name="prod_categoria"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
            <option value="">Nenhuma</option>
            <option value="A1">A1</option>
            <option value="A2">A2</option>
            <option value="A3">A3</option>
        </select>
    </div>
    <div>
        <x-input-label for="prod_fator_producao" value="Fator de Produção (%)" />        
        <x-text-input id="prod_fator_producao" class="block mt-1 w-full" type="number" step="0.01"
            name="prod_fator_producao" :value="old('prod_fator_producao')" />
    </div>
</div>

<div class="mt-4">
    <div>
        <x-input-label for="prod_classe" value="Classe (Descrição para Etiqueta)" />
        <x-text-input id="prod_classe" class="block mt-1 w-full" type="text" name="prod_classe"
            :value="old('prod_classe')" placeholder="Será calculado automaticamente..." />
    </div>
</div>