<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class EntidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /* public function index()
     {
         // Pega o nome da rota que foi acessada
         $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
         $pageTitle = 'Gerenciamento de Entidades'; // Título Padrão
         $entidadesQuery = \App\Models\Entidade::with('enderecos');

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
     }*/

    public function index(Request $request)
    {
        // Esta parte lida com a requisição AJAX do DataTables
        if ($request->ajax()) {
            $routeName = $request->get('routeName'); // Recebe o nome da rota via AJAX

            // Inicia a consulta base
            $entidadesQuery = \App\Models\Entidade::with('enderecos');

            // Filtro base: Clientes ou Fornecedores
            if ($routeName === 'clientes.index') {
                $entidadesQuery->whereIn('ent_tipo_entidade', ['Cliente', 'Cliente e Fornecedor']);
            } elseif ($routeName === 'fornecedores.index') {
                $entidadesQuery->whereIn('ent_tipo_entidade', ['Fornecedor', 'Cliente e Fornecedor']);
            }

            // Aplica os filtros customizados que virão do formulário
            if ($request->filled('filtro_situacao') && $request->input('filtro_situacao') !== 'Todos') {
                $entidadesQuery->where('ent_situacao', $request->input('filtro_situacao'));
            }
            if ($request->filled('filtro_tipo_entidade') && $request->input('filtro_tipo_entidade') !== 'Todos') {
                $entidadesQuery->where('ent_tipo_entidade', $request->input('filtro_tipo_entidade'));
            }

            // Usa o pacote DataTables para processar a consulta e retornar o JSON
            return \Yajra\DataTables\Facades\DataTables::of($entidadesQuery)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !is_null($request->input('search')['value'])) {
                        $searchValue = $request->input('search')['value'];
                        // Agrupa as condições de busca com OR
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('ent_razao_social', 'like', "%{$searchValue}%")
                                ->orWhere('ent_nome_fantasia', 'like', "%{$searchValue}%")
                                ->orWhere('ent_cnpj', 'like', "%{$searchValue}%")
                                ->orWhere('ent_cpf', 'like', "%{$searchValue}%")
                                ->orWhere('ent_codigo_interno', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->addColumn('documento_formatado', function ($entidade) {
                    return formatar_documento($entidade->ent_tipo_pessoa == 'J' ? $entidade->ent_cnpj : $entidade->ent_cpf);
                })
                ->addColumn('endereco_principal', function ($entidade) {
                    $endereco = $entidade->enderecos->firstWhere('end_tipo_endereco', 'Principal');
                    return $endereco ? $endereco->end_logradouro . ', ' . $endereco->end_numero : '-';
                })
                ->addColumn('acoes', function ($entidade) use ($routeName) {
                    $editRoute = \Illuminate\Support\Str::contains($routeName, 'clientes') ? route('clientes.edit', $entidade) : route('fornecedores.edit', $entidade);
                    $destroyRoute = \Illuminate\Support\Str::contains($routeName, 'clientes') ? route('clientes.destroy', $entidade) : route('fornecedores.destroy', $entidade);

                    $actionButtons = '<a href="' . $editRoute . '" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Editar</a>';
                    $actionButtons .= '<form action="' . $destroyRoute . '" method="POST" class="inline form-inativar"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 ml-4">Inativar</button></form>';
                    return $actionButtons;
                })
                ->rawColumns(['acoes']) // Diz ao DataTables para não escapar o HTML na coluna de ações
                ->make(true);
        }

        // Esta parte lida com o carregamento inicial da página (não AJAX)
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        $pageTitle = \Illuminate\Support\Str::contains($routeName, 'clientes') ? 'Gerenciamento de Clientes' : 'Gerenciamento de Fornecedores';

        return view('entidades.index', [
            'pageTitle' => $pageTitle,
            'routeName' => $routeName,
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
        // dd($request->all());
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

            // 2. Cria a Entidade principal (com os campos já limpos)
            $entidade = \App\Models\Entidade::create([
                'ent_tipo_pessoa' => $validatedData['ent_tipo_pessoa'],
                'ent_razao_social' => $validatedData['ent_razao_social'],
                'ent_nome_fantasia' => $validatedData['ent_nome_fantasia'] ?? null,
                'ent_cpf' => limpar_mascara($validatedData['ent_cpf'] ?? null),
                'ent_cnpj' => limpar_mascara($validatedData['ent_cnpj'] ?? null),
                'ent_inscricao_estadual' => $validatedData['ent_inscricao_estadual'] ?? null,
                'ent_codigo_interno' => $validatedData['ent_codigo_interno'] ?? null,
                'ent_tipo_entidade' => $validatedData['ent_tipo_entidade'],
                'ent_situacao' => $request->has('ent_situacao') ? 'A' : 'I',
                'ent_usuario_cadastro_id' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // 3. Cria o Endereço Principal (com o CEP limpo)
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
            $redirectRoute = \Illuminate\Support\Str::contains($tipoEntidade, 'Cliente') ? 'clientes.index' : 'fornecedores.index';
            return redirect()->route($redirectRoute)
                ->with('success', $tipoEntidade . ' cadastrado(a) com sucesso!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Erro ao salvar entidade: ' . $e->getMessage());
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
        // Carrega a entidade junto com seus endereços
        $entidade->load('enderecos');

        // Pega o nome da rota atual (clientes.edit ou fornecedores.edit)
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();

        // Define o tipo com base no nome da rota
        $tipo = \Illuminate\Support\Str::contains($routeName, 'clientes') ? 'Cliente' : 'Fornecedor';

        // Retorna a view de edição, passando a entidade e outras infos
        return view('entidades.edit', [
            'pageTitle' => 'Editar ' . $tipo . ': ' . $entidade->ent_nome_fantasia,
            'tipoEntidade' => $tipo,
            'routeName' => $routeName,
            'entidade' => $entidade // A entidade a ser editada
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entidade $entidade)
    {
        // 1. Validação dos dados (com regra 'unique' ajustada para ignorar o registro atual)
        $validatedData = $request->validate([
            'ent_tipo_entidade' => 'required|in:Cliente,Fornecedor,Cliente e Fornecedor',
            'ent_tipo_pessoa' => 'required|in:F,J',
            'ent_situacao' => 'nullable|string',
            'ent_cnpj' => ['nullable', 'required_if:ent_tipo_pessoa,J', 'string', 'max:18', \Illuminate\Validation\Rule::unique('tbl_entidades', 'ent_cnpj')->ignore($entidade->ent_codigo, 'ent_codigo')],
            'ent_cpf' => ['nullable', 'required_if:ent_tipo_pessoa,F', 'string', 'max:14', \Illuminate\Validation\Rule::unique('tbl_entidades', 'ent_cpf')->ignore($entidade->ent_codigo, 'ent_codigo')],
            'ent_inscricao_estadual' => 'nullable|string|max:50',
            'ent_codigo_interno' => 'nullable|string|max:255',
            'ent_razao_social' => 'required|string|max:255',
            'ent_nome_fantasia' => 'nullable|string|max:255',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 2. Atualiza a Entidade principal
            $entidade->update([
                'ent_tipo_pessoa' => $validatedData['ent_tipo_pessoa'],
                'ent_razao_social' => $validatedData['ent_razao_social'],
                'ent_nome_fantasia' => $validatedData['ent_nome_fantasia'] ?? null,
                'ent_cpf' => limpar_mascara($validatedData['ent_cpf'] ?? null),
                'ent_cnpj' => limpar_mascara($validatedData['ent_cnpj'] ?? null),
                'ent_inscricao_estadual' => $validatedData['ent_inscricao_estadual'] ?? null,
                'ent_codigo_interno' => $validatedData['ent_codigo_interno'] ?? null,
                'ent_tipo_entidade' => $validatedData['ent_tipo_entidade'],
                'ent_situacao' => $request->has('ent_situacao') ? 'A' : 'I',
            ]);

            // 3. Gerencia as tabelas relacionadas (Cliente/Fornecedor) com base na mudança de tipo
            $novoTipo = $validatedData['ent_tipo_entidade'];
            $authId = \Illuminate\Support\Facades\Auth::id();

            // Lógica para Cliente
            if ($novoTipo === 'Cliente' || $novoTipo === 'Cliente e Fornecedor') {
                $entidade->cliente()->firstOrCreate(['cli_entidade_id' => $entidade->ent_codigo], ['cli_usuario_cadastro_id' => $authId]);
            } else {
                $entidade->cliente()->delete();
            }

            // Lógica para Fornecedor
            if ($novoTipo === 'Fornecedor' || $novoTipo === 'Cliente e Fornecedor') {
                $entidade->fornecedor()->firstOrCreate(['forn_entidade_id' => $entidade->ent_codigo], ['forn_usuario_cadastro_id' => $authId]);
            } else {
                $entidade->fornecedor()->delete();
            }

            \Illuminate\Support\Facades\DB::commit();

            // 4. Redireciona para a lista correta com mensagem de sucesso
            $redirectRoute = \Illuminate\Support\Str::contains($novoTipo, 'Cliente') ? 'clientes.index' : 'fornecedores.index';
            return redirect()->route($redirectRoute)
                ->with('success', $novoTipo . ' atualizado(a) com sucesso!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Erro ao atualizar entidade: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao atualizar. Tente novamente.');
        }
    }

    /**
     * Inativa (soft delete) a entidade especificada.
     */
    public function destroy(Entidade $entidade)
    {
        try {
            $entidade->update(['ent_situacao' => 'I']);

            $tipoEntidade = $entidade->ent_tipo_entidade;
            $redirectRoute = \Illuminate\Support\Str::contains($tipoEntidade, 'Cliente') ? 'clientes.index' : 'fornecedores.index';

            return redirect()->route($redirectRoute)
                ->with('success', $tipoEntidade . ' inativado(a) com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao inativar entidade: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao inativar a entidade.');
        }
    }

    public function verificarDuplicidade(Request $request)
    {
        $documento = limpar_mascara($request->input('documento'));
        $tipo = $request->input('tipo'); // 'cpf' ou 'cnpj'

        if (!$documento || !$tipo) {
            return response()->json(['existe' => false]);
        }

        // Busca a entidade pelo CPF ou CNPJ
        $entidade = \App\Models\Entidade::where('ent_' . $tipo, $documento)->first();

        if ($entidade) {
            // Se encontrou, retorna que existe e o link para a edição
            $editRoute = Str::contains($entidade->ent_tipo_entidade, 'Cliente') ? route('clientes.edit', $entidade) : route('fornecedores.edit', $entidade);
            return response()->json([
                'existe' => true,
                'entidade_id' => $entidade->ent_codigo,
                'edit_url' => $editRoute
            ]);
        }

        return response()->json(['existe' => false]);
    }
}
