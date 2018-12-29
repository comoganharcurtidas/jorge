<?php

namespace App\Http\Controllers\Atualizadores;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriasRepo;
use App\Repositories\MateriasRepo;
use App\Repositories\MeusLinkRepo;
use App\Repositories\RelatoriosRepo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    private $request;
    private $link;
    private $relatorio;
    private $categoria;
    private $materias;

    public function __construct(Request $request, MeusLinkRepo $link, RelatoriosRepo $relatorio, CategoriasRepo $categoria, MateriasRepo $materias)
    {
        $this->middleware('auth');
        $this->request = $request;
        $this->link = $link;
        $this->relatorio = $relatorio;
        $this->categoria = $categoria;
        $this->materias = $materias;
    }

    public function index()
    {
        $data_hoje = today()->subDays(1);

        $links = $this->link->todos_link();

        $link_visita = [];

        foreach ($links as $link) {
            $visita = $this->relatorio->link_visita_pendente_data($link->uid, $link->mid, $data_hoje);
            $link_visita[] = $this->tipo_saldo($visita);
        }

        return $link_visita;
    }

    private function tipo_saldo($data)
    {
        if (!empty($data)) {
            $materia = $this->materias->find_materia($data->mid);
            $redator = $this->materias->find_materia_redator($materia->rid);

            if (!empty($redator)) {
                if ($redator->uid === $data->uid) {
                    return $this->gerar_saldo($data);
                } else {
                    return 'bonus publisher redator';
                }
            } else {
                return $this->gerar_saldo($data);
            }
        }
    }

    public function gerar_saldo($data)
    {
        $cpm_atual = $this->valor_cpm($data->mid);
        $ganhos_publisher = 0;
        $data_pub = [];
        if (!empty($data->visitas_publisher)) {
            $ganhos_publisher = (int) $data->visitas_publisher * $cpm_atual;
            $data_pub = [
                'ganhos_publisher' => $ganhos_publisher,
            ];
        }
        $data_red = [];
        $ganhos_redator_cpm = 0;

        if (!empty($data->visitas_redator)) {
            $ganhos_redator_cpm = (int) $data->visitas_redator * $this->cpm_redator($cpm_atual);

            $data_red = [
                'ganhos_redator' => $ganhos_redator_cpm,
            ];
        }

        $data_extra = [
            'ganhos_total' => $ganhos_publisher + $ganhos_redator_cpm, // por ganho de acordo.
            'status' => 'analisado',
        ];

        $data_saldo = array_merge($data_pub, $data_red, $data_extra);

        return $this->relatorio->atualizar_relatorio_pendente($data->id, $data_saldo);
    }

    public function valor_cpm($mid)
    {
        $fixo_valor = env('VALOR_CPM_FIXO', '');

        $categoria_valor = $this->categoria->valor_cpm($mid)->valor_cpm;

        $cpm = (env('TIPO_CPM', '') === 'fixo') ? $fixo_valor : $categoria_valor;

        return $fixo_valor;
    }

    public function cpm_redator($valor)
    {
        $por_cento_valor = 10; // 10%

        $por_cento = $por_cento_valor / 100;

        return $por_cento * $valor + $valor;
    }
}
