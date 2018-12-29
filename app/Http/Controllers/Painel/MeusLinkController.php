<?php

namespace App\Http\Controllers\Painel;

use App\Helpers\Bitly;
use App\Helpers\Plataforma;
use App\Http\Controllers\Controller;
use App\Repositories\MeusLinkRepo;
use Illuminate\Http\Request;

class MeusLinkController extends Controller
{
    private $link;
    private $request;
    private $bit;
    private $plataforma;

    public function __construct(MeusLinkRepo $link, Request $request, Bitly $bit, Plataforma $plataforma)
    {
        $this->link = $link;
        $this->request = $request;
        $this->bit = $bit;
        $this->plataforma = $plataforma;
    }

    public function index()
    {
        $titulo = 'Meus Link';
        $meus_links = $this->link->meus_link($this->request->user()->id);

        return view('painel.meus_links.index', compact('meus_links', 'titulo'));
    }

    public function gerar()
    {
        $link = $this->link->usar_link($this->request->user()->id, $this->request->get('url'));

        $materia = $this->link->link_materia($link->mid);

        $usuario_hash = $this->plataforma->encode($this->request->user()->id);

        $materia_hash = $this->plataforma->encode($materia->post_id);

        $query_url = $materia->post_url . "?utm_source=fb_parceiro&utm_medium=cpm&utm_campaign={$usuario_hash}&slot={$materia_hash}";

        return [
            'short' => $this->bit->encurtar($query_url),
            'raw' => $query_url,
        ];
    }
}
