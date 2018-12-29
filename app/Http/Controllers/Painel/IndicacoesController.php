<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;

class IndicacoesController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $titulo = 'Indicações';

        return view('painel.indicacoes.index', compact('titulo'));
    }
}
