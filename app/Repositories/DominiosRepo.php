<?php

namespace App\Repositories;

use App\Categorias;
use App\Dominios;
use App\Materias;

class DominiosRepo
{
    private $model;
    private $categorias;
    private $materias;

    public function __construct(Dominios $model, Categorias $categorias, Materias $materias)
    {
        $this->model = $model;
        $this->materias = $materias;
        $this->categorias = $categorias;
    }

    public function dominio_id($id)
    {
        return $this->model->find($id);
    }

    public function listar_dominios()
    {
        return $this->model->orderBy('id', 'desc')
            ->get();
    }

    public function listar_dominios_ativo()
    {
        return $this->model->orderBy('id', 'desc')
            ->get();
    }

    public function cadastrar_dominio($data)
    {
        return $this->model->create($data);
    }

    public function excluir_dominio($id)
    {
        $dominio = $this->model->find($id);
        $this->remover_tudo($dominio->url_blog);
        $dominio->delete();
    }

    public function atualizar_dominio($id, $data)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    public function remover_tudo($dominio)
    {
        $base = parse_url($dominio)['host'];
        $this->categorias->where('dominio_categoria', $base)->delete();
        $this->materias->where('dominio', $base)->delete();
    }
}
