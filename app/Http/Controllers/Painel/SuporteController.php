<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Repositories\SuportesRepo;
use Illuminate\Http\Request;
use Validator;

class SuporteController extends Controller
{
    private $request;
    private $suporte;

    public function __construct(Request $request, SuportesRepo $suporte)
    {
        $this->request = $request;
        $this->suporte = $suporte;
    }

    public function index()
    {
        $titulo = 'Suporte';
        $suportes = $this->suporte->listar_suporte_usuario($this->request->user()->id);

        return view('painel.suporte.index', compact('suportes', 'titulo'));
    }

    public function enviar()
    {
        $validator = Validator::make($this->request->all(), [
            'assunto' => 'required',
            'setor' => 'required',
            'mensagem' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('suporte')
                ->withErrors($validator)
                ->withInput();
        }

        $this->suporte->abrir_suporte([
            'uid' => $this->request->user()->id,
            'assunto' => $this->request->input('assunto'),
            'setor' => $this->request->input('setor'),
            'mensagem' => $this->request->input('mensagem'),
        ]);

        return redirect()->back()->with('success', true);
    }
}
