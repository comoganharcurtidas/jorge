<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Plataforma;
use App\Http\Controllers\Controller;
use App\Repositories\ContasRepo;
use App\Repositories\PreCadastroRepo;
use App\Repositories\UsersRepo;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    private $usuarios;
    private $contas;
    private $request;
    private $cadastro;
    private $plataforma;

    public function __construct(UsersRepo $users, ContasRepo $contas, Request $request, PreCadastroRepo $cadastro, Plataforma $plataforma)
    {
        $this->usuarios = $users;
        $this->contas = $contas;
        $this->request = $request;
        $this->cadastro = $cadastro;
        $this->plataforma = $plataforma;
    }

    public function index()
    {
        $usuarios = $this->usuarios->listar_usuarios();
        $total = $this->total_usuario_filtro();

        return view('admin.usuarios.index', compact('usuarios', 'total'));
    }

    public function pendentes()
    {
        $cadastros = $this->cadastro->pendentes();

        return view('admin.usuarios.pendentes', compact('cadastros'));
    }

    public function total_usuario_filtro()
    {
        return (object) [
            'pendentes' => $this->cadastro->total(),
        ];
    }

    public function perfil($id)
    {
        $usuario = $this->usuarios->usuario_id($id);
        $conta = $this->contas->listar_conta_id($id);

        if (empty($usuario)) {
            return redirect('/admin/usuarios');
        }

        return view('admin.usuarios.perfil', compact('usuario', 'conta'));
    }

    public function atualizar_status_cadastro($id, $status)
    {
        if ($status === 'aceitar') {
            $this->cadastrar_usuario($id);
            return redirect()->back()->with('aceito', true);
        }

        $this->plataforma->enviar_email('emails.conta_recusado', $this->cadastro->cadastro_id($id), ['assunto' => 'Cadastro Recusado']);

        $this->cadastro->atualizar($id, ['status' => $status]);
        return redirect()->back()->with('recusado', true);
    }

    private function cadastrar_usuario($id)
    {
        $conta = $this->cadastro->cadastro_id($id);
        $senha = $this->senha_gerada($conta->email);

        $cadastrar = $this->usuarios->cadastrar([
            'name' => $conta->nome,
            'email' => $conta->email,
            'password' => Hash::make($senha),
        ]);

        $data = [
            'assunto' => 'Cadastro Aprovado',
            'usuario' => $cadastrar['email'],
            'senha' => $senha,
        ];

        $this->plataforma->enviar_email('emails.conta_aprovada', $cadastrar, $data);

        return $this->cadastro->atualizar($id, ['status' => 'ativo']);
    }

    private function senha_gerada($email, $length = 4)
    {
        $characters = md5($email);
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function atualizar_perfil()
    {
        $id = $this->request->input('id');
        $data = [
            'name' => $this->request->input('nome'),
            'redator' => $this->request->input('redator'),
            'admin' => $this->request->input('admin'),
            'endereco' => $this->request->input('endereco'),
            'cidade' => $this->request->input('cidade'),
            'estado' => $this->request->input('estado'),
            'whatasapp' => $this->request->input('whatasapp'),
        ];

        $this->usuarios->atualizar_perfil($id, $data);

        return redirect()->back()->with('perfil_atualizado', true);
    }

    public function status_usuario($id, $status)
    {
        $this->usuarios->atualizar_perfil($id, ['status' => $status]);

        if ($status === 'bloqueado') {
            return redirect()->back()->with('status_bloqueado', true);
        }

        return redirect()->back()->with('status_ativo', true);
    }

    public function logar_na_conta($id)
    {
        $user = $this->usuarios->usuario_find($id);
        Auth::login($user);
        return redirect('home');
    }
}
