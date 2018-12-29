<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\MateriasRepo;
use App\Repositories\PreCadastroRepo;
use App\Repositories\UsersRepo;

class HomeController extends Controller
{
    private $cadastro;
    private $materias;
    private $usuarios;

    public function __construct(PreCadastroRepo $cadastro, MateriasRepo $materias, UsersRepo $usuarios)
    {
        $this->cadastro = $cadastro;
        $this->materias = $materias;
        $this->usuarios = $usuarios;
    }

    public function index()
    {
        $total = $this->total();

        return view('admin.dashboard.index', compact('total'));
    }

    public function total()
    {
        return (object) [
            'cadastro_pendentes' => $this->cadastro->total(),
            'materia_pendentes' => $this->materias->total_pendente_redatores_materia(),
            'total_usuarios' => $this->usuarios->total(),
        ];
    }
}
