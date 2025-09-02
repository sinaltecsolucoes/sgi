<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    /**
     * Retorna a lista de endereços de uma entidade em formato JSON.
     */
    public function index(Entidade $entidade)
    {
        return response()->json($entidade->enderecos()->orderBy('end_tipo_endereco')->get());
    }

    /**
     * Salva um novo endereço para uma entidade.
     */
    public function store(Request $request, Entidade $entidade)
    {
        $validatedData = $request->validate([
            'end_tipo_endereco' => 'required|string|max:255',
            'end_cep' => 'nullable|string|max:9',
            'end_logradouro' => 'nullable|string|max:255',
            'end_numero' => 'nullable|string|max:50',
            'end_complemento' => 'nullable|string|max:255',
            'end_bairro' => 'nullable|string|max:100',
            'end_cidade' => 'nullable|string|max:100',
            'end_uf' => 'nullable|string|max:2',
        ]);

        // Adiciona o ID do usuário logado aos dados
        $validatedData['end_usuario_cadastro_id'] = auth()->id();
        // Limpa a máscara do CEP
        $validatedData['end_cep'] = limpar_mascara($validatedData['end_cep']);

        // Usa o relacionamento para criar o novo endereço já associado à entidade correta
        $entidade->enderecos()->create($validatedData);

        // Retorna uma resposta de sucesso em JSON para o nosso JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Endereço adicionado com sucesso!'
        ]);
    }

    /**
     * Atualiza um endereço existente.
     */
    public function update(Request $request, Entidade $entidade, Endereco $endereco)
    {
        $validatedData = $request->validate([
            'end_tipo_endereco' => 'required|string|max:255',
            'end_cep' => 'nullable|string|max:9',
            'end_logradouro' => 'nullable|string|max:255',
            'end_numero' => 'nullable|string|max:50',
            'end_complemento' => 'nullable|string|max:255',
            'end_bairro' => 'nullable|string|max:100',
            'end_cidade' => 'nullable|string|max:100',
            'end_uf' => 'nullable|string|max:2',
        ]);

        $validatedData['end_cep'] = limpar_mascara($validatedData['end_cep']);

        // O Laravel já nos entrega o $endereco correto para atualizar
        $endereco->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Endereço atualizado com sucesso!'
        ]);
    }

    /**
     * Remove o endereço especificado do banco de dados.
     */
    public function destroy(Entidade $entidade, Endereco $endereco)
    {
        // Regra de negócio: Não permitir a exclusão do endereço Principal.
        if ($endereco->end_tipo_endereco === 'Principal') {
            return response()->json([
                'success' => false,
                'message' => 'O endereço Principal não pode ser excluído.'
            ], 422); // Código de erro para entidade não processável
        }

        // Apaga o registro do banco de dados
        $endereco->delete();

        // Retorna uma resposta de sucesso em JSON
        return response()->json([
            'success' => true,
            'message' => 'Endereço excluído com sucesso!'
        ]);
    }
}