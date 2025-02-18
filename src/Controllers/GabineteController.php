<?php


namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\GabineteModel;
use PDOException;

/**
 * Controlador de gabinetes.
 * 
 * Esta classe gerencia as operações relacionadas aos gabinetes no sistema, como criação, atualização, 
 * busca, listagem e remoção de gabinetes, além de validar e tratar erros durante essas operações.
 */
class GabineteController {

    private $gabineteModel;
    private $logger;

    /**
     * Construtor da classe GabineteController.
     * 
     * Inicializa o modelo de gabinete e o logger para registrar erros.
     */
    public function __construct() {
        $this->gabineteModel = new GabineteModel();
        $this->logger = new Logger();
    }

    /**
     * Cria um novo gabinete no sistema.
     * 
     * Este método recebe os dados de um gabinete, valida os campos obrigatórios, 
     * formata os dados de entrada e insere o gabinete no banco de dados.
     * 
     * Campos obrigatórios:
     * - gabinete_cliente
     * - gabinete_tipo
     * - gabinete_politico
     * - gabinete_estado
     * - gabinete_endereco
     * - gabinete_telefone
     *
     * @param array $dados Os dados do gabinete a serem inseridos.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function novoGabinete($dados) {
        $campos = ['gabinete_cliente', 'gabinete_tipo', 'gabinete_politico', 'gabinete_estado', 'gabinete_endereco', 'gabinete_telefone'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Formata os dados de entrada
        $dados['gabinete_cliente'] = htmlspecialchars(trim($dados['gabinete_cliente']));
        $dados['gabinete_endereco'] = htmlspecialchars(trim($dados['gabinete_endereco']));
        $dados['gabinete_telefone'] = preg_replace('/[^0-9]/', '', $dados['gabinete_telefone']);

        try {
            // Cria o gabinete no banco de dados
            $this->gabineteModel->criar($dados);
            return ['status' => 'success', 'message' => 'Gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            // Trata erros do servidor
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O gabinete já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Atualiza os dados de um gabinete existente.
     * 
     * Este método valida os dados de entrada, busca o gabinete pelo ID e atualiza suas informações no banco de dados.
     * 
     * Campos obrigatórios:
     * - gabinete_id
     * - gabinete_cliente
     * - gabinete_tipo
     * - gabinete_politico
     * - gabinete_estado
     * - gabinete_endereco
     * - gabinete_telefone
     *
     * @param array $dados Os dados do gabinete a serem atualizados.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso gabinete não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function atualizarGabinete($dados) {
        $campos = ['gabinete_id', 'gabinete_cliente', 'gabinete_tipo', 'gabinete_politico', 'gabinete_estado', 'gabinete_endereco', 'gabinete_telefone'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Formata os dados de entrada
        $dados['gabinete_cliente'] = htmlspecialchars(trim($dados['gabinete_cliente']));
        $dados['gabinete_endereco'] = htmlspecialchars(trim($dados['gabinete_endereco']));

        try {
            // Verifica se o gabinete existe
            $buscaGabinete = $this->gabineteModel->busca('gabinete_id', $dados['gabinete_id']);
            if (!$buscaGabinete) {
                return ['status' => 'not_found'];
            }

            // Atualiza os dados do gabinete
            $this->gabineteModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Busca um gabinete pelo ID ou cliente.
     * 
     * Este método permite buscar um gabinete usando um identificador específico (ID ou cliente).
     * 
     * Campos permitidos para a busca:
     * - gabinete_id
     * - gabinete_cliente
     * 
     * @param mixed $valor O valor a ser pesquisado (ID ou cliente do gabinete).
     * @param string $coluna A coluna onde será realizada a busca (pode ser 'gabinete_id' ou 'gabinete_cliente').
     * 
     * @return array Retorna o status da operação com os dados do gabinete ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso gabinete encontrado: `['status' => 'success', 'dados' => $resultado]`
     * - Caso gabinete não encontrado: `['status' => 'not_found']`
     * - Caso coluna inválida: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function buscarGabinete($valor, $coluna = 'gabinete_id') {
        $colunasPermitidas = ['gabinete_id', 'gabinete_cliente'];

        // Valida a coluna de busca
        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Campos obrigatórios não enviados'];
        }

        try {
            // Realiza a busca do gabinete
            $resultado = $this->gabineteModel->busca($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Lista gabinetes com paginação.
     * 
     * Este método lista gabinetes com base em parâmetros de paginação, como número de itens por página, número da página, 
     * ordem e campo de ordenação.
     * 
     * @param int $itens Número de itens a serem exibidos por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem da lista ('ASC' ou 'DESC').
     * @param string $ordenarPor Campo pelo qual a lista será ordenada.
     * 
     * @return array Retorna o status da operação com a lista de gabinetes ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado]`
     * - Caso gabinetes não encontrados: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function listarGabinetes($itens, $pagina, $ordem, $ordenarPor) {
        try {
            // Lista os gabinetes com base nos parâmetros
            $resultado = $this->gabineteModel->listar($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                // Calcula o total de páginas
                $total = (isset($resultado[0]['total_gabinete'])) ? $resultado[0]['total_gabinete'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum gabinete encontrado'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Remove um gabinete do sistema.
     * 
     * Este método recebe o ID de um gabinete e tenta removê-lo do banco de dados.
     * 
     * @param int $gabinete_id O ID do gabinete a ser removido.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso gabinete não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function apagarGabinete($gabinete_id) {
        try {
            // Verifica se o gabinete existe
            $buscaGabinete = $this->gabineteModel->busca('gabinete_id', $gabinete_id);
            if (!$buscaGabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            // Remove o gabinete
            $this->gabineteModel->apagar($gabinete_id);
            return ['status' => 'success', 'message' => 'Gabinete removido com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o cliente. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
