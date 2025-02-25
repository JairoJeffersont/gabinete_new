<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\EmendaModel;

use PDOException;

class EmendaController {

    private $emendaModel;
    private $logger;

    public function __construct() {
        $this->emendaModel = new EmendaModel();
        $this->logger = new Logger();
    }

    // CRIAR EMENDA STATUS
    public function novoEmendaStatus($dados) {
        try {
            $this->emendaModel->criarEmendaStatus($dados);
            return ['status' => 'success', 'message' => 'Status de emenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O status de emenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR EMENDA STATUS
    public function atualizarEmendaStatus($dados) {
        try {
            $buscaStatus = $this->emendaModel->buscaEmendaStatus($dados['emendas_status_id']);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }

            if ($buscaStatus['emendas_status_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um status padrão do sistema.'];
            }

            $this->emendaModel->atualizarEmendaStatus($dados);
            return ['status' => 'success', 'message' => 'Status de emenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR EMENDA STATUS
    public function listarEmendaStatus($gabinete) {
        try {
            $resultado = $this->emendaModel->listarEmendaStatus($gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum status de emenda encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR EMENDA STATUS POR ID
    public function buscaEmendaStatus($id) {
        try {
            $resultado = $this->emendaModel->buscaEmendaStatus($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR EMENDA STATUS
    public function apagarEmendaStatus($statusId) {
        try {
            $buscaStatus = $this->emendaModel->buscaEmendaStatus($statusId);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }

            if ($buscaStatus['emendas_status_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um status padrão do sistema.'];
            }

            $this->emendaModel->apagarEmendaStatus($statusId);
            return ['status' => 'success', 'message' => 'Status de emenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o status de emenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR EMENDA OBJETIVO
    public function novoEmendaObjetivo($dados) {
        try {
            $this->emendaModel->criarEmendaObjetivo($dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O objetivo de emenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR EMENDA OBJETIVO
    public function atualizarEmendaObjetivo($dados) {
        try {
            $buscaObjetivo = $this->emendaModel->buscaEmendaObjetivo($dados['emendas_objetivos_id']);

            if (!$buscaObjetivo) {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }

            if ($buscaObjetivo['emendas_objetivos_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um objetivo padrão do sistema.'];
            }

            $this->emendaModel->atualizarEmendaObjetivo($dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR EMENDA OBJETIVO
    public function listarEmendaObjetivo($gabinete) {
        try {
            $resultado = $this->emendaModel->listarEmendaObjetivo($gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum objetivo de emenda encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR EMENDA OBJETIVO POR ID
    public function buscaEmendaObjetivo($id) {
        try {
            $resultado = $this->emendaModel->buscaEmendaObjetivo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR EMENDA OBJETIVO
    public function apagarEmendaObjetivo($objetivoId) {
        try {
            $buscaObjetivo = $this->emendaModel->buscaEmendaObjetivo($objetivoId);

            if (!$buscaObjetivo) {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }

            if ($buscaObjetivo['emendas_objetivos_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um objetivo padrão do sistema.'];
            }

            $this->emendaModel->apagarEmendaObjetivo($objetivoId);
            return ['status' => 'success', 'message' => 'Objetivo de emenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o objetivo de emenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
