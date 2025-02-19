<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Controllers\GabineteController;

class LoginController {

    private $usuarioController;
    private $gabineteController;
    private $logger;

    public function __construct() {
        $this->usuarioController = new UsuarioController();
        $this->gabineteController = new GabineteController();
        $this->logger = new Logger();
    }

    public function logar($email, $senha) {
        $buscaUsuario = $this->usuarioController->buscarUsuario('usuario_email', $email);

        if ($buscaUsuario['status'] == 'not_found') {
            return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
        }

        if ($buscaUsuario['status'] == 'success') {

            if (!$buscaUsuario['dados']['usuario_ativo']) {
                return ['status' => 'deactivated', 'message' => 'Usuário desativado.'];
            }

            if (password_verify($senha, $buscaUsuario['dados']['usuario_senha'])) {
                session_start();

                $buscaGabinete = $this->gabineteController->buscarGabinete($buscaUsuario['dados']['usuario_gabinete']);

                if (!$buscaGabinete['status'] = 'success') {
                    return ['status' => 'error', 'message' => 'Erro interno do servidor.', 'id_erro' => $buscaUsuario['error_id']];
                }

                $_SESSION = [
                    'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                    'usuario_nome' => $buscaUsuario['dados']['usuario_nome'],
                    'usuario_tipo' => $buscaUsuario['dados']['usuario_tipo'],
                    'usuario_gabinete' => $buscaUsuario['dados']['usuario_gabinete'],
                    'gabinete_funcionarios' => $buscaGabinete['dados']['gabinete_funcionarios'],
                    'gabinete_politico' => $buscaGabinete['dados']['gabinete_politico']
                ];

                $this->logger->novoLog('login_log', $buscaUsuario['dados']['usuario_gabinete'] . ' | ' . $buscaUsuario['dados']['usuario_nome']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Erro interno do servidor.', 'id_erro' => $buscaUsuario['error_id']];
        }
    }
}
