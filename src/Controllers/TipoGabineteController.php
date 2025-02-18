<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\TipoGabineteModel;
use PDOException;

/**
 * Controlador de tipos de gabinetes.
 * 
 * Esta classe gerencia as operações relacionadas aos tipos de gabinetes no sistema, como criação, atualização, 
 * busca, listagem e remoção, além de validar e tratar erros durante essas operações.
 */
class TipoGabineteController {

    private $tipoGabineteModel;
    private $logger;

    /**
     * Construtor da classe TipoGabineteController.
     * 
     * Inicializa o modelo de tipo de gabinete e o logger para registrar erros.
     */
    public function __construct() {
        $this->tipoGabineteModel = new TipoGabineteModel();
        $this->logger = new Logger();
    }

    /**
     * Cria um novo tipo de gabinete no sistema.
     * 
     * Este método recebe os dados de um tipo de gabinete, valida os campos obrigatórios, 
     * formata os dados de entrada e insere o tipo de gabinete no banco de dados.
     * 
     * Campos obrigatórios:
     * - tipo_gabinete_nome
     * 
     * @param array $dados Os dados do tipo de gabinete a serem inseridos.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function novoTipoGabinete($dados) {
        // Valida os campos obrigatórios
        if (!isset($dados['tipo_gabinete_nome'])) {
            return ['status' => 'bad_request', 'message' => 'Campo tipo_gabinete_nome é obrigatório'];
        }

        // Formata os dados de entrada
        $dados['tipo_gabinete_nome'] = htmlspecialchars(trim($dados['tipo_gabinete_nome']));
        $dados['tipo_gabinete_informacoes'] = isset($dados['tipo_gabinete_informacoes']) ? htmlspecialchars(trim($dados['tipo_gabinete_informacoes'])) : '';

        try {
            // Cria o tipo de gabinete no banco de dados
            $this->tipoGabineteModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            // Trata erro interno
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de gabinete já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Atualiza os dados de um tipo de gabinete existente.
     * 
     * Este método valida os dados de entrada, busca o tipo de gabinete pelo ID e atualiza suas informações no banco de dados.
     * 
     * Campos obrigatórios:
     * - tipo_gabinete_id
     * - tipo_gabinete_nome
     * 
     * @param array $dados Os dados do tipo de gabinete a serem atualizados.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso tipo de gabinete não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function atualizarTipoGabinete($dados) {
        // Valida os campos obrigatórios
        if (!isset($dados['tipo_gabinete_id']) || !isset($dados['tipo_gabinete_nome'])) {
            return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
        }

        // Formata os dados de entrada
        $dados['tipo_gabinete_nome'] = htmlspecialchars(trim($dados['tipo_gabinete_nome']));
        $dados['tipo_gabinete_informacoes'] = isset($dados['tipo_gabinete_informacoes']) ? htmlspecialchars(trim($dados['tipo_gabinete_informacoes'])) : '';

        try {
            // Verifica se o tipo de gabinete existe
            $buscaTipoGabinete = $this->tipoGabineteModel->busca('tipo_gabinete_id', $dados['tipo_gabinete_id']);
            if (!$buscaTipoGabinete) {
                return ['status' => 'not_found'];
            }

            // Atualiza os dados do tipo de gabinete
            $this->tipoGabineteModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Busca um tipo de gabinete pelo ID ou nome.
     * 
     * Este método permite buscar um tipo de gabinete usando um identificador específico (ID ou nome).
     * 
     * Campos permitidos para a busca:
     * - tipo_gabinete_id
     * - tipo_gabinete_nome
     * 
     * @param mixed $valor O valor a ser pesquisado (ID ou nome do tipo de gabinete).
     * @param string $coluna A coluna onde será realizada a busca (pode ser 'tipo_gabinete_id' ou 'tipo_gabinete_nome').
     * 
     * @return array Retorna o status da operação com os dados do tipo de gabinete ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso tipo de gabinete encontrado: `['status' => 'success', 'dados' => $resultado]`
     * - Caso tipo de gabinete não encontrado: `['status' => 'not_found']`
     * - Caso coluna inválida: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function buscarTipoGabinete($valor, $coluna = 'tipo_gabinete_id') {
        $colunasPermitidas = ['tipo_gabinete_id', 'tipo_gabinete_nome'];

        // Valida a coluna de busca
        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request'];
        }

        try {
            // Realiza a busca do tipo de gabinete
            $resultado = $this->tipoGabineteModel->busca($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Lista tipos de gabinetes com paginação.
     * 
     * Este método lista tipos de gabinetes com base em parâmetros de paginação, como número de itens por página, número da página, 
     * ordem e campo de ordenação.
     * 
     * @param int $itens Número de itens a serem exibidos por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem da lista ('ASC' ou 'DESC').
     * @param string $ordenarPor Campo pelo qual a lista será ordenada.
     * 
     * @return array Retorna o status da operação com a lista de tipos de gabinetes ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado]`
     * - Caso tipos de gabinetes não encontrados: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function listarTiposGabinete($itens, $pagina, $ordem, $ordenarPor) {

        try {
            // Lista os tipos de gabinetes com base nos parâmetros
            $resultado = $this->tipoGabineteModel->listar($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                // Calcula o total de páginas
                $total = (isset($resultado[0]['total_tipo_gabinete'])) ? $resultado[0]['total_tipo_gabinete'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de gabinete encontrado'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Apaga um tipo de gabinete do sistema.
     * 
     * Este método apaga um tipo de gabinete do banco de dados utilizando o ID do tipo de gabinete.
     * 
     * @param int $tipoGabineteId O ID do tipo de gabinete a ser removido.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     * - Caso proibido de apagar: `['status' => 'forbidden']`
     */
    public function apagarTipoGabinete($tipoGabineteId) {

        // Valida a presença do ID do cliente
        if (!isset($tipoGabineteId)) {
            return ['status' => 'bad_request'];
        }

        try {

            $buscaCliente = $this->tipoGabineteModel->busca('tipo_gabinete_id', $tipoGabineteId);
            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado'];
            }

            $this->tipoGabineteModel->apagar($tipoGabineteId);
            return ['status' => 'success', 'message' => 'Tipo de gabinete removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de gabinete. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
