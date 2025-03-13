<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Middleware\GetJson;
use GabineteMvc\Middleware\Utils;
use PDOException;

class ProposicaoController {

    private $logger;
    private $getJson;
    private $utils;

    public function __construct() {
        $this->logger = new Logger();
        $this->getJson = new GetJson();
        $this->utils = new Utils();
    }


    public function buscarProposicoesDeputado($autor, $ano, $itens, $pagina, $tipo) {
        $buscaDep = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/arquivos/deputados/json/deputados.json');

        if ($buscaDep['status'] == 'success' && !empty($buscaDep['dados'])) {
            foreach ($buscaDep['dados'] as $dep) {
                if ($this->utils->sanitizarString($dep['nome']) == $this->utils->sanitizarString($autor)) {
                    $ideDep = basename($dep['uri']);
                }
            }
        } else {
            return $buscaDep;
        }

        $response = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes?idDeputadoAutor=' . $ideDep . '&itens=' . $itens . '&pagina=' . $pagina . '&ano=' . $ano . '&ordem=DESC&ordenarPor=id&siglaTipo=' . $tipo);

        $proposicoes = $response['dados'];
        $total_registros = isset($response['headers']['x-total-count']) ? (int) $response['headers']['x-total-count'] : 0;
        $total_paginas = $itens > 0 ? ceil($total_registros / $itens) : 1;

        if (empty($proposicoes)) {
            return ['status' => 'empty', 'message' => 'Nenhuma proposição encontrada.'];
        }

        return [
            'code' => '200',
            'status' => 'success',
            'dados' => $proposicoes,
            'total_paginas' => $total_paginas
        ];
    }

    public function buscarDetalhe($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId);
    }

    public function buscarTramitacoes($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/tramitacoes');
    }

    public function buscarAutores($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/autores');
    }
}
