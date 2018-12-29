<?php

namespace App\Repositories;

use App\Pagamentos;

class PagamentosRepo
{
    private $model;

    public function __construct(Pagamentos $model)
    {
        $this->model = $model;
    }

    public function pagamento_usuario($id)
    {
        return $this->model->where('uid', $id)
            ->get();
    }
}
