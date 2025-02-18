<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\ClienteModel;
use PDOException;

/**
 * Controlador de clientes.
 * 
 * Esta classe gerencia as operações relacionadas aos clientes no sistema, como criação, atualização, 
 * busca, listagem e remoção de clientes, além de validar e tratar erros durante essas operações.
 */
class ClienteController {

    private $clienteModel;
    private $logger;

    /**
     * Construtor da classe ClienteController.
     * 
     * Inicializa o modelo de cliente e o logger para registrar erros.
     */
    public function __construct() {
        $this->clienteModel = new ClienteModel();
        $this->logger = new Logger();
    }

    /**
     * Cria um novo cliente no sistema.
     * 
     * Este método recebe os dados de um cliente, valida os campos obrigatórios, 
     * formata os dados de entrada e insere o cliente no banco de dados.
     * 
     * Campos obrigatórios:
     * - cliente_nome
     * - cliente_email
     * - cliente_telefone
     * - cliente_ativo
     * - cliente_endereco
     * - cliente_cep
     * - cliente_cpf
     *
     * @param array $dados Os dados do cliente a serem inseridos.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso email inválido: `['status' => 'invalid_email']`
     * - Caso cliente duplicado: `['status' => 'duplicated']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function novoCliente($dados) {
        $campos = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_endereco', 'cliente_cep', 'cliente_cpf'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Valida o formato do email
        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'E-mail inválido'];
        }

        // Formata os dados de entrada
        $dados['cliente_nome'] = htmlspecialchars(trim($dados['cliente_nome']));
        $dados['cliente_telefone'] = preg_replace('/[^0-9]/', '', $dados['cliente_telefone']);
        $dados['cliente_endereco'] = htmlspecialchars(trim($dados['cliente_endereco']));
        $dados['cliente_cep'] = preg_replace('/[^0-9]/', '', $dados['cliente_cep']);
        $dados['cliente_cpf'] = preg_replace('/[^0-9]/', '', $dados['cliente_cpf']);

        try {
            // Cria o cliente no banco de dados
            $this->clienteModel->criar($dados);
            return ['status' => 'success', 'message' => 'Cliente inserido com sucesso'];
        } catch (PDOException $e) {
            // Trata erro de cliente duplicado ou outros erros do servidor
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O cliente já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Atualiza os dados de um cliente existente.
     * 
     * Este método valida os dados de entrada, busca o cliente pelo ID e atualiza suas informações no banco de dados.
     * 
     * Campos obrigatórios:
     * - cliente_id
     * - cliente_nome
     * - cliente_email
     * - cliente_telefone
     * - cliente_ativo
     * - cliente_endereco
     * - cliente_cep
     * - cliente_cpf
     *
     * @param array $dados Os dados do cliente a serem atualizados.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso email inválido: `['status' => 'invalid_email']`
     * - Caso cliente não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function atualizarCliente($dados) {
        $campos = ['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_endereco', 'cliente_cep', 'cliente_cpf'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Valida o formato do email
        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'E-mail inválido'];
        }

        // Formata os dados de entrada
        $dados['cliente_nome'] = htmlspecialchars(trim($dados['cliente_nome']));
        $dados['cliente_telefone'] = preg_replace('/[^0-9]/', '', $dados['cliente_telefone']);
        $dados['cliente_endereco'] = htmlspecialchars(trim($dados['cliente_endereco']));
        $dados['cliente_cep'] = preg_replace('/[^0-9]/', '', $dados['cliente_cep']);
        $dados['cliente_cpf'] = preg_replace('/[^0-9]/', '', $dados['cliente_cpf']);

        try {
            // Verifica se o cliente existe
            $buscaCliente = $this->clienteModel->busca('cliente_id', $dados['cliente_id']);
            if (!$buscaCliente) {
                return ['status' => 'not_found'];
            }

            // Atualiza os dados do cliente
            $this->clienteModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Busca um cliente pelo ID ou email.
     * 
     * Este método permite buscar um cliente usando um identificador específico (ID ou email).
     * 
     * Campos permitidos para a busca:
     * - cliente_id
     * - cliente_email
     * 
     * @param mixed $valor O valor a ser pesquisado (ID ou email do cliente).
     * @param string $coluna A coluna onde será realizada a busca (pode ser 'cliente_id' ou 'cliente_email').
     * 
     * @return array Retorna o status da operação com os dados do cliente ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso cliente encontrado: `['status' => 'success', 'dados' => $resultado]`
     * - Caso cliente não encontrado: `['status' => 'not_found']`
     * - Caso coluna inválida: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function buscarCliente($valor, $coluna = 'cliente_id') {
        $colunasPermitidas = ['cliente_id', 'cliente_email'];

        // Valida a coluna de busca
        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request'];
        }

        try {
            // Realiza a busca do cliente
            $resultado = $this->clienteModel->busca($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Lista clientes com paginação.
     * 
     * Este método lista clientes com base em parâmetros de paginação, como número de itens por página, número da página, 
     * ordem e campo de ordenação.
     * 
     * @param int $itens Número de itens a serem exibidos por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem da lista ('ASC' ou 'DESC').
     * @param string $ordenarPor Campo pelo qual a lista será ordenada.
     * 
     * @return array Retorna o status da operação com a lista de clientes ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado]`
     * - Caso clientes não encontrados: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function listarClientes($itens, $pagina, $ordem, $ordenarPor) {

        try {
            // Lista os clientes com base nos parâmetros
            $resultado = $this->clienteModel->listar($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                // Calcula o total de páginas
                $total = (isset($resultado[0]['total_cliente'])) ? $resultado[0]['total_cliente'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum cliente encontrado'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Apaga um cliente do sistema.
     * 
     * Este método remove um cliente do banco de dados utilizando o ID do cliente.
     * 
     * @param int $clienteId O ID do cliente a ser apagado.
     * 
     * @return array Retorna o status da operação, com mensagens de sucesso ou erro.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso cliente não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     * - Caso proibido de apagar: `['status' => 'forbidden']`
     */
    public function apagarCliente($clienteId) {

        // Valida a presença do ID do cliente
        if (!isset($clienteId)) {
            return ['status' => 'bad_request'];
        }

        try {
            // Verifica se o cliente existe
            $buscaCliente = $this->clienteModel->busca('cliente_id', $clienteId);
            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado'];
            }

            // Apaga o cliente
            $this->clienteModel->apagar($clienteId);
            return ['status' => 'success', 'message' => 'Cliente apagado com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o cliente. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
