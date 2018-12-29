<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriasRepo;
use App\Repositories\DominiosRepo;
use App\Repositories\MateriasRepo;
use App\Repositories\UsersRepo;
use Illuminate\Http\Request;
use Validator;

class RedatorController extends Controller
{
    private $request;
    private $usuarios;
    private $categoria;
    private $dominio;
    private $materias;

    public function __construct(Request $request, UsersRepo $usuarios, CategoriasRepo $categoria, DominiosRepo $dominio, MateriasRepo $materias)
    {
        $this->usuarios = $usuarios;
        $this->request = $request;
        $this->categoria = $categoria;
        $this->dominio = $dominio;
        $this->materias = $materias;
    }

    public function index()
    {
        $titulo = 'Criar Matéria';
        $blogs = $this->dominio->listar_dominios();

        return view('painel.redator.criar.index', compact('titulo', 'blogs'));
    }

    public function escrever_materia($dominio_id)
    {
        $titulo = 'Criar Matéria';
        $dominio = $this->dominio->dominio_id($dominio_id);

        if (empty($dominio)) {
            return redirect('/redator/criar-materia');
        }

        $categorias = $this->categoria->categoria_dominio($dominio->url_blog);

        return view('painel.redator.criar.escrever', compact('titulo', 'categorias', 'dominio', 'dominio_id'));
    }

    public function enviar()
    {
        $validator = Validator::make($this->request->all(), [
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'texto' => 'required|min:1500',
            'titulo' => 'required',
            'categoria' => 'required',
        ]);

        if ($this->request->hasFile('img')) {
            $image = $this->request->file('img');
            $foto_nome = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images_capa';
            $image->move($destinationPath, $foto_nome);
        }

        $this->materias->criar_materia_redator([
            'uid' => $this->request->user()->id,
            'titulo' => $this->request->input('titulo'),
            'cid' => $this->request->input('categoria'),
            'did' => $this->request->input('did'),
            'foto_destaque' => $foto_nome,
            'texto' => $this->request->input('texto'),
        ]);

        return 'Artigo Enviado com sucesso, em breve um dos nossos administrador irar analisar.';
    }

    public function gerenciar()
    {
        $titulo = 'Gerenciar Matéria';
        $materias = $this->materias->listar_materia_redator_id($this->request->user()->id);

        return view('painel.redator.gerenciar.index', compact('titulo', 'materias'));
    }
}
