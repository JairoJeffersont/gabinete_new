<?php

use GabineteMvc\Controllers\LoginController;

ob_start();


require_once './vendor/autoload.php';

$loginController = new LoginController();



?>
<link href="public/css/login.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-2">Mandato Digital</h2>
        <p class="text-white">Gestão de gabinete parlamentar</p>

        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_logar'])) {

            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
            $senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');

            $resultado = $loginController->Logar($email, $senha);

            if ($resultado['status'] == 'success') {
                header('Location: ?secao=home');
                exit;
            } else if ($resultado['status'] == 'not_found' || $resultado['status'] == 'user_deactived') {
                echo '<div class="alert alert-info px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'invalid_password' || $resultado['status'] == 'error' || $resultado['status'] == 'invalid_email') {
                echo '<div class="alert alert-danger px-2 rounded-5 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            }
        }

        ?>

        <form id="form_login" method="post" enctype="application/x-www-form-urlencoded" class="form-group">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha"  required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_logar" class="btn">Entrar</button>
            </div>
        </form>
        <p class="mt-3 link"> <a href="?secao=recuperar-senha">Esqueceu a senha?</a> | <a href="?secao=cadastro">Cadastre seu gabinete</a></p>
        <p class="mt-3 copyright">
            &copy; <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.
        </p>

    </div>
</div>