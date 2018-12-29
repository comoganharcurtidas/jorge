<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Word;
use App\Http\Controllers\Controller;
use App\Repositories\DominiosRepo;
use Illuminate\Http\Request;
use Validator;

class DominiosController extends Controller
{
    private $dominio;
    private $request;
    private $word;
    public function __construct(DominiosRepo $dominio, Request $request, Word $word)
    {
        $this->dominio = $dominio;
        $this->request = $request;
        $this->word = $word;
    }

    public function index()
    {
        $lista_dominio = $this->dominio->listar_dominios();

        return view('admin.dominios.index', compact('lista_dominio'));
    }

    public function cadastrar_dominio()
    {
        $validator = Validator::make($this->request->all(), [
            'nome_blog' => 'required',
            'url_blog' => 'required',
            'slug_blog' => 'required',
            'tipo_blog' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('admin/dominios')
                ->withErrors($validator)
                ->withInput();
        }

        $this->dominio->cadastrar_dominio([
            'nome_blog' => $this->request->input('nome_blog'),
            'url_blog' => $this->request->input('url_blog'),
            'slug_blog' => $this->request->input('slug_blog'),
            'tipo_blog' => $this->request->input('tipo_blog'),
        ]);

        $tipo = ($this->request->input('tipo_blog') === 'qualquer') ? true : false;

        if (!$tipo) {
            $this->word_api($this->request->input('url_blog'));
        }

        return redirect()->back()->with('success', true);
    }

    private function word_api($url)
    {
        $this->word->dominio($url);
        $this->word->copiar_todos_post();
    }

    public function atualizar_dominio($id, $status)
    {
        $this->dominio->atualizar_dominio($id, ['status' => $status]);
        return redirect()->back()->with('success_up_dominio', true);
    }

    public function excluir_dominio($id)
    {
        $this->dominio->excluir_dominio($id);
        return redirect()->back()->with('success_excluir_dominio', true);
    }
}
