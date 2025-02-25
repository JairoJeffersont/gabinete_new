<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Middleware\Utils;

require 'vendor/autoload.php';

$gabineteController = new GabineteController();
$usuarioController = new UsuarioController();
$utils = new Utils();

?>

<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-1">Mandato Digital</h2>
        <h6 class="host mb-3">Novo Gabinete</h6>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
            if ($_POST['usuario_senha'] == $_POST['usuario_senha2']) {

                $gabinete = [
                    'gabinete_nome' => htmlspecialchars($_POST['gabinete_nome'], ENT_QUOTES, 'UTF-8'),
                    'gabinete_nome_sistema' => $utils->sanitizarString(htmlspecialchars($_POST['gabinete_nome'], ENT_QUOTES, 'UTF-8')),
                    'gabinete_tipo' => htmlspecialchars($_POST['gabinete_tipo'], ENT_QUOTES, 'UTF-8'),
                    'gabinete_estado_autoridade' => htmlspecialchars($_POST['gabinete_estado'], ENT_QUOTES, 'UTF-8'),
                    'gabinete_usuarios' => htmlspecialchars($_POST['gabinete_usuarios'], ENT_QUOTES, 'UTF-8')
                ];

                $resultGabinete = $gabineteController->novoGabinete($gabinete);

                if ($resultGabinete['status'] == 'success') {
                    $buscaIdGabinete = $gabineteController->listarGabinetes(1, 1, 'desc', 'gabinete_criado_em')['dados'][0]['gabinete_id'];

                    $usuario = [
                        'usuario_tipo' => 2,
                        'usuario_gabinete' => $buscaIdGabinete,
                        'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                        'usuario_senha' => htmlspecialchars($_POST['usuario_senha'], ENT_QUOTES, 'UTF-8'),
                        'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                        'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                        'usuario_ativo' => 1,
                        'usuario_gestor' => 1,
                    ];

                    $resultUsuario = $usuarioController->novoUsuario($usuario);

                    if ($resultUsuario['status'] == 'success') {
                        echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $resultUsuario['message'] . '</div>';
                    } else if ($resultUsuario['status'] == 'duplicated' || $resultUsuario['status'] == 'bad_request' || $resultUsuario['status'] == 'invalid_email') {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $resultUsuario['message'] . '</div>';
                    } else if ($resultUsuario['status'] == 'error') {
                        echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $resultUsuario['message'] . ' | Código do erro: ' . $resultUsuario['error_id'] . '</div>';
                    }
                } else if ($resultGabinete['status'] == 'duplicated') {
                    echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $resultGabinete['message'] . '</div>';
                } else if ($resultGabinete['status'] == 'error') {
                    echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $resultGabinete['message'] . '</div>';
                }
            } else {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">Senhas não conferem.</div>';
            }
        }

        ?>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome do resposável pelo sistema" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha" placeholder="Senha" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha2" placeholder="Confirma a senha" required>
            </div>
            <div class="col-md-8 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Telefone (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-4 col-6">
                <input type="text" class="form-control form-control-sm" name="gabinete_usuarios" placeholder="Assinaturas"  required>
            </div>

            <div class="col-md-6 col-6">
                <select class="form-select form-select-sm form_dep" name="gabinete_tipo" required>
                    <option value="0" selected>Tipo do Gabinete</option>
                    <?php

                    $buscaTipo = $gabineteController->listarTipoGabinete();

                    if ($buscaTipo['status'] == 'success') {
                        foreach ($buscaTipo['dados'] as $tipo) {
                            echo '<option value="' . $tipo['gabinete_tipo_id'] . '">' . $tipo['gabinete_tipo_nome'] . '</option>';
                        }
                    }

                    ?>
                </select>
            </div>
            <div class="col-md-6 col-6">
                <select class="form-select form-select-sm form_dep" name="gabinete_estado" required>
                    <option selected>Escolha o estado</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AM">Amazonas</option>
                    <option value="AP">Amapá</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="PR">Paraná</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SE">Sergipe</option>
                    <option value="SP">São Paulo</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_nome" placeholder="Nome do deputado, senador..." required>
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