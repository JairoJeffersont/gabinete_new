<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Utils;

class EleicoesController {

    private $utils;
    private $dadosJson;

    public function __construct($ano, $estado) {
        $this->utils = new Utils();
        $this->dadosJson = json_decode(file_get_contents('public/resultados/' . $ano . '/votacao_nominal/votos_' . $ano . '_' . $estado . '.json'), true);
    }


    public function getTotalVotos($candidato){
        
        $busca = $this->dadosJson;

        $resultados = [];

        foreach($busca as $votos){
            if($votos['NM_URNA_CANDIDATO'] == mb_strtoupper($candidato)){
                $resultados[] = $votos;
            }
        }

        return $resultados;

    }


}
