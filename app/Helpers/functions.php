<?php

if (!function_exists('limpar_mascara')) {
    /**
     * Remove todos os caracteres não numéricos de uma string.
     *
     * @param string|null $valor
     * @return string|null
     */
    function limpar_mascara(?string $valor): ?string
    {
        if (is_null($valor)) {
            return null;
        }
        // Usa uma expressão regular para remover tudo que não for (^) um dígito de 0-9.
        return preg_replace('/[^0-9]/', '', $valor);
    }

    if (!function_exists('formatar_documento')) {
        /**
         * Formata um número de CPF ou CNPJ com a máscara apropriada.
         *
         * @param string|null $documento O número do documento (apenas dígitos).
         * @return string|null O documento formatado ou nulo.
         */
        function formatar_documento(?string $documento): ?string
        {
            if (is_null($documento)) {
                return null;
            }

            $length = strlen($documento);

            if ($length === 11) { // CPF
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $documento);
            }

            if ($length === 14) { // CNPJ
                return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $documento);
            }

            return $documento; // Retorna o original se não for CPF/CNPJ
        }
    }
}