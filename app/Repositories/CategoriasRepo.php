<?php

namespace App\Repositories;

use App\Categorias;
use App\LogCpms;
use App\Materias;

class CategoriasRepo
{
    private $model;
    private $materias;
    private $log_categoria;

    public function __construct(Categorias $model, Materias $materias, LogCpms $log_categoria)
    {
        $this->model = $model;
        $this->materias = $materias;
        $this->log_categoria = $log_categoria;
    }

    public function criar_log_cpm($data)
    {
        return $this->log_categoria->create($data);
    }

    public function valor_cpm($id)
    {
        $materia_cat = $this->materias->where('id', $id)
            ->first();

        return $this->model->where('id', $materia_cat->post_cat)
            ->first();
    }

    public function listar_categorias()
    {
        return $this->model->where('status', 'ativo')
            ->orderBy('nome_categoria', 'asc')
            ->get();
    }

    public function categoria_id($id)
    {
        return $this->model->where('id', $id)
            ->where('status', 'ativo')
            ->first();
    }

    public function categoria_dominio($dominio)
    {
        return $this->model->where('status', 'ativo')
            ->where('dominio_categoria', parse_url($dominio)['host'])
            ->orderBy('nome_categoria', 'asc')
            ->get();
    }

    public function categoria_dominio_url($url)
    {
        return $this->model->where('status', 'ativo')
            ->where('dominio_categoria', $url)
            ->orderBy('nome_categoria', 'asc')
            ->get();
    }

    public function todas_categorias()
    {
        return $this->model->orderBy('nome_categoria', 'asc')
            ->get();
    }

    public function atualizar_categoria($id, $data)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }
}
