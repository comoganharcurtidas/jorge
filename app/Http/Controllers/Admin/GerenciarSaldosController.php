<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SaldosRepo;
use App\Repositories\UsersRepo;
use Illuminate\Http\Request;
use Validator;

class GerenciarSaldosController extends Controller
{
    private $users;
    private $request;
    private $saldo;

    public function __construct(UsersRepo $users, Request $request, SaldosRepo $saldo)
    {
        $this->users = $users;
        $this->request = $request;
        $this->saldo = $saldo;
    }

    public function index()
    {
        $usuarios = $this->users->listar_usuarios_ativo();

        return view('admin.gerenciar_saldo.index', compact('usuarios'));
    }

    public function agendar()
    {
        $validator = Validator::make($this->request->all(), [
            'hora' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/gerenciar-saldo')
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'data' => $this->request->input('data'),
            'hora' => $this->request->input('hora'),
        ];

        $this->saldo->criar_agenda($data);

        return redirect()->back()->with('success_agenda', true);
    }

    public function atualizar()
    {
    }
}
