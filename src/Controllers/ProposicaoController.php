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

    //OPERACOES DADOS ABERTO CAMARA
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


        foreach ($proposicoes as &$proposicao) {
            $buscaAutores = $this->buscarAutoresProposicaoCD($proposicao['id']);
            $proposicao['proposicao_autores'] = ($buscaAutores['status'] == 'success') ? $buscaAutores['dados'] : [];
        }
        unset($proposicao);

        return [
            'code' => '200',
            'status' => 'success',
            'dados' => $proposicoes,
            'total_paginas' => $total_paginas
        ];
    }

    public function buscarDetalheProposicaoCD($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId);
    }

    public function buscarTramitacoesProposicaoCD($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/tramitacoes');
    }

    public function buscarAutoresProposicaoCD($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/autores');
    }

    public function buscarTiposProposicaoCD() {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/referencias/proposicoes/siglaTipo');
    }


    //OPERACOES DADOS ABERTOS SENADO
    public function buscarProposicoesSenado($autor, $ano, $tipo) {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/pesquisa/lista?sigla=' . $tipo . '&ano=' . $ano . '&nomeAutor=' . $autor);
    }

    public function buscarDetalheSenado($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/' . $proposicaoId);
    }

    public function buscarTextoSenado($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/textos/' . $proposicaoId);
    }

    public function buscarTramitacoesSenado($proposicaoId) {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/movimentacoes/' . $proposicaoId);
    }

    public function buscarTiposProposicaoSenado() {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/dados/ListaSiglas.json');
    }
}
