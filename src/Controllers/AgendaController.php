<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\AgendaModel;
use PDOException;

class AgendaController {
    private $agendaModel;
    private $logger;

    public function __construct() {
        $this->agendaModel = new AgendaModel();
        $this->logger = new Logger();
    }


    // CRIAR NOVO EVENTO DE AGENDA
    public function novaAgenda($dados) {
        try {
            $this->agendaModel->criarAgenda($dados);
            return ['status' => 'success', 'message' => 'Evento de agenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O evento de agenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR EVENTO DE AGENDA
    public function atualizarAgenda($dados) {
        try {
            $buscaAgenda = $this->agendaModel->buscaAgenda($dados['agenda_id']);

            if (!$buscaAgenda) {
                return ['status' => 'not_found', 'message' => 'Evento de agenda não encontrado'];
            }

            $this->agendaModel->atualizarAgenda($dados);
            return ['status' => 'success', 'message' => 'Evento de agenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    // LISTAR EVENTOS DA AGENDA
    public function listarAgendas($data, $tipo, $situacao,  $cliente) {
        try {
            $agendas = $this->agendaModel->listarAgendas($data, $tipo, $situacao,  $cliente);

            if (empty($agendas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma agenda registrada.'];
            }

            return ['status' => 'success', 'message' => count($agendas) . ' agenda(s) encontrada(s)', 'dados' => $agendas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR EVENTO DE AGENDA PELO ID
    public function buscaAgenda($id) {
        try {
            $resultado = $this->agendaModel->buscaAgenda($id);
            return ['status' => 'success', 'dados' => $resultado];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    // APAGAR EVENTO DA AGENDA
    public function apagarAgenda($id) {
        try {
            $buscaAgenda = $this->agendaModel->buscaAgenda($id);
            if (!$buscaAgenda) {
                return ['status' => 'not_found', 'message' => 'Evento de agenda não encontrado'];
            }

            $this->agendaModel->apagarAgenda($id);
            return ['status' => 'success', 'message' => 'Evento de agenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o evento da agenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    // CRIAR NOVO TIPO DE AGENDA
    public function novoAgendaTipo($dados) {
        try {
            $this->agendaModel->criarTipoAgenda($dados);
            return ['status' => 'success', 'message' => 'Tipo de agenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de agenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR TIPO DE AGENDA
    public function atualizarAgendaTipo($dados) {
        try {
            $buscaTipo = $this->agendaModel->buscaTipoAgenda($dados['agenda_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de agenda não encontrado'];
            }

            if ($buscaTipo['agenda_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipo de órgão padrão dos sistema.'];
            }

            $this->agendaModel->atualizarTipoAgenda($dados);
            return ['status' => 'success', 'message' => 'Tipo de agenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE AGENDA
    public function listarAgendaTipos($gabinete) {
        try {
            $resultado = $this->agendaModel->listarTiposAgenda($gabinete);
            return ['status' => 'success', 'dados' => $resultado];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR TIPO DE AGENDA PELO ID
    public function buscaAgendaTipo($id) {
        try {
            $resultado = $this->agendaModel->buscaTipoAgenda($id);
            return ['status' => 'success', 'dados' => $resultado];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR TIPO DE AGENDA
    public function apagarAgendaTipo($id) {
        try {
            $buscaTipo = $this->agendaModel->buscaTipoAgenda($id);
            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de agenda não encontrado'];
            }

            if ($buscaTipo['agenda_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipo de órgão padrão dos sistema.'];
            }

            $this->agendaModel->apagarTipoAgenda($id);
            return ['status' => 'success', 'message' => 'Tipo de agenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de agenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR NOVA SITUAÇÃO DA AGENDA
    public function novaAgendaSituacao($dados) {
        try {
            $this->agendaModel->criarSituacaoAgenda($dados);
            return ['status' => 'success', 'message' => 'Situação da agenda inserida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A situação da agenda já está cadastrada'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR SITUAÇÃO DA AGENDA
    public function atualizarAgendaSituacao($dados) {
        try {
            $buscaSituacao = $this->agendaModel->buscaSituacaoAgenda($dados['agenda_situacao_id']);

            if (!$buscaSituacao) {
                return ['status' => 'not_found', 'message' => 'Situação da agenda não encontrada'];
            }

            if ($buscaSituacao['agenda_situacao_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar uma situação padrão do sistema.'];
            }

            $this->agendaModel->atualizarSituacaoAgenda($dados);
            return ['status' => 'success', 'message' => 'Situação da agenda atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR SITUAÇÕES DA AGENDA
    public function listarAgendaSituacoes($gabinete) {
        try {
            $resultado = $this->agendaModel->listarSituacoesAgenda($gabinete);
            return ['status' => 'success', 'dados' => $resultado];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR SITUAÇÃO DA AGENDA PELO ID
    public function buscaAgendaSituacao($id) {
        try {
            $resultado = $this->agendaModel->buscaSituacaoAgenda($id);
            return ['status' => 'success', 'dados' => $resultado];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR SITUAÇÃO DA AGENDA
    public function apagarAgendaSituacao($id) {
        try {
            $buscaSituacao = $this->agendaModel->buscaSituacaoAgenda($id);
            if (!$buscaSituacao) {
                return ['status' => 'not_found', 'message' => 'Situação da agenda não encontrada'];
            }

            if ($buscaSituacao['agenda_situacao_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível excluir uma situação padrão do sistema.'];
            }

            $this->agendaModel->apagarSituacaoAgenda($id);
            return ['status' => 'success', 'message' => 'Situação da agenda apagada com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a situação da agenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
