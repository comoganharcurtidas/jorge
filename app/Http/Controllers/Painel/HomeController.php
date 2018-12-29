<?php

namespace App\Http\Controllers\Painel;

use App\Charts\SampleChart;
use App\Http\Controllers\Controller;
use App\Repositories\LogsVisitasRepo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $visitas;
    private $request;

    public function __construct(LogsVisitasRepo $visitas, Request $request)
    {
        $this->visitas = $visitas;
        $this->request = $request;
    }

    public function index()
    {
        $titulo = 'Dashboard';
        $chart = $this->grafico_materia();
        $top_dez = $this->top_dez_materia();
        $widget = $this->widget();

        return view('painel.dashboard.index', compact('chart', 'top_dez', 'widget', 'titulo'));
    }

    public function widget()
    {
        return (object) [
            'clicks_hoje' => 0,
            'ganhos_ontem' => 'R$ 0,00',
            'ganhos_ultima_semana' => 'R$ 0,00',
            'recebido' => 'R$ 0,00',
            'ultimo_pagamento' => 'R$ 0,00',
            'a_receber' => 'R$ 0,00',
            'inidicados' => 'R$ 0,00',
        ];
    }

    public function grafico_materia()
    {
        $grafico = $this->visitas->grafico_dois_dias($this->request->user()->id);

        $chart = new SampleChart;
        $chart->labels([
            '2 Dias atrás',
            'Ontem',
            'Hoje',
        ]);

        $chart->dataset('Visitas', 'line', [
            $grafico->ultimo_dois_dias,
            $grafico->ontem, $grafico->visitas_hoje,
        ]);

        return $chart;
    }

    public function top_dez_materia()
    {
        return $this->visitas->top_dez_materia_usuario_hoje($this->request->user()->id);
    }
}
