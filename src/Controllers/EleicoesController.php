<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Utils;

class EleicoesController {
    private $utils;
    private $dadosJson;

    public function __construct($ano, $estado) {
        $this->utils = new Utils();
        $this->dadosJson = $this->lerJsonGzip('public/resultados/' . $ano . '/votacao_candidato_munzona_' . $ano . '_' . $estado . '.jsonl.gz');
    }

    private function lerJsonGzip($caminhoArquivo) {
        $dados = [];

        if (!file_exists($caminhoArquivo)) {
            die("Erro: Arquivo não encontrado - $caminhoArquivo");
        }

        $handle = gzopen($caminhoArquivo, "r");
        if (!$handle) {
            die("Erro ao abrir o arquivo $caminhoArquivo");
        }

        while (!gzeof($handle)) {
            $linha = gzgets($handle);
            if ($linha) {
                $dados[] = json_decode($linha, true); // Decodifica cada linha JSON
            }
        }

        gzclose($handle);
        return $dados;
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
                    $total_votos += isset($votos['QT_VOTOS_NOMINAIS_VALIDOS']) ? 
                        $votos['QT_VOTOS_NOMINAIS_VALIDOS'] : 
                        $votos['QT_VOTOS_NOMINAIS'];
                }
            }
        }

        return ['dados' => $resultados, 'total_votos' => $total_votos];
    }
}
