<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Middleware\Utils;

require 'vendor/autoload.php';

$gabineteController = new GabineteController();
$usuarioController = new UsuarioController();
$utils = new Utils();

$token = $_GET['token'];

$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $token);
$buscaUsuarios = $usuarioController->listarUsuarios(1000, 1, 'asc', 'usuario_nome', $token);

?>

<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-1">Mandato Digital</h2>
        <h6 class="host mb-3">Cadastro de usuário | Todos os campos são obrigatórios</h6>

        <?php

        if ($buscaGabinete['status'] != 'success') {
            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">Gabinete não endcontrado</div>';
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                if ($_POST['usuario_senha'] !== $_POST['usuario_senha2']) {
                    echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">As senha não conferem</div>';
                } elseif (strlen($_POST['usuario_senha']) < 6) {
                    echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">A senha tem menos de 6 caracteres</div>';
                } else {
                    $usuario = [
                        'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                        'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                        'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                        'usuario_aniversario' => $utils->formatarAniversario(htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8')),
                        'usuario_ativo' => 0,
                        'usuario_tipo' => 2,
                        'usuario_senha' => htmlspecialchars($_POST['usuario_senha'], ENT_QUOTES, 'UTF-8'),
                        'usuario_gabinete' => $buscaGabinete['dados']['gabinete_id']
                    ];

                    if ($buscaGabinete['dados']['gabinete_usuarios'] <= count($buscaUsuarios['dados'])) {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">Não existem assinaturas disponíveis</div>';
                    } else {
                        $result = $usuarioController->novoUsuario($usuario);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                }
            }
        }



        ?>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_aniversario" placeholder="dd/mm" data-mask="00/00" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha" placeholder="Senha" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha2" placeholder="Confirme a senha" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright">
            &copy; <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.
        </p>
    </div>
</div>

<div class="modal fade" id="aguardeModal" tabindex="-1" aria-labelledby="aguardeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="mb-3">Aguarde...</h5>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        </div>
    </div>
</div>