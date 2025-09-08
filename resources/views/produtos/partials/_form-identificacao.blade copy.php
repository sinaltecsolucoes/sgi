<div class="grid grid-cols-1 md:grid-cols-6 gap-6">

    <div class="md:col-span-1">
        <x-input-label for="prod_codigo_interno" value="Código Interno" />
        <x-text-input id="prod_codigo_interno" class="block mt-1 w-full" type="text" name="prod_codigo_interno"
            :value="old('prod_codigo_interno')" />
    </div>
    <div class="md:col-span-4">
        <x-input-label for="prod_descricao" value="Descrição do Produto *" />
        <x-text-input id="prod_descricao" class="block mt-1 w-full" type="text" name="prod_descricao"
            :value="old('prod_descricao')" x-model="form.descricao" required autofocus />
    </div>
    <div class="md:col-span-1">
        <x-input-label for="prod_situacao" value="Situação" />
        <label for="prod_situacao" class="inline-flex items-center mt-2 cursor-pointer">
            <input id="prod_situacao" type="checkbox" name="prod_situacao" class="sr-only peer" value="A" checked>
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Ativo</span>
        </label>
    </div>
</div>