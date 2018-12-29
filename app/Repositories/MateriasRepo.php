<?php

namespace App\Repositories;

use App\Categorias;
use App\Materias;
use App\MateriasRedatores;
use App\MeusLink;

class MateriasRepo
{
    private $model;
    private $categorias;
    private $link;
    private $materias_redator;

    public function __construct(Materias $model, Categorias $categorias, MeusLink $link, MateriasRedatores $materias_redator)
    {
        $this->model = $model;
        $this->categorias = $categorias;
        $this->link = $link;
        $this->materias_redator = $materias_redator;
    }

    public function gravar_materia($data)
    {
        return $this->model->create($data);
    }

    public function listar_materia_redator_id($id)
    {
        return $this->materias_redator->where('uid', $id)
            ->join('categorias', 'materias_redatores.cid', '=', 'categorias.id')
            ->select('materias_redatores.*', 'categorias.dominio_categoria')
            ->orderBy('status', 'pendente')
            ->get();
    }

    public function listar_materia_redator_pendente()
    {
        return $this->materias_redator->join('categorias', 'materias_redatores.cid', '=', 'categorias.id')
            ->select('materias_redatores.*', 'categorias.dominio_categoria')
            ->get();
    }

    public function total_pendente_redatores_materia()
    {
        return $this->materias_redator->where('materias_redatores.status', 'pendente')
            ->join('categorias', 'materias_redatores.cid', '=', 'categorias.id')
            ->select('materias_redatores.*', 'categorias.dominio_categoria')
            ->count();
    }

    public function ver_materia_redator($id)
    {
        return $this->materias_redator->where('materias_redatores.id', $id)
            ->join('categorias', 'materias_redatores.cid', '=', 'categorias.id')
            ->join('users', 'materias_redatores.uid', '=', 'users.id')
            ->select('materias_redatores.*', 'categorias.dominio_categoria', 'users.name')
            ->first();
    }

    public function find_materia($id)
    {
        return $this->model->find($id);
    }

    public function find_materia_redator($id)
    {
        return $this->materias_redator->find($id);
    }

    public function criar_materia_redator($data)
    {
        return $this->materias_redator->create($data);
    }

    public function atualizar_materia_redator($id, $data)
    {
        return $this->materias_redator->where('id', $id)->update($data);
    }

    public function listar_materias()
    {
        return $this->model->where('status', 'aprovado')
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function excluir_materia($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function atualizar_materia($id, $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function listar_todas_materias()
    {
        return $this->model->orderBy('id', 'desc')
            ->get();
    }

    public function aprovar_pendentes()
    {
        return $this->model->where('status', 'pendente')
            ->where('importada', 'sim')
            ->update(['status' => 'aprovado']);
    }

    public function pesquisar($categoria = false, $site = false, $termo = false)
    {
        $materia = $this->model->newQuery();

        if (!empty($categoria)) {
            $post = $this->categorias->where('nome_categoria', $categoria)->first();
            $materia->where('post_cat', $post->id);
        }

        if (!empty($site)) {
            $materia->where('dominio', parse_url($site)['host']);
        }

        if (!empty($termo)) {
            $materia->where('titulo', 'LIKE', "%{$termo}%");
        }

        return $materia->where('status', 'aprovado')
            ->orderBy('id', 'desc')
            ->get();
    }
}
