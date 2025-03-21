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


    public function getCargos() {
        $cargos = [];
        foreach ($this->dadosJson as $resultado) {
            $cargos[] = $resultado['DS_CARGO'];
        }
        return array_unique($cargos);
    }


    public function getAnos() {
        $diretorio = 'public/resultados/';

        if (is_dir($diretorio)) {
            $itens = scandir($diretorio);

            $pastas = array_filter($itens, function ($item) use ($diretorio) {
                return is_dir($diretorio . $item) && $item != '.' && $item != '..';
            });

            return array_values($pastas);
        } else {
            return []; 
        }
    }


    public function votacaoNominal($cargo, $ordenarPor = 'NM_URNA_CANDIDATO', $ordem = 'asc') {
        $resultados = [];

        // Itera sobre os dados para filtrar pelo cargo
        foreach ($this->dadosJson as $resultado) {
            if ($resultado['DS_CARGO'] == $cargo) {
                $resultados[] = $resultado;
            }
        }

        // Define a chave de votos válidos, considerando a possibilidade de variação no nome do campo
        foreach ($resultados as &$resultado) {
            if (!isset($resultado['QT_VOTOS_NOMINAIS_VALIDOS']) && isset($resultado['QT_VOTOS_NOMINAIS'])) {
                $resultado['QT_VOTOS_NOMINAIS_VALIDOS'] = $resultado['QT_VOTOS_NOMINAIS'];  // Ajusta se o campo de votos válidos não existir
            }
        }

        // Se houver resultados, ordena de acordo com o parâmetro
        if (!empty($resultados)) {
            usort($resultados, function ($a, $b) use ($ordenarPor, $ordem) {
                // Verifica se o campo existe para ambos os elementos
                if ($ordenarPor == 'NM_URNA_CANDIDATO') {
                    $valorA = $a[$ordenarPor];
                    $valorB = $b[$ordenarPor];
                } else {
                    // Prioriza "QT_VOTOS_NOMINAIS_VALIDOS", mas considera "QT_VOTOS_NOMINAIS" se não existir
                    $valorA = isset($a['QT_VOTOS_NOMINAIS_VALIDOS']) ? $a['QT_VOTOS_NOMINAIS_VALIDOS'] : $a['QT_VOTOS_NOMINAIS'];
                    $valorB = isset($b['QT_VOTOS_NOMINAIS_VALIDOS']) ? $b['QT_VOTOS_NOMINAIS_VALIDOS'] : $b['QT_VOTOS_NOMINAIS'];
                }

                // Define a ordem crescente ou decrescente
                if ($ordem == 'asc') {
                    return $valorA <=> $valorB;
                } else {
                    return $valorB <=> $valorA;
                }
            });
        }

        return !empty($resultados) ? $resultados : null;
    }
}
