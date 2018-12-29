<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Repositories\ContasRepo;
use App\Repositories\PagamentosRepo;
use Illuminate\Http\Request;
use Validator;

class PagamentosController extends Controller
{
    private $request;
    private $conta;
    private $pagamento;

    public function __construct(Request $request, ContasRepo $conta, PagamentosRepo $pagamento)
    {
        $this->request = $request;
        $this->conta = $conta;
        $this->pagamento = $pagamento;
    }

    public function index()
    {
        $titulo = 'Meus Pagamento';
        $conta_cadastrada = $this->conta->listar_conta_id($this->request->user()->id);
        $pagamentos = $this->pagamento->pagamento_usuario($this->request->user()->id);

        return view('painel.pagamentos.index', compact('conta_cadastrada', 'pagamentos', 'titulo'));
    }

    public function cadastrar_conta()
    {
        $validator = Validator::make($this->request->all(), [
            'forma_pagamento' => 'required',
            'titular' => 'required',
            'tipo_pessoa' => 'required',
            'cpf_or_cnpj' => 'required',
            'banco_acc' => 'required',
            'ag' => 'required',
            'acc' => 'required',
            'tipo_conta' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $this->conta->cadastrar_conta([
            'uid' => $this->request->user()->id,
            'nome_conta' => $this->request->get('titular'),
            'tipo_pessoa' => $this->request->get('tipo_pessoa'),
            'cpf_cnpj' => $this->request->get('cpf_or_cnpj'),
            'banco' => $this->request->get('banco_acc'),
            'ag' => $this->request->get('ag'),
            'ag_digito' => $this->request->get('ag_digito'),
            'acc' => $this->request->get('acc'),
            'acc_digito' => $this->request->get('acc_digito'),
            'tipo_conta' => $this->request->get('tipo_conta'),
            'observacao' => $this->request->get('obs'),
        ]);
        return ['success' => true];
    }
}
