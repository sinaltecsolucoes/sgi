<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracaoController extends Controller
{
    /**
     * Mostra o formulário para editar as configurações.
     */
    public function edit()
    {
        // Busca a primeira (e única) linha de configuração da tabela.
        // Se não encontrar, cria uma nova em branco para o formulário não dar erro.
        $configuracao = Configuracao::firstOrCreate(
            [], // Condições para buscar (nenhuma, queremos a primeira)
            [   // Valores para usar na criação, caso não encontre
                'config_nome_empresa' => 'Preencha o Nome da Empresa',
                'config_nome_fantasia' => 'Preencha o Nome Fantasia'
            ]
        );
        // Retorna a view, passando os dados da configuração para ela.
        return view('configuracoes.edit', ['configuracao' => $configuracao]);
    }

    /**
     * Atualiza as configurações no banco de dados.
     */
    public function update(Request $request)
    {
        // Pega a primeira (e única) linha de configuração
        $configuracao = Configuracao::first();

        // 1. Validação de todos os campos do formulário
        $request->validate([
            'config_nome_empresa' => 'required|string|max:255',
            'config_nome_fantasia' => 'required|string|max:255',
            'config_cnpj' => 'nullable|string|max:18',
            'config_inscricao_estadual' => 'nullable|string|max:50',
            'config_logradouro' => 'nullable|string|max:255',
            'config_numero' => 'nullable|string|max:50',
            'config_complemento' => 'nullable|string|max:255',
            'config_cep' => 'nullable|string|max:9',
            'config_bairro' => 'nullable|string|max:100',
            'config_cidade' => 'nullable|string|max:100',
            'config_uf' => 'nullable|string|max:2',
            'config_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // 2. Lógica para o Upload do Logo
        $caminhoLogo = $configuracao->config_logo_path;
        if ($request->hasFile('config_logo')) {
            if ($caminhoLogo) {
                Storage::disk('public')->delete($caminhoLogo);
            }
            $caminhoLogo = $request->file('config_logo')->store('logos', 'public');
        }

        // 3. Prepara os dados para salvar (limpando as máscaras)
        $dadosParaAtualizar = $request->except(['_token', '_method', 'config_logo']);
        $dadosParaAtualizar['config_cnpj'] = limpar_mascara($request->config_cnpj);
        $dadosParaAtualizar['config_cep'] = limpar_mascara($request->config_cep);
        $dadosParaAtualizar['config_logo_path'] = $caminhoLogo;

        // 4. Atualiza os dados no banco
        $configuracao->update($dadosParaAtualizar);

        // 5. Redireciona com a mensagem de sucesso
        return redirect()->route('configuracoes.edit')
            ->with('success', 'Configurações salvas com sucesso!');
    }
}