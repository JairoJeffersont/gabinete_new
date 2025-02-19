<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteMvc\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

$idGet = isset($_GET['id']) ? $_GET['id'] : null;

$buscaUsuario = $usuarioController->buscarUsuario('usuario_id', $idGet);

if ($buscaUsuario['status'] != 'success' || empty($idGet)) {
    header('Location: ?secao=usuarios');
}

?>
<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=usuarios" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-people-fill"></i> Editar um usuário</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $usuario = [
                            'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                            'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                            'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                            'usuario_aniversario' => !empty($_POST['usuario_aniversario'])
                                ? DateTime::createFromFormat('d/m', $_POST['usuario_aniversario'])->format('2000-m-d')
                                : null,
                            'usuario_ativo' => htmlspecialchars($_POST['usuario_ativo'], ENT_QUOTES, 'UTF-8'),
                            'usuario_tipo' => htmlspecialchars($_POST['usuario_tipo'], ENT_QUOTES, 'UTF-8'),
                            'usuario_id' => htmlspecialchars($idGet, ENT_QUOTES, 'UTF-8'),

                        ];

                        $result = $usuarioController->atualizarUsuario($usuario);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaUsuario = $usuarioController->buscarUsuario('usuario_id', $idGet);
                        } else if ($result['status'] == 'forbidden' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $usuarioController->apagarUsuario($idGet);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            header('Location: ?secao=usuarios');
                        } else if ($result['status'] == 'forbidden') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados']['usuario_nome'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados']['usuario_email'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="15" value="<?php echo $buscaUsuario['dados']['usuario_telefone'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo !empty($buscaUsuario['dados']['usuario_aniversario']) ? date('d/m', strtotime($buscaUsuario['dados']['usuario_aniversario'])) : '' ?>" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="1" <?php $buscaUsuario['dados']['usuario_email'] = 1 ? 'selected' : '' ?> selected>Ativado</option>
                                <option value="0" <?php $buscaUsuario['dados']['usuario_email'] = 2 ? 'selected' : '' ?>>Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_tipo" required>
                                <?php
                                $buscaTipo = $usuarioController->listarTiposUsuario();
                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['dados'] as $tipo) {
                                        if ($buscaUsuario['dados']['usuario_tipo'] == $tipo['usuario_tipo_id']) {
                                            echo '<option value="' . $tipo['usuario_tipo_id'] . '" selected>' . $tipo['usuario_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['usuario_tipo_id'] . '">' . $tipo['usuario_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>