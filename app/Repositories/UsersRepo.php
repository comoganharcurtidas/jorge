<?php

namespace App\Repositories;

use App\User;

class UsersRepo
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function cadastro_email($email)
    {
        return $this->model->where('email', $email)
            ->first();
    }

    public function cadastrar($data)
    {
        return $this->model->create($data);
    }

    public function listar_usuarios()
    {
        return $this->model->get();
    }

    public function listar_usuarios_ativo()
    {
        return $this->model->where('status', 'ativo')
            ->get();
    }

    public function usuario_id($id)
    {
        return $this->model->find($id);
    }

    public function atualizar_perfil($id, $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function usuario_find($id)
    {
        return $this->model->find($id);
    }

    public function total()
    {
        return $this->model->count();
    }
}
