<?php

namespace App\Repositories;

use App\Materias;
use App\MeusLink;

class MeusLinkRepo
{
    private $model;
    private $materias;

    public function __construct(MeusLink $model, Materias $materias)
    {
        $this->model = $model;
        $this->materias = $materias;
    }

    public function todos_link()
    {
        return $this->model->get();
    }

    public function meus_link($id)
    {
        return $this->model->where('uid', $id)
            ->where('status', 'aprovado')
            ->join('materias', 'meus_links.mid', '=', 'materias.id')
            ->select('materias.titulo', 'materias.imagem', 'materias.post_url', 'materias.post_cat', 'materias.id as artigo_id', 'materias.post_id', 'materias.dominio', 'materias.status', 'materias.created_at as post_data', 'meus_links.*')
            ->orderBy('meus_links.id', 'desc')
            ->get();
    }

    public function usar_link($uid, $mid)
    {
        return $this->model->updateOrCreate([
            'uid' => $uid,
            'mid' => $mid,
        ]);
    }

    public function link_materia($id)
    {
        return $this->materias->find($id);
    }
}
