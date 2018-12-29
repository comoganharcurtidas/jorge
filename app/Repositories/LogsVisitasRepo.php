<?php

namespace App\Repositories;

use App\LogsVisitas;
use App\MeusLink;

class LogsVisitasRepo
{
    private $model;
    private $link;

    public function __construct(LogsVisitas $model, MeusLink $link)
    {
        $this->model = $model;
        $this->link = $link;
    }

    public function buscar_visita_data_visitas($data_visitas, $link_id)
    {
        return $this->model->where('link_id', $link_id)
            ->where('data_visitas', $data_visitas)
            ->whereDate('created_at', '=', date('Y-m-d') . ' 00:00:00')
            ->first();
    }

    public function criar_log_hoje($data)
    {
        return $this->model->create($data);
    }

    public function atualizar_log_hoje($data)
    {
        return $this->model->where('id', $data['id_log'])
            ->update([
                'total_visitas' => $data['total_visitas'],
            ]);
    }

    public function link_id($id)
    {
        return $this->link->where('id', $id)
            ->first();
    }

    public function top_dez_materia_usuario_hoje($id)
    {
        return $this->model
            ->join('meus_links', 'logs_visitas.link_id', '=', 'meus_links.id')
            ->join('materias', 'meus_links.mid', '=', 'materias.id')
            ->where('uid', $id)
            ->whereDate('logs_visitas.updated_at', today())
            ->orderBy('logs_visitas.total_visitas', 'asc')
            ->take(10)
            ->get();
    }

    public function grafico_dois_dias($id)
    {
        $visitas_hoje = $this->model
            ->join('meus_links', 'logs_visitas.link_id', '=', 'meus_links.id')
            ->where('uid', $id)
            ->whereDate('logs_visitas.created_at', today())
            ->sum('total_visitas');

        $ontem = $this->model
            ->join('meus_links', 'logs_visitas.link_id', '=', 'meus_links.id')
            ->where('uid', $id)
            ->whereDate('logs_visitas.created_at', today()->subDays(1))
            ->sum('total_visitas');

        $ultimo_dois_dias = $this->model
            ->join('meus_links', 'logs_visitas.link_id', '=', 'meus_links.id')
            ->where('uid', $id)
            ->whereDate('logs_visitas.created_at', today()->subDays(2))
            ->sum('total_visitas');

        return (object) [
            'ontem' => $ontem,
            'visitas_hoje' => $visitas_hoje,
            'ultimo_dois_dias' => $ultimo_dois_dias,
        ];
    }
}
