<?php

namespace App\Repositories;

use App\LogsVisitas;
use App\MeusLink;
use App\RelatoriosVisitas;
use Illuminate\Support\Facades\DB;

class RelatoriosRepo
{
    private $relatorio;
    private $meus_link;
    private $logs_visitas;

    public function __construct(RelatoriosVisitas $relatorio, MeusLink $meus_link, LogsVisitas $logs_visitas)
    {
        $this->relatorio = $relatorio;
        $this->meus_link = $meus_link;
        $this->logs_visitas = $logs_visitas;
    }

    public function atualizar_relatorio_pendente($id, $data)
    {
        return $this->relatorio->where('id', $id)->update($data);
    }

    public function relatorio_pendentes($uid)
    {
        return $this->relatorio->where('uid', $uid)
            ->where('status', 'pendente')
            ->where('periodo', date('m/Y'))
            ->whereDate('created_at', '=', date('Y-m-d') . ' 00:00:00')
            ->select('status', DB::raw('DATE(created_at) as data'), DB::raw('SUM(visitas_publisher) as total_visitas_publisher'), DB::raw('SUM(visitas_redator) as total_visitas_redator'))
            ->groupBy('data')
            ->groupBy('status')
            ->get();
    }

    public function relatorio_metricas($uid, $data)
    {
        return $this->relatorio->where('uid', $uid)
            ->where('periodo', date('m/Y'))
            ->whereDate('created_at', '=', $data . ' 00:00:00')
            ->select('status', DB::raw('DATE(created_at) as data', 'status'), DB::raw('SUM(visitas_publisher) as total_visitas_publisher'), DB::raw('SUM(visitas_redator) as total_visitas_redator'), DB::raw('SUM(ganhos_publisher) as ganhos_publisher'), DB::raw('SUM(ganhos_redator) as ganhos_redator'), DB::raw('SUM(ganhos_total) as ganhos_total'))
            ->groupBy('data')
            ->groupBy('status')
            ->get();
    }

    public function relatorio_mes_atual_publisher($uid)
    {
        return $this->relatorio->where('uid', $uid)
            ->where('periodo', date('m/Y'))
            ->whereNull('relatorios_visitas.visitas_redator')
            ->whereNull('relatorios_visitas.ganhos_redator')
            ->join('materias', 'relatorios_visitas.mid', '=', 'materias.id')
            ->selectRaw('materias.titulo as titulo, sum(relatorios_visitas.visitas_publisher) as total_visita, sum(relatorios_visitas.ganhos_total) as total_ganho, sum(relatorios_visitas.ganhos_publisher) as ganhos_publisher')
            ->groupBy('materias.titulo')
            ->orderBy('total_visita', 'desc')
            ->get();
    }

    public function relatorio_mes_atual_redator($uid)
    {
        return $this->relatorio->where('uid', $uid)
            ->where('periodo', date('m/Y'))
            ->whereNull('relatorios_visitas.visitas_publisher')
            ->whereNull('relatorios_visitas.ganhos_publisher')
            ->join('materias', 'relatorios_visitas.mid', '=', 'materias.id')
            ->selectRaw('materias.titulo as titulo, sum(relatorios_visitas.visitas_redator) as total_visita, sum(relatorios_visitas.ganhos_total) as total_ganho, sum(relatorios_visitas.ganhos_redator) as ganhos_redator')
            ->groupBy('materias.titulo')
            ->orderBy('total_visita', 'desc')
            ->get();
    }

    public function criar_relatorios_pendente($data)
    {
        $atualizado = $this->relatorio->where('uid', $data['uid'])
            ->where('mid', $data['mid'])
            ->whereDate('created_at', '=', date('Y-m-d') . ' 00:00:00')
            ->first();

        if (!empty($atualizado)) {
            return $this->relatorio->where('id', $atualizado->id)
                ->update($data);
        }

        return $this->relatorio->create($data);
    }

    public function criar_relatorios_pendente_redator($data)
    {
        $atualizado = $this->relatorio->where('uid', $data['uid'])
            ->where('mid', $data['mid'])
            ->whereDate('created_at', '=', date('Y-m-d') . ' 00:00:00')
            ->first();

        if (!empty($atualizado)) {
            return $this->relatorio->where('id', $atualizado->id)
                ->update($data);
        }

        return $this->relatorio->create($data);
    }

    public function meus_link($uid)
    {
        return $this->meus_link->where('uid', $uid);
    }

    public function link_visita_pendente_data($uid, $mid, $data)
    {
        $link = $this->relatorio->where('uid', $uid)
            ->where('status', 'pendente')
            ->whereDate('created_at', $data)
            ->where('mid', $mid)
            ->first();

        return (!empty($link)) ? $link : false;
    }

    public function link_visita_pendente($uid, $mid)
    {
        $link = $this->relatorio->where('uid', $uid)
            ->where('status', 'pendente')
            ->where('mid', $mid)
            ->first();

        return (!empty($link)) ? $link : false;
    }
}
