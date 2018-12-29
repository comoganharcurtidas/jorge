<?php
namespace app\Helpers;

use App\MeusLink;
use Mail;
use Vinkla\Hashids\Facades\Hashids;

class Plataforma
{
    public static function formatar_data($data_plataforma)
    {
        $data = explode('/', $data_plataforma);

        list($dia, $mes, $ano) = $data;
        return $ano . '-' . $mes . '-' . $dia;
    }

    public static function enviar_email($tema, $usuario, $data)
    {
        Mail::send(
            $tema,
            ['content' => $data, 'usuario' => $usuario->name],
            function ($message) use ($usuario, $data) {
                $message->from(config('mail.username'), config('app.name', 'Laravel'));
                $message->to($usuario->email);
                $message->subject(config('app.name', 'Laravel') . " {$data['assunto']}");
            }
        );
    }

    public static function usou_materia($id)
    {
        $usou = MeusLink::where('mid', $id)
            ->where('uid', \Auth::user()->id)
            ->first();

        $nao = '<span class="label label-danger text-center">não compartilhada</span>';
        $sim = '<span class="label label-success text-center">compartilhada</span>';

        return (!empty($usou)) ? $sim : $nao;
    }

    public static function encode($id)
    {
        return Hashids::encode($id);
    }

    public static function decode($id)
    {
        return Hashids::decode($id)[0];
    }

    public static function relatorio_total($data)
    {
        if (!empty($data)) {
            $total_visita = 0;
            $total_ganho = 0;

            foreach ($data as $relatorio) {
                $total_visita += $relatorio->total_visita;
                $total_ganho += $relatorio->total_ganho;
            }

            return (object) [
                'total_visita' => $total_visita,
                'total_ganho' => $total_ganho,
            ];
        }
    }
}
