<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Word;
use App\Http\Controllers\Controller;
use App\Repositories\CategoriasRepo;
use App\Repositories\DominiosRepo;
use App\Repositories\MateriasRepo;
use Illuminate\Http\Request;

class MateriasController extends Controller
{
    private $materias;
    private $categorias;
    private $request;
    private $word;
    private $dominio;

    public function __construct(MateriasRepo $materias, CategoriasRepo $categorias, Request $request, Word $word, DominiosRepo $dominio)
    {
        $this->materias = $materias;
        $this->categorias = $categorias;
        $this->request = $request;
        $this->word = $word;
        $this->dominio = $dominio;
    }

    public function index()
    {
        $materias = $this->materias->listar_todas_materias();
        $total_materia_redator_pendente = $this->materias->total_pendente_redatores_materia();

        return view('admin.materias.index', compact('materias', 'total_materia_redator_pendente'));
    }

    public function redatores()
    {
        $materias = $this->materias->listar_materia_redator_pendente();

        return view('admin.materias.redator', compact('materias'));
    }

    public function materia_redator($id)
    {
        $materia = $this->materias->ver_materia_redator($id);
        if (empty($materia)) {
            return redirect('/admin/materias/redatores');
        }
        $categorias = $this->categorias->categoria_dominio_url($materia->dominio_categoria);

        return view('admin.materias.ver', compact('materia', 'categorias'));
    }

    public function editar_materia_redator()
    {
        $id = $this->request->get('materia_id');
        $materia = $this->materias->find_materia_redator($id);

        if ($this->request->hasFile('img')) {
            $this->validate($this->request, [
                'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $image = $this->request->file('img');
            $foto_nome = time() . '.' . $image->getClientOriginalExtension();
            #editar imagem
            $materia->foto_destaque = $foto_nome;
            $materia->save();
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images_capa';
            $image->move($destinationPath, $foto_nome);
        }

        $data = [
            'titulo' => $this->request->get('titulo'),
            'cid' => $this->request->get('categoria'),
            'texto' => $this->request->get('texto'),
            'revisado' => 'sim',
            'status' => $this->request->get('status'),
        ];

        $this->materias->atualizar_materia_redator($id, $data);

        if ($materia->status === 'aprovado') {
            return $this->enviar_materia_pro_blog($id);
        }

        return 'Matéria editada com Sucesso!';
    }

    public function enviar_materia_pro_blog($id)
    {
        $materia = $this->materias->ver_materia_redator($id);

        $dominio_do_redator = $this->dominio->dominio_id($materia->did)->url_blog;

        $this->word->dominio($dominio_do_redator);

        $arquivo = public_path() . DIRECTORY_SEPARATOR . 'images_capa' . DIRECTORY_SEPARATOR . $materia->foto_destaque;

        $image = $this->word->carregar_imagem($arquivo);

        $categoria = $this->categorias->categoria_id($materia->cid);

        $post = [
            'title' => $materia->titulo,
            'content' => $materia->texto,
            'featured_media' => $image->id,
            'categories' => $categoria->id_categoria,
            'status' => 'publish',
            'type' => 'post',
        ];

        if (!empty($materia->mid)) {
            $editar = $this->word->editar_post($materia->post_id, $post);
            $this->materias->atualizar_materia($materia->mid, $editar);
            return 'Matéria editada com sucesso!';
        }

        $gravar = $this->word->criar_post($post);

        $data_materia = array_merge_recursive($this->word->importar_post($gravar->id), ['status' => 'aprovado']);

        $importa = $this->materias->gravar_materia($data_materia);

        $materia->mid = $importa->id;
        $materia->save();

        $importa->rid = $materia->id;
        $importa->save();

        return 'Matéria Aprovada com sucesso!';
    }

    public function aprovar_pendentes()
    {
        $this->materias->aprovar_pendentes();
        return redirect()->back()->with('aprovado_sucesso', true);
    }

    public function excluir_materia($id)
    {
        $this->materias->excluir_materia($id);
        return redirect()->back()->with('materia_excluida', true);
    }

    public function atualizar_materia($id, $status)
    {
        $this->materias->atualizar_materia($id, ['status' => $status]);
        return redirect()->back()->with('materia_atualizada', true);
    }
}
