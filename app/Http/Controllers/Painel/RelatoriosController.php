<?php

namespace App\Http\Controllers\Painel;

use App\Helpers\Plataforma;
use App\Http\Controllers\Controller;
use App\Repositories\RelatoriosRepo;
use Illuminate\Http\Request;

class RelatoriosController extends Controller
{
    private $relatorio;
    private $request;
    private $plataforma;

    public function __construct(RelatoriosRepo $relatorio, Request $request, Plataforma $plataforma)
    {
        $this->relatorio = $relatorio;
        $this->request = $request;
        $this->plataforma = $plataforma;
    }

    public function index()
    {
        $titulo = 'Relatório de Visitas';
        $relatorio_pendente = $this->relatorio->relatorio_pendentes($this->request->user()->id);
        $relatorio_mes_atual_publisher = $this->relatorio->relatorio_mes_atual_publisher($this->request->user()->id);

        $relatorio_mes_atual_redator = $this->relatorio->relatorio_mes_atual_redator($this->request->user()->id);

        return view('painel.relatorios.index', compact('relatorio_pendente', 'relatorio_mes_atual_publisher', 'relatorio_mes_atual_redator', 'titulo'));
    }

    public function metricas()
    {
        $data_post = $this->request->input('data');
        $uid = $this->request->user()->id;

        $data = $this->plataforma->formatar_data($data_post);

        if ($data_post === date("d/m/Y")) {
            $relatorio_metricas = $this->relatorio->relatorio_pendentes($uid);
        } else {
            $relatorio_metricas = $this->relatorio->relatorio_metricas($uid, $data);
        }

        return view('painel.relatorios.tabela', compact('relatorio_metricas'));
    }
}
