<?php

namespace App\Repositories;

use App\CadastroPendentes;

class PreCadastroRepo
{
    private $model;

    public function __construct(CadastroPendentes $model)
    {
        $this->model = $model;
    }

    public function criar_cadastro($data)
    {
        return $this->model->create($data);
    }

    public function atualizar($id, $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function cadastro_id($id)
    {
        return $this->model->find($id);
    }

    public function total()
    {
        return $this->model->where('status', 'pendente')
            ->count();
    }

    public function pendentes()
    {
        return $this->model->where('status', 'pendente')
            ->get();
    }

    public function cadastro_doc($doc)
    {
        return $this->model->where('cpf_or_cnpj', $doc)
            ->first();
    }

    public function cadastro_email($email)
    {
        return $this->model->where('email', $email)
            ->first();
    }
}
