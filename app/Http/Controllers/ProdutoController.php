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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        //
    }
}
