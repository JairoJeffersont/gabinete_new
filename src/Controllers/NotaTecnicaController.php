<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Models\NotaTecnicaModel;
use GabineteMvc\Middleware\Logger;
use PDOException;

class NotaTecnicaController {

    private $notaTecnicaModel;
    private $logger;

    public function __construct() {
        $this->notaTecnicaModel = new NotaTecnicaModel();
        $this->logger = new Logger();
    }

    public function novaNotaTecnica($dados) {
        try {
            $this->notaTecnicaModel->criarNotaTecnica($dados);
            return ['status' => 'success', 'message' => 'Nota Técnica inserida com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarNotaTecnica($dados) {
        try {
            $buscaNota = $this->notaTecnicaModel->buscaNotaTecnica('nota_id', $dados['id']);

            if (!$buscaNota) {
                return ['status' => 'not_found', 'message' => 'Nota Técnica não encontrada'];
            }

            $this->notaTecnicaModel->atualizarNotaTecnica($dados);
            return ['status' => 'success', 'message' => 'Nota Técnica atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarNotaTecnica($id) {
        try {
            $buscaNota = $this->notaTecnicaModel->buscaNotaTecnica('nota_id', $id);
            if ($buscaNota) {
                return ['status' => 'success', 'dados' => $buscaNota];
            } else {
                return ['status' => 'not_found', 'message' => 'Nota Técnica não encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarNotasTecnicas($itens, $pagina, $ordem, $ordenarPor, $gabinete) {
        try {
            $resultado = $this->notaTecnicaModel->listarNotasTecnicas($itens, $pagina, $ordem, $ordenarPor, $gabinete);

            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhuma Nota Técnica encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarNotaTecnica($id) {
        try {
            $buscaNota = $this->notaTecnicaModel->buscaNotaTecnica('nota_id', $id);

            if (!$buscaNota) {
                return ['status' => 'not_found', 'message' => 'Nota Técnica não encontrada'];
            }

            $this->notaTecnicaModel->apagarNotaTecnica($id);
            return ['status' => 'success', 'message' => 'Nota Técnica apagada com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a Nota Técnica. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
