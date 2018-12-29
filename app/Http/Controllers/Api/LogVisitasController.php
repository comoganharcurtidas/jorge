<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\LogsVisitasRepo;
use App\Repositories\MateriasRepo;
use App\Repositories\RelatoriosRepo;

class LogVisitasController extends Controller
{
    private $logs;
    private $relatorios;
    private $materias;

    public function __construct(LogsVisitasRepo $logs, RelatoriosRepo $relatorios, MateriasRepo $materias)
    {
        $this->logs = $logs;
        $this->relatorios = $relatorios;
        $this->materias = $materias;
    }

    public function index($id = false)
    {
        if (empty($id)) {
            return ['status' => false];
        }

        for ($i = 0; $i < 15; $i++) {
            $gravar_log = $this->criar_log(($id));

            if (!empty($gravar_log)) {
                $relatorio_res = $this->gerar_relatorio($id);
            }

            if (!empty($gravar_log) && !empty($relatorio_res)) {
                #return 'Visita gravada com sucesso.';
            }
        }
    }

    private function criar_log($id)
    {
        $link_existe = $this->logs->link_id($id);

        if (empty($link_existe)) {
            return ['status' => false];
        }

        $log_visita_hoje = $this->logs->buscar_visita_data_visitas(date('d/m'), (int) $id);

        if (!empty($log_visita_hoje)) {
            $this->logs->atualizar_log_hoje([
                'id_log' => $log_visita_hoje->id,
                'total_visitas' => $log_visita_hoje->total_visitas + 1,
            ]);
            return ['status' => true];
        }
        $this->logs->criar_log_hoje([
            'link_id' => (int) $id,
            'total_visitas' => 1,
            'data_visitas' => date('d/m'),
            'periodo' => date('m/Y'),
        ]);

        return ['status' => true];
    }

    private function gerar_relatorio($id)
    {
        $log = $this->logs->buscar_visita_data_visitas(date('d/m'), (int) $id);
        $link = $this->logs->link_id($id);

        $materia = $this->materias->find_materia($link->mid);
        $redator = $this->materias->find_materia_redator($materia->rid);

        if (!empty($redator)) {
            if ($redator->uid === $link->uid) {
                return $this->relatorios->criar_relatorios_pendente([
                    'uid' => $link->uid,
                    'mid' => $link->mid,
                    'visitas_redator' => (int) $log->total_visitas,
                    'ganhos_redator' => 0,
                    'ganhos_total' => 0,
                    'periodo' => $log->periodo,
                ]);
            } else {
                $this->relatorios->criar_relatorios_pendente_redator([
                    'visitas_redator_do_publisher' => (int) $log->total_visitas,
                    'ganhos_redator_do_publisher' => 0,
                    'mid' => $link->mid,
                    'uid' => $redator->uid,
                    'periodo' => $log->periodo,
                ]);

                return $this->relatorios->criar_relatorios_pendente([
                    'uid' => $link->uid,
                    'mid' => $link->mid,
                    'visitas_publisher' => (int) $log->total_visitas,
                    'ganhos_publisher' => 0,
                    'ganhos_total' => 0,
                    'periodo' => $log->periodo,
                ]);
            }
        } else {
            return $this->relatorios->criar_relatorios_pendente([
                'uid' => $link->uid,
                'mid' => $link->mid,
                'visitas_publisher' => (int) $log->total_visitas,
                'ganhos_publisher' => 0,
                'ganhos_total' => 0,
                'periodo' => $log->periodo,
            ]);
        }
    }
}
