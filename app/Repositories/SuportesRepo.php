<?php

namespace App\Repositories;

use App\Suportes;

class SuportesRepo
{
    private $model;

    public function __construct(Suportes $model)
    {
        $this->model = $model;
    }

    public function abrir_suporte($data)
    {
        return $this->model->create($data);
    }

    public function listar_suporte_usuario($id)
    {
        return $this->model->where('uid', $id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function abertos()
    {
        return $this->model->orderBy('suportes.updated_at', 'desc')
            ->join('users', 'suportes.uid', '=', 'users.id')
            ->select('suportes.*', 'users.id as uid', 'users.name')
            ->get();
    }

    public function ver_id($id)
    {
        return $this->model->where('suportes.id', $id)
            ->join('users', 'suportes.uid', '=', 'users.id')
            ->select('suportes.*', 'users.id as uid', 'users.name')
            ->first();
    }

    public function atualizar_suporte($id, $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function excluir_suporte($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
