<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\OrgaoTipoModel;
use PDOException;

class OrgaoTipoController {

    private $orgaoTipoModel;
    private $logger;

    public function __construct() {
        $this->orgaoTipoModel = new OrgaoTipoModel();
        $this->logger = new Logger();
    }

    // CRIAR NOVO TIPO DE ÓRGÃO
    public function novoOrgaoTipo($dados) {
        try {
            $this->orgaoTipoModel->criarOrgaoTipo($dados);
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
            $buscaTipo = $this->orgaoTipoModel->buscaOrgaoTipo($dados['orgao_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            $this->orgaoTipoModel->atualizarOrgaoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE ÓRGÃOS
    public function listarOrgaosTipos() {
        try {
            $resultado = $this->orgaoTipoModel->listarOrgaosTipos();
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
            $resultado = $this->orgaoTipoModel->buscaOrgaoTipo($id);
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
            $buscaTipo = $this->orgaoTipoModel->buscaOrgaoTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            $this->orgaoTipoModel->apagarOrgaoTipo($tipoId);
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
