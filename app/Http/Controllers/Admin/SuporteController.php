<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SuportesRepo;
use Illuminate\Http\Request;

class SuporteController extends Controller
{
    private $suporte;
    private $request;

    public function __construct(SuportesRepo $suporte, Request $request)
    {
        $this->suporte = $suporte;
        $this->request = $request;
    }

    public function index()
    {
        $suportes = $this->suporte->abertos();
        return view('admin.suporte.index', compact('suportes'));
    }

    public function ver($id)
    {
        $suporte = $this->suporte->ver_id($id);

        if (empty($suporte)) {
            return redirect('/admin/suporte');
        }

        return view('admin.suporte.ver', compact('suporte'));
    }

    public function responder()
    {
        $id = $this->request->get('id');
        $resposta = $this->request->get('resposta');
        $status = $this->request->get('acao');

        if (!empty($status) && $status === 'excluir') {
            $this->suporte->excluir_suporte($id);
            return redirect('/admin/suporte')->with('excluido', true);
        }

        $this->suporte->atualizar_suporte($id, ['resposta' => $resposta, 'status' => $status]);
        return redirect()->back()->with('respondindo', true);
    }
}
