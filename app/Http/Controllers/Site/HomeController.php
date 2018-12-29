<?php

namespace App\Http\Controllers\Site;

use App\Helpers\Plataforma;
use App\Http\Controllers\Controller;
use App\Repositories\PreCadastroRepo;
use App\Repositories\UsersRepo;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class HomeController extends Controller
{
    private $request;
    private $response;
    private $cookie;
    private $cadastro;
    private $plataforma;
    private $usuario;

    public function __construct(Request $request, CookieJar $cookie, Response $response, PreCadastroRepo $cadastro, Plataforma $plataforma, UsersRepo $usuario)
    {
        $this->request = $request;
        $this->cookie = $cookie;
        $this->response = $response;
        $this->cadastro = $cadastro;
        $this->plataforma = $plataforma;
        $this->usuario = $usuario;
    }

    public function index()
    {
        return view('site.home.index');
    }

    public function pre_cadastrado()
    {
        $hash_indicado = $this->request->cookie('ref');

        $validator = Validator::make($this->request->all(), [
            'nome' => 'required',
            'email' => 'required',
            'cpf_or_cnpj' => 'required',
            'info' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (!empty($hash_indicado)) {
            $id_indicado = $this->plataforma->decode($hash_indicado);
            $usuario_q_indiciou = $this->usuario->usuario_id($id_indicado);

            $foi_inidicado = (!empty($usuario_q_indiciou)) ? $usuario_q_indiciou->id : null;
        }

        $email_existe = $this->cadastro_existe(($this->request->input('email')));

        if (!empty($email_existe)) {
            return ['email_existe' => $email_existe];
        }

        $doc_existe = $this->doc_existe(($this->request->input('cpf_or_cnpj')));

        if (!empty($doc_existe)) {
            return ['doc_existe' => $doc_existe];
        }

        $this->cadastro->criar_cadastro([
            'nome' => $this->request->input('nome'),
            'email' => $this->request->input('email'),
            'cpf_or_cnpj' => $this->request->input('cpf_or_cnpj'),
            'info' => $this->request->input('info'),
            'id_indicado' => (!empty($foi_inidicado)) ? $foi_inidicado : null,
        ]);

        return ['success' => true];
    }

    private function cadastro_existe($email)
    {
        $user_existe = $this->usuario->cadastro_email($email);
        if (!empty($user_existe)) {
            $status_user = $user_existe->status;
            if ($status_user === 'ativo') {
                return 'Já existe um usuário cadastrado com esse E-mail: ' . $email . '  é já foi aprovado.';
            } elseif ($status_user === 'bloqueado') {
                return 'Já existe um usuário cadastrado com esse E-mail: ' . $email . '  é foi bloqueado.';
            }
        }
        $cadastro_existe = $this->cadastro->cadastro_email($email);

        if (!empty($cadastro_existe)) {
            $status_cadastro = $cadastro_existe->status;

            if ($status_cadastro === 'ativo') {
                return 'Já existe um usuário cadastrado com esse E-mail: ' . $email . ' é já foi aprovado.';
            } elseif ($status_cadastro === 'pendente') {
                return 'Já existe um usuário cadastrado com esse E-mail: ' . $email . ' em analise.';
            } elseif ($status_cadastro === 'recusado') {
                return 'Já existe um usuário cadastrado com esse E-mail: ' . $email . ' é foi recusado.';
            }
        }

        return false;
    }

    private function doc_existe($cpf_or_cnpj)
    {
        $cpf_existe = $this->cadastro->cadastro_doc($cpf_or_cnpj);

        if (!empty($cpf_existe)) {
            $status_cadastro = $cpf_existe->status;

            if ($status_cadastro === 'ativo') {
                return 'Já existe um usuário cadastrado com esse CPF/CNPJ é já foi aprovado.';
            } elseif ($status_cadastro === 'pendente') {
                return 'Já existe um usuário cadastrado com esse CPF/CNPJ em analise.';
            } elseif ($status_cadastro === 'recusado') {
                return 'Já existe um usuário cadastrado com esse CPF/CNPJ é foi recusado.';
            }
        }

        return false;
    }
}
