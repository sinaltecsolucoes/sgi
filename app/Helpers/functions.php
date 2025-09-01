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
}