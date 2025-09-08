<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $produtosQuery = Produto::query();

            if ($request->filled('filtro_situacao') && $request->input('filtro_situacao') !== 'Todos') {
                $produtosQuery->where('prod_situacao', $request->input('filtro_situacao'));
            }

            return DataTables::of($produtosQuery)
                ->addColumn('acoes', function ($produto) {
                    $editRoute = route('produtos.edit', $produto);
                    $destroyRoute = route('produtos.destroy', $produto);

                    $actionButtons = '<a href="' . $editRoute . '" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Editar</a>';
                    // Futuramente, adicionaremos o botão de exclusão
                    return $actionButtons;
                })
                ->editColumn('prod_situacao', function ($produto) {
                    return $produto->prod_situacao === 'A' ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>' : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>';
                })
                ->rawColumns(['acoes', 'prod_situacao'])
                ->make(true);
        }

        return view('produtos.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validação de todos os campos do formulário
        $validatedData = $request->validate([
            'prod_descricao' => 'required|string|max:255',
            'prod_codigo_interno' => 'nullable|string|max:50',
            'prod_tipo' => 'required|string',
            'prod_subtipo' => 'nullable|string|max:100',
            'prod_classificacao' => 'nullable|string|max:100',
            'prod_categoria' => 'nullable|string|max:30',
            'prod_classe' => 'nullable|string|max:255',
            'prod_especie' => 'nullable|string|max:100',
            'prod_origem' => 'nullable|string',
            'prod_conservacao' => 'nullable|string',
            'prod_congelamento' => 'nullable|string',
            'prod_fator_producao' => 'nullable|numeric',
            'prod_tipo_embalagem' => 'required|string',
            'prod_especie_embalagem' => 'nullable|string|max:50',
            'prod_validade_meses' => 'nullable|integer',
            'prod_peso_embalagem' => 'required|string', // String para aceitar vírgula
            'prod_total_pecas' => 'nullable|string|max:50',
            'prod_ean13' => 'nullable|string|max:13',
            'prod_primario_id' => 'nullable|required_if:prod_tipo_embalagem,SECUNDARIA|integer',
            'prod_dun14' => 'nullable|required_if:prod_tipo_embalagem,SECUNDARIA|string|max:14',
        ]);

        // 2. Preparar os dados para salvar
        $dataToSave = $validatedData;

        // Trata o campo de situação (checkbox)
        $dataToSave['prod_situacao'] = $request->has('prod_situacao') ? 'A' : 'I';

        // Converte vírgula para ponto para campos decimais, se necessário
        if (isset($dataToSave['prod_peso_embalagem'])) {
            $dataToSave['prod_peso_embalagem'] = str_replace(',', '.', $dataToSave['prod_peso_embalagem']);
        }
        if (isset($dataToSave['prod_fator_producao'])) {
            $dataToSave['prod_fator_producao'] = str_replace(',', '.', $dataToSave['prod_fator_producao']);
        }

        // 3. Salvar o novo produto no Banco de Dados
        \App\Models\Produto::create($dataToSave);

        // 4. Redirecionar para a lista de produtos com mensagem de sucesso
        return redirect()->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        // 1. Validação
        $validatedData = $request->validate([
            'prod_descricao' => 'required|string|max:255',
            'prod_codigo_interno' => 'nullable|string|max:50',
            'prod_tipo' => 'required|string',
            'prod_subtipo' => 'nullable|string|max:100',
            'prod_classificacao' => 'nullable|string|max:100',
            'prod_categoria' => 'nullable|string|max:30',
            'prod_classe' => 'nullable|string|max:255',
            'prod_especie' => 'nullable|string|max:100',
            'prod_origem' => 'nullable|string',
            'prod_conservacao' => 'nullable|string',
            'prod_congelamento' => 'nullable|string',
            'prod_fator_producao' => 'nullable|numeric',
            'prod_tipo_embalagem' => 'required|string',
            'prod_especie_embalagem' => 'nullable|string|max:50',
            'prod_validade_meses' => 'nullable|integer',
            'prod_peso_embalagem' => 'required|string',
            'prod_total_pecas' => 'nullable|string|max:50',
            'prod_ean13' => 'nullable|string|max:13',
            'prod_primario_id' => 'nullable|required_if:prod_tipo_embalagem,SECUNDARIA|integer',
            'prod_dun14' => 'nullable|required_if:prod_tipo_embalagem,SECUNDARIA|string|max:14',
        ]);

        // 2. Preparar os dados para atualizar
        $dataToSave = $validatedData;
        $dataToSave['prod_situacao'] = $request->has('prod_situacao') ? 'A' : 'I';

        if (isset($dataToSave['prod_peso_embalagem'])) {
            $dataToSave['prod_peso_embalagem'] = str_replace(',', '.', $dataToSave['prod_peso_embalagem']);
        }
        if (isset($dataToSave['prod_fator_producao'])) {
            $dataToSave['prod_fator_producao'] = str_replace(',', '.', $dataToSave['prod_fator_producao']);
        }

        // Limpa campos específicos se a embalagem for alterada para primária
        if ($dataToSave['prod_tipo_embalagem'] === 'PRIMARIA') {
            $dataToSave['prod_primario_id'] = null;
            $dataToSave['prod_dun14'] = null;
        }

        // 3. Atualizar o produto no Banco de Dados
        $produto->update($dataToSave);

        // 4. Redirecionar com mensagem de sucesso
        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        //
    }

    /**
     * Retorna uma lista de produtos primários para popular selects.
     */
    public function getProdutosPrimarios()
    {
        $produtos = \App\Models\Produto::where('prod_tipo_embalagem', 'PRIMARIA')
            ->where('prod_situacao', 'A')
            ->orderBy('prod_descricao')
            ->get();
        return response()->json($produtos);
    }

    /**
     * Busca produtos primários para o Select2 com base em um termo de pesquisa.
     */
    public function buscarProdutosPrimarios(Request $request)
    {
        $termoBusca = $request->input('term');

        $produtos = \App\Models\Produto::where('prod_tipo_embalagem', 'PRIMARIA')
            ->where('prod_situacao', 'A')
            ->where(function ($query) use ($termoBusca) {
                $query->where('prod_descricao', 'like', '%' . $termoBusca . '%')
                    ->orWhere('prod_codigo_interno', 'like', '%' . $termoBusca . '%');
            })
            ->orderBy('prod_descricao')
            ->limit(20) // Limita a quantidade de resultados
            ->get();

        // Formata os dados para o padrão que o Select2 espera
        $resultadosFormatados = $produtos->map(function ($produto) {
            return [
                'id' => $produto->prod_codigo,
                'text' => "{$produto->prod_codigo_interno} - {$produto->prod_descricao} ({$produto->prod_peso_embalagem} kg)"
            ];
        });

        return response()->json(['results' => $resultadosFormatados]);
    }
}
