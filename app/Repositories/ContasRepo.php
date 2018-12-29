<?php

namespace App\Repositories;

use App\ContasBancaria;

class ContasRepo
{
    private $model;

    public function __construct(ContasBancaria $model)
    {
        $this->model = $model;
    }

    public function cadastrar_conta($data)
    {
        $existe = $this->model->where('uid', $data['uid'])
            ->first();

        if (!empty($existe)) {
            return $this->model->where('id', $existe->id)
                ->update($data);
        } else {
            return $this->model->create($data);
        }
    }

    public function listar_conta_id($uid)
    {
        return $this->model->where('uid', $uid)->first();
    }
}
