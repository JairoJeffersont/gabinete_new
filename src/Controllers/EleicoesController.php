<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Utils;

class EleicoesController {

    private $utils;
    private $dadosJson;

    public function __construct($ano, $estado) {
        $this->utils = new Utils();
        $this->dadosJson = json_decode(file_get_contents('public/resultados/' . $ano . '/votacao_candidato_munzona_' . $ano . '_' . $estado . '.json'), true);
    }


    public function getTotalVotos($candidato) {
        $busca = $this->dadosJson;

        $resultados = [];
        $total_votos = 0;
        $cargo = null;
        $candidato = mb_strtoupper(trim($candidato));

        foreach ($busca as $votos) {
            if ($votos['NM_URNA_CANDIDATO'] === $candidato) {
                $resultados[] = $votos;
                $cargo = $votos['DS_CARGO'];
            }
        }

        if ($cargo) {
            foreach ($busca as $votos) {
                if ($votos['DS_CARGO'] === $cargo) {
                    $total_votos += isset($votos['QT_VOTOS_NOMINAIS_VALIDOS']) ? $votos['QT_VOTOS_NOMINAIS_VALIDOS'] : $votos['QT_VOTOS_NOMINAIS'];
                }
            }
        }

        return ['dados' => $resultados, 'total_votos' => $total_votos];
    }
}
