<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Repositories\UsersRepo;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    private $request;
    private $usuarios;

    public function __construct(Request $request, UsersRepo $usuarios)
    {
        $this->usuarios = $usuarios;
        $this->request = $request;
    }

    public function index()
    {
        $titulo = 'Meu Perfil';
        $usuario = $this->usuarios->usuario_id($this->request->user()->id);
        return view('painel.perfil.index', compact('usuario', 'titulo'));
    }

    public function alterar_perfil()
    {
        $data = [
            'name' => $this->request->input('nome'),
            'endereco' => $this->request->input('endereco'),
            'cidade' => $this->request->input('cidade'),
            'estado' => $this->request->input('estado'),
            'whatasapp' => $this->request->input('whatasapp'),
        ];

        $this->usuarios->atualizar_perfil($this->request->user()->id, $data);

        return redirect()->back()->with("success_perfil", "Perfil alterada com sucesso !");
    }

    public function alterar_senha()
    {
        if (!(\Hash::check($this->request->get('current-password'), \Auth::user()->password))) {
            return redirect()->back()->with("error", "Sua senha atual não corresponde à senha que você forneceu. Por favor, tente novamente.");
        }

        if (strcmp($this->request->get('current-password'), $this->request->get('new-password')) == 0) {
            return redirect()->back()->with("error", "A nova senha não pode ser igual à sua senha atual. Por favor, escolha uma senha diferente.");
        }

        $validatedData = $this->request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        $user = \Auth::user();
        $user->password = bcrypt($this->request->get('new-password'));
        $user->save();

        return redirect()->back()->with("success", "Senha alterada com sucesso !");
    }
}
