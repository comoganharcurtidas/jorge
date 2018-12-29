<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriasRepo;
use App\Repositories\DominiosRepo;
use App\Repositories\MateriasRepo;
use Illuminate\Http\Request;

class MateriasController extends Controller
{
    private $materias;
    private $categorias;
    private $dominios;
    private $request;

    public function __construct(Request $request, MateriasRepo $materias, CategoriasRepo $categorias, DominiosRepo $dominios)
    {
        $this->materias = $materias;
        $this->categorias = $categorias;
        $this->dominios = $dominios;
        $this->request = $request;
    }

    public function index()
    {
        $titulo = 'Matérias';
        $materias = $this->materias->listar_materias();
        $categorias = $this->categorias->listar_categorias();
        $dominios = $this->dominios->listar_dominios_ativo();

        return view('painel.materias.index', compact('materias', 'categorias', 'dominios', 'titulo'));
    }

    public function pesquisar()
    {
        $categoria = $this->request->input('categoria');
        $dominio = $this->request->input('dominio');
        $palavra = $this->request->input('palavra');
        $resultado = $this->materias->pesquisar($categoria, $dominio, $palavra);

        return view('painel.materias.resultado', compact(['resultado']));
    }

    public function categorias()
    {
        $categorias = $this->categorias->categoria_dominio($this->request->input('site'));

        return view('painel.materias.categorias', compact(['categorias']));
    }
}
