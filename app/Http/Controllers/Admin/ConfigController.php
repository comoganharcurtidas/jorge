<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EnvWriter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ConfigController extends Controller
{
    private $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        return view('admin.configuracoes.index');
    }

    public function email_smtp()
    {
        $validator = Validator::make($this->request->all(), [
            'usuario_smtp' => 'required',
            'senha_smtp' => 'required',
            'host_smtp' => 'required',
            'porta' => 'required',
            'contato_email' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $erro = '';
            foreach ($messages->all(':message') as $message) {
                $erro .= "<li>$message</li>";
            }

            return redirect('admin/configuracoes')->with('email_erro', $erro);
        }

        $env = new EnvWriter();

        $env->writeNewEnvironmentFileWith('MAIL_HOST', $this->request->get('host_smtp'));
        $env->writeNewEnvironmentFileWith('MAIL_PORT', intval($this->request->get('porta')));
        $env->writeNewEnvironmentFileWith('MAIL_USERNAME', $this->request->get('usuario_smtp'));
        $env->writeNewEnvironmentFileWith('MAIL_PASSWORD', $this->request->get('senha_smtp'));
        $env->writeNewEnvironmentFileWith('MAIL_CONTATO', $this->request->get('contato_email'));

        if (file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:clear");
        }
        return redirect('admin/configuracoes')->with('email', 'Configuração de email atualizado com sucesso!');

    }

    public function plataforma()
    {
        $validator = Validator::make($this->request->all(), [
            'nome_plataforma' => 'required',
            'blog_principal' => 'required',
            'chave_bit' => 'required',
            'facebook' => 'required',
            'instagram' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $erro = '';
            foreach ($messages->all(':message') as $message) {
                $erro .= "<li>$message</li>";
            }

            return redirect('admin/configuracoes')->with('plataforma_erro', $erro);
        }

        $env = new EnvWriter();
        $nome = '"' . $this->request->get('nome_plataforma') . '"';

        $env->writeNewEnvironmentFileWith('APP_NAME', $nome);
        $env->writeNewEnvironmentFileWith('PLATAFORMA_BLOG', $this->request->get('blog_principal'));
        $env->writeNewEnvironmentFileWith('TOKEN_BIT_LY', $this->request->get('chave_bit'));
        $env->writeNewEnvironmentFileWith('PLATAFORMA_FACEBOOK', $this->request->get('facebook'));
        $env->writeNewEnvironmentFileWith('PLATAFORMA_INSTAGRAM', $this->request->get('instagram'));

        if (file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:clear");
        }

        return redirect('admin/configuracoes')->with('plataforma', 'Configuração da plataforma atualizado com sucesso!');
    }

    public function pagamento()
    {
        $validator = Validator::make($this->request->all(), [
            'redator_cpm' => 'required',
            'redator_publisher' => 'required',
            'indicados_bonus' => 'required',
            'ciclo' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $erro = '';
            foreach ($messages->all(':message') as $message) {
                $erro .= "<li>$message</li>";
            }

            return redirect('admin/configuracoes')->with('pagamento_erro', $erro);
        }

        $env = new EnvWriter();

        $env->writeNewEnvironmentFileWith('REDATOR_CPM', $this->request->get('redator_cpm'));
        $env->writeNewEnvironmentFileWith('REDATOR_PUBLISHER', $this->request->get('redator_publisher'));
        $env->writeNewEnvironmentFileWith('INDICADOS_BONUS', $this->request->get('indicados_bonus'));
        $env->writeNewEnvironmentFileWith('CICLO_PAGAMENTO', $this->request->get('ciclo'));

        if (file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:clear");
        }

        return redirect('admin/configuracoes')->with('pagamento', 'Configuração de pagamento atualizado com sucesso!');
    }
}
