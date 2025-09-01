<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EntidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pega o nome da rota que foi acessada
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        $pageTitle = 'Gerenciamento de Entidades'; // Título Padrão
        $entidadesQuery = \App\Models\Entidade::query();

        if ($routeName === 'clientes.index') {
            $pageTitle = 'Gerenciamento de Clientes';
            // Busca entidades que são 'Cliente' OU 'Cliente e Fornecedor'
            $entidadesQuery->whereIn('ent_tipo_entidade', ['Cliente', 'Cliente e Fornecedor']);
        } elseif ($routeName === 'fornecedores.index') {
            $pageTitle = 'Gerenciamento de Fornecedores';
            // Busca entidades que são 'Fornecedor' OU 'Cliente e Fornecedor'
            $entidadesQuery->whereIn('ent_tipo_entidade', ['Fornecedor', 'Cliente e Fornecedor']);
        }

        $entidades = $entidadesQuery->paginate(15);

        // Envia os dados para a view
        return view('entidades.index', [
            'entidades' => $entidades,
            'pageTitle' => $pageTitle,
            'routeName' => $routeName
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pega o nome da rota atual para saber o que estamos criando
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();

        // Define o tipo com base na rota
        $tipo = Str::contains($routeName, 'clientes') ? 'Cliente' : 'Fornecedor';

        return view('entidades.create', [
            'pageTitle' => 'Novo ' . $tipo,
            'tipoEntidade' => $tipo,
            'routeName' => $routeName // <-- ADICIONE ESTA LINHA
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validação de todos os campos do formulário
        $validatedData = $request->validate([
            'ent_tipo_entidade' => 'required|in:Cliente,Fornecedor,Cliente e Fornecedor',
            'ent_tipo_pessoa' => 'required|in:F,J',
            'ent_situacao' => 'nullable|string',
            'ent_cnpj' => 'nullable|required_if:ent_tipo_pessoa,J|string|max:18',
            'ent_cpf' => 'nullable|required_if:ent_tipo_pessoa,F|string|max:14',
            'ent_inscricao_estadual' => 'nullable|string|max:50',
            'ent_codigo_interno' => 'nullable|string|max:255',
            'ent_razao_social' => 'required|string|max:255',
            'ent_nome_fantasia' => 'nullable|string|max:255',
            'end_cep' => 'nullable|string|max:9',
            'end_logradouro' => 'nullable|string|max:255',
            'end_numero' => 'nullable|string|max:50',
            'end_complemento' => 'nullable|string|max:255',
            'end_bairro' => 'nullable|string|max:100',
            'end_cidade' => 'nullable|string|max:100',
            'end_uf' => 'nullable|string|max:2',
        ]);

        try {
            // Inicia uma transação para garantir a integridade dos dados
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 2. Cria a Entidade principal
            $entidade = \App\Models\Entidade::create([
                'ent_tipo_pessoa' => $validatedData['ent_tipo_pessoa'],
                'ent_razao_social' => $validatedData['ent_razao_social'],
                'ent_nome_fantasia' => $validatedData['ent_nome_fantasia'] ?? null,
                'ent_cpf' => limpar_mascara($validatedData['ent_cpf'] ?? null),
                'ent_cnpj' => limpar_mascara($validatedData['ent_cnpj'] ?? null),
                'ent_inscricao_estadual' => $validatedData['ent_inscricao_estadual'] ?? null,
                'ent_codigo_interno' => $validatedData['ent_codigo_interno'] ?? null,
                'ent_tipo_entidade' => $validatedData['ent_tipo_entidade'],
                'ent_situacao' => $request->has('ent_situacao') ? 'A' : 'I', // Se o checkbox estiver marcado, é 'A'
                'ent_usuario_cadastro_id' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // 3. Cria o Endereço Principal (se o CEP foi preenchido)
            if ($request->filled('end_cep')) {
                $entidade->enderecos()->create([
                    'end_tipo_endereco' => 'Principal',
                    'end_cep' => limpar_mascara($request->end_cep),
                    'end_logradouro' => $request->end_logradouro,
                    'end_numero' => $request->end_numero,
                    'end_complemento' => $request->end_complemento,
                    'end_bairro' => $request->end_bairro,
                    'end_cidade' => $request->end_cidade,
                    'end_uf' => $request->end_uf,
                    'end_usuario_cadastro_id' => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            // 4. Cria os registros nas tabelas relacionadas (Cliente/Fornecedor)
            $tipoEntidade = $validatedData['ent_tipo_entidade'];
            if ($tipoEntidade === 'Cliente' || $tipoEntidade === 'Cliente e Fornecedor') {
                $entidade->cliente()->create(['cli_usuario_cadastro_id' => \Illuminate\Support\Facades\Auth::id()]);
            }
            if ($tipoEntidade === 'Fornecedor' || $tipoEntidade === 'Cliente e Fornecedor') {
                $entidade->fornecedor()->create(['forn_usuario_cadastro_id' => \Illuminate\Support\Facades\Auth::id()]);
            }

            // Se tudo deu certo, confirma as operações no banco
            \Illuminate\Support\Facades\DB::commit();

            // 5. Redireciona para a lista correta com mensagem de sucesso
            $redirectRoute = Str::contains($tipoEntidade, 'Cliente') ? 'clientes.index' : 'fornecedores.index';
            return redirect()->route($redirectRoute)
                ->with('success', $tipoEntidade . ' cadastrado(a) com sucesso!');

        } catch (\Exception $e) {
            // Se algo deu errado, desfaz tudo
            \Illuminate\Support\Facades\DB::rollBack();
            // Loga o erro para futura análise
            \Log::error('Erro ao salvar entidade: ' . $e->getMessage());
            // Retorna ao formulário com uma mensagem de erro para o usuário
            return back()->withInput()->with('error', 'Ocorreu um erro ao salvar. Verifique os dados e tente novamente.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Entidade $entidade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entidade $entidade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entidade $entidade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entidade $entidade)
    {
        //
    }
}
