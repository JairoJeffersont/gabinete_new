<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Middleware\GetJson;
use GabineteMvc\Middleware\Utils;
use GabineteMvc\Models\ProposicaoModel;
use PDOException;

class ProposicaoController {

    private $logger;
    private $getJson;
    private $utils;
    private $proposicaoModel;

    public function __construct() {
        $this->logger = new Logger();
        $this->getJson = new GetJson();
        $this->utils = new Utils();
        $this->proposicaoModel = new ProposicaoModel();
    }

    //OPERACOES DADOS ABERTO CAMARA
    public function buscarProposicoesDeputado($autor, $ano, $itens, $pagina, $tipo) {
        // Gerar uma chave única para o cache baseada nos parâmetros da requisição
        $cacheKey = md5($autor . $ano . $itens . $pagina . $tipo);
        $cacheFile = '/tmp/cache_' . $cacheKey . '.cache';

        // Verificar se o cache existe e não expirou (cache válido por 10 minutos)
        if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - 600))) {
            // Se o cache não expirou, leia os dados armazenados no arquivo
            $dados = unserialize(file_get_contents($cacheFile));
            return $dados; // Retornar os dados diretamente do cache
        }

        // Buscar o deputado
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

        // Buscar as proposições do deputado
        $response = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes?idDeputadoAutor=' . $ideDep . '&itens=' . $itens . '&pagina=' . $pagina . '&ano=' . $ano . '&ordem=DESC&ordenarPor=id&siglaTipo=' . $tipo);

        $proposicoes = $response['dados'];
        $total_registros = isset($response['headers']['x-total-count']) ? (int) $response['headers']['x-total-count'] : 0;
        $total_paginas = $itens > 0 ? ceil($total_registros / $itens) : 1;

        if (empty($proposicoes)) {
            return ['status' => 'empty', 'message' => 'Nenhuma proposição encontrada.'];
        }

        // Adicionar autores às proposições
        foreach ($proposicoes as &$proposicao) {
            $buscaAutores = $this->buscarAutoresProposicaoCD($proposicao['id']);
            $proposicao['proposicao_autores'] = ($buscaAutores['status'] == 'success') ? $buscaAutores['dados'] : [];
        }
        unset($proposicao);

        // Preparar a resposta para cache
        $dados = [
            'code' => '200',
            'status' => 'success',
            'dados' => $proposicoes,
            'total_paginas' => $total_paginas
        ];

        // Armazenar os dados no cache
        file_put_contents($cacheFile, serialize($dados));

        return $dados;
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

    //OPERACOES BANCO DE DADOS

    public function criarProposicaoTema($dados) {
        $camposObrigatorios = ['proposicao_tema_nome', 'proposicao_tema_criado_por', 'proposicao_tema_gabinete'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->proposicaoModel->novoTemaProposicao($dados);
            return ['status' => 'success', 'message' => 'Proposição de tema criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do proposição de tema já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarProposicoesTemas($cliente) {
        try {
            $proposicoesTemas = $this->proposicaoModel->listarTemaProposicao($cliente);

            if (empty($proposicoesTemas)) {
                return ['status' => 'empty', 'message' => 'Nenhum proposição de tema registrado.'];
            }

            return ['status' => 'success', 'message' => count($proposicoesTemas) . ' proposição(ões) de tema(s) encontrado(s)', 'dados' => $proposicoesTemas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarProposicaoTema($coluna, $valor) {
        $colunasPermitidas = ['proposicao_tema_id', 'proposicao_tema_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas proposicao_tema_id e proposicao_tema_nome são permitidos.'];
        }

        try {
            $proposicaoTema = $this->proposicaoModel->buscar($coluna, $valor);
            if ($proposicaoTema) {
                return ['status' => 'success', 'dados' => $proposicaoTema];
            } else {
                return ['status' => 'not_found', 'message' => 'Proposição de tema não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
