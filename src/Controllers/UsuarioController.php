<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\UsuarioModel;
use PDOException;

/**
 * Controlador de usuários.
 * 
 * Esta classe gerencia as operações relacionadas aos usuários no sistema, como criação, atualização, 
 * busca, listagem e remoção de usuários, além de validar e tratar erros durante essas operações.
 */
class UsuarioController {

    private $usuarioModel;
    private $logger;

    /**
     * Construtor da classe UsuarioController.
     * 
     * Inicializa o modelo de usuário e o logger para registrar erros.
     */
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->logger = new Logger();
    }

    /**
     * Cria um novo usuário no sistema.
     * 
     * Este método recebe os dados de um usuário, valida os campos obrigatórios, 
     * formata os dados de entrada e insere o usuário no banco de dados.
     * 
     * Campos obrigatórios:
     * - usuario_nome
     * - usuario_email
     * - usuario_telefone
     * - usuario_senha
     * - usuario_tipo
     * - usuario_ativo
     * - usuario_gabinete
     * - usuario_aniversario
     *
     * @param array $dados Os dados do usuário a serem inseridos.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso email inválido: `['status' => 'invalid_email']`
     * - Caso usuário duplicado: `['status' => 'duplicated']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function novoUsuario($dados) {
        $campos = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_tipo', 'usuario_ativo', 'usuario_gabinete', 'usuario_aniversario'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Valida o formato do email
        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'E-mail inválido'];
        }

        // Formata os dados de entrada
        $dados['usuario_nome'] = htmlspecialchars(trim($dados['usuario_nome']));
        $dados['usuario_telefone'] = preg_replace('/[^0-9]/', '', $dados['usuario_telefone']);
        $dados['usuario_gabinete'] = htmlspecialchars(trim($dados['usuario_gabinete']));
        $dados['usuario_aniversario'] = htmlspecialchars(trim($dados['usuario_aniversario']));

        try {
            // Cria o usuário no banco de dados
            $this->usuarioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso'];
        } catch (PDOException $e) {
            // Trata erro de usuário duplicado ou outros erros do servidor
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O usuário já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Atualiza os dados de um usuário existente.
     * 
     * Este método valida os dados de entrada, busca o usuário pelo ID e atualiza suas informações no banco de dados.
     * 
     * Campos obrigatórios:
     * - usuario_id
     * - usuario_nome
     * - usuario_email
     * - usuario_telefone
     * - usuario_senha
     * - usuario_tipo
     * - usuario_ativo
     * - usuario_gabinete
     * - usuario_aniversario
     *
     * @param array $dados Os dados do usuário a serem atualizados.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso campo obrigatório ausente: `['status' => 'bad_request']`
     * - Caso email inválido: `['status' => 'invalid_email']`
     * - Caso usuário não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function atualizarUsuario($dados) {
        $campos = ['usuario_id', 'usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_tipo', 'usuario_ativo', 'usuario_gabinete', 'usuario_aniversario'];

        // Valida os campos obrigatórios
        foreach ($campos as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
            }
        }

        // Valida o formato do email
        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'E-mail inválido'];
        }

        // Formata os dados de entrada
        $dados['usuario_nome'] = htmlspecialchars(trim($dados['usuario_nome']));
        $dados['usuario_telefone'] = preg_replace('/[^0-9]/', '', $dados['usuario_telefone']);
        $dados['usuario_gabinete'] = htmlspecialchars(trim($dados['usuario_gabinete']));
        $dados['usuario_aniversario'] = htmlspecialchars(trim($dados['usuario_aniversario']));

        try {
            // Verifica se o usuário existe
            $buscaUsuario = $this->usuarioModel->busca('usuario_id', $dados['usuario_id']);
            if (!$buscaUsuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            // Atualiza os dados do usuário
            $this->usuarioModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Busca um usuário pelo ID ou email.
     * 
     * Este método permite buscar um usuário usando um identificador específico (ID ou email).
     * 
     * Campos permitidos para a busca:
     * - usuario_id
     * - usuario_email
     * 
     * @param mixed $valor O valor a ser pesquisado (ID ou email do usuário).
     * @param string $coluna A coluna onde será realizada a busca (pode ser 'usuario_id' ou 'usuario_email').
     * 
     * @return array Retorna o status da operação com os dados do usuário ou uma mensagem de erro.
     * 
     * Exemplo de retorno:
     * - Caso usuário encontrado: `['status' => 'success', 'dados' => $resultado]`
     * - Caso usuário não encontrado: `['status' => 'not_found']`
     * - Caso coluna inválida: `['status' => 'bad_request']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function buscarUsuario($valor, $coluna = 'usuario_id') {
        $colunasPermitidas = ['usuario_id', 'usuario_email'];

        // Valida a coluna de busca
        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
        }

        try {
            // Realiza a busca do usuário
            $resultado = $this->usuarioModel->busca($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Lista usuários com paginação.
     * 
     * Este método lista usuários com base em parâmetros de paginação, como número de itens por página, número da página, 
     * ordem e campo de ordenação.
     * 
     * @param int $itens Número de itens a serem exibidos por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem dos resultados (asc ou desc).
     * @param string $campo Campo para ordenação (ex: 'usuario_nome').
     * 
     * @return array Retorna a lista de usuários com o status da operação.
     */
    public function listarUsuarios($itens = 10, $pagina = 1, $ordem = 'asc', $campo = 'usuario_nome') {
        try {
            $usuarios = $this->usuarioModel->listar($itens, $pagina, $ordem, $campo);
            return ['status' => 'success', 'dados' => $usuarios];
        } catch (PDOException $e) {
            // Trata erros internos
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Remove um usuário do sistema.
     * 
     * Este método remove um usuário do banco de dados baseado no ID.
     * 
     * @param int $id O ID do usuário a ser removido.
     * 
     * @return array Retorna o status da operação.
     * 
     * Exemplo de retorno:
     * - Caso sucesso: `['status' => 'success']`
     * - Caso erro de usuário não encontrado: `['status' => 'not_found']`
     * - Caso erro interno: `['status' => 'error', 'error_id' => 'unique_error_id']`
     */
    public function removerUsuario($id) {
        try {
            // Verifica se o usuário existe
            $usuario = $this->usuarioModel->busca('usuario_id', $id);
            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            // Remove o usuário
            $this->usuarioModel->apagar($id);
            return ['status' => 'success', 'message' => 'Usuário removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
