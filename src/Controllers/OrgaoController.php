<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\OrgaoModel;
use PDOException;

class OrgaoController {

    private $orgaoModel;
    private $logger;

    public function __construct() {
        $this->orgaoModel = new OrgaoModel();
        $this->logger = new Logger();
    }

    // CRIAR ÓRGÃO
    public function novoOrgao($dados) {
        try {
            $this->orgaoModel->criarOrgao($dados);
            return ['status' => 'success', 'message' => 'Órgão inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O órgão já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR ÓRGÃO
    public function atualizarOrgao($dados) {
        try {
            $buscaOrgao = $this->orgaoModel->buscaOrgao('orgao_id', $dados['orgao_id']);

            if (!$buscaOrgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            $this->orgaoModel->atualizarOrgao($dados);
            return ['status' => 'success', 'message' => 'Órgão atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR ÓRGÃO
    public function buscaOrgao($coluna, $valor) {
        try {
            $resultado = $this->orgaoModel->buscaOrgao($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR ÓRGÃOS
    public function listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete) {
        try {
            $resultado = $this->orgaoModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete);

            if ($resultado) {
                $total = (isset($resultado[0]['total'])) ? $resultado[0]['total'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum órgão encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR ÓRGÃO
    public function apagarOrgao($orgaoId) {
        try {
            $buscaOrgao = $this->orgaoModel->buscaOrgao('orgao_id', $orgaoId);

            if (!$buscaOrgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            $this->orgaoModel->apagarOrgao($orgaoId);
            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o órgão. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR NOVO TIPO DE ÓRGÃO
    public function novoOrgaoTipo($dados) {
        try {
            $this->orgaoModel->criarOrgaoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de órgão já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR TIPO DE ÓRGÃO
    public function atualizarOrgaoTipo($dados) {
        try {
            $buscaTipo = $this->orgaoModel->buscaOrgaoTipo($dados['orgao_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            $this->orgaoModel->atualizarOrgaoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE ÓRGÃOS
    public function listarOrgaosTipos($orgao_tipo_gabinete) {
        try {
            $resultado = $this->orgaoModel->listarOrgaosTipos($orgao_tipo_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de órgão encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR TIPO DE ÓRGÃO PELO ID
    public function buscaOrgaoTipo($id) {
        try {
            $resultado = $this->orgaoModel->buscaOrgaoTipo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR TIPO DE ÓRGÃO
    public function apagarOrgaoTipo($tipoId) {
        try {
            $buscaTipo = $this->orgaoModel->buscaOrgaoTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            $this->orgaoModel->apagarOrgaoTipo($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de órgão apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de órgão. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

  
}
