<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EnvWriter;
use App\Http\Controllers\Controller;
use App\Repositories\CategoriasRepo;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    private $categorias;
    private $request;

    public function __construct(CategoriasRepo $categorias, Request $request)
    {
        $this->categorias = $categorias;
        $this->request = $request;
    }

    public function index()
    {
        $categorias = $this->categorias->todas_categorias();

        return view('admin.categorias.index', compact('categorias'));
    }

    public function atualizar_categoria($id, $status)
    {
        $this->categorias->atualizar_categoria($id, ['status' => $status]);
        return redirect()->back()->with('categoria_atualizada', true);
    }

    public function tipo_cpm($tipo)
    {
        $env = new EnvWriter();
        $env->writeNewEnvironmentFileWith('TIPO_CPM', $tipo);

        if (file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:clear");
        }

        return redirect()->back()->with('tipo_cpm_atualizado', true);
    }

    public function valor()
    {
        $id = $this->request->input('categoria');
        return $this->categorias->categoria_id($id);
    }

    public function alterar_valor()
    {
        $cat_id = $this->request->input('cat_id');
        $novo_cpm = $this->request->input('valor_cpm') / 1000;
        $this->categorias->criar_log_cpm(['cid' => $cat_id, 'valor_cpm' => $novo_cpm]);
        $this->categorias->atualizar_categoria($cat_id, ['valor_cpm' => $novo_cpm]);

        return redirect()->back()->with('cpm_categoria_atualizada', true);
    }

    public function fixo()
    {
        $valor = $this->request->input('valor_fixo') / 1000;

        $env = new EnvWriter();
        $env->writeNewEnvironmentFileWith('VALOR_CPM_FIXO', $valor);

        if (file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:clear");
        }

        return redirect()->back()->with('valor_cpm_fixo_sucesso', true);
    }
}
