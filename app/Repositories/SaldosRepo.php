<?php

namespace App\Repositories;

use App\AgendaAtualizacoes;

class SaldosRepo
{
    private $agenda;

    public function __construct(AgendaAtualizacoes $agenda)
    {
        $this->agenda = $agenda;
    }

    public function criar_agenda($data)
    {
        $this->agenda->truncate();
        return $this->agenda->create($data);
    }

    public function agendamento_criado()
    {
        return $this->agenda->first();
    }

}
