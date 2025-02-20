<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Controllers\UsuarioController;

class LoginController {

    private $usuarioController;
    private $logger;

    public function __construct() {
        $this->usuarioController = new UsuarioController();
        $this->logger = new Logger();
    }

    public function logar($email, $senha) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        if (empty($senha)) {
            return ['status' => 'invalid_password', 'message' => 'A senha não pode estar vazia.'];
        }

        $buscaUsuario = $this->usuarioController->buscaUsuario('usuario_email', $email);

        if ($buscaUsuario['status'] === 'not_found') {
            return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
        }

        if ($buscaUsuario['status'] === 'error') {
            return ['status' => 'error', 'message' => $buscaUsuario['message'], 'error_id' => $buscaUsuario['error_id']];
        }

        if (!password_verify($senha, $buscaUsuario['dados']['usuario_senha'])) {
            return ['status' => 'invalid_password', 'message' => 'Senha incorreta.'];
        }

        if (!$buscaUsuario['dados']['usuario_ativo']) {
            return ['status' => 'user_deactived', 'message' => 'Usuário desativado.'];
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION = [
            'usuario_id' => $buscaUsuario['dados']['usuario_id'],
            'usuario_nome' => $buscaUsuario['dados']['usuario_nome'],
            'usuario_cliente' => $buscaUsuario['dados']['usuario_cliente'],
            'usuario_tipo' => $buscaUsuario['dados']['usuario_tipo'],
            'usuario_tipo_nome' => $buscaUsuario['dados']['usuario_tipo_nome'],
            'cliente_nome' => $buscaUsuario['dados']['cliente_nome'],
            'cliente_gabinete_estado' => $buscaUsuario['dados']['cliente_gabinete_estado'],
            'cliente_gabinete_nome ' => $buscaUsuario['dados']['cliente_gabinete_nome ']
        ];

        $this->usuarioController->novoLog($_SESSION['usuario_id']);

        $this->logger->novoLog('login.log', $_SESSION['usuario_id'].' | '.$_SESSION['usuario_nome'].' | '.$_SESSION['cliente_gabinete_nome']);

        return ['status' => 'success', 'message' => 'Login feito com sucesso.'];
    }
}
