<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteMvc\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

$usuarioGet = $_GET['id'];

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $usuarioGet);

if ($buscaUsuario['status'] != 'success') {
    header('Location:?secao=meu-gabinete');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=meu-gabinete" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-person-gear"></i> Editar usuário</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text">Nesta seção, você pode ativar ou desativar um usuário, alterar seu nível administrativo ou excluí-lo.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body card_descricao_body p-2">

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_usuario'])) {

                        $dados = [
                            'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                            'usuario_tipo' => $_POST['usuario_tipo']
                        ];

                        $result = $usuarioController->atualizarUsuario($dados);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Nível do usuário atualizado com sucesso. Aguarde...</div>';
                            echo '<script>
                                            setTimeout(function() {
                                                window.location.href = "?secao=usuario&id=' . $buscaUsuario['dados']['usuario_id'] . '";
                                            }, 1000);
                                        </script>';
                        } elseif ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_desativar_usuario'])) {
                        $dados = [
                            'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                            'usuario_ativo' => 0
                        ];

                        $result = $usuarioController->atualizarUsuario($dados);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Usuário desativado com sucesso. Aguarde...</div>';
                            echo '<script>
                                            setTimeout(function() {
                                                window.location.href = "?secao=usuario&id=' . $buscaUsuario['dados']['usuario_id'] . '";
                                            }, 1000);
                                        </script>';
                        } elseif ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_ativar_usuario'])) {
                        $dados = [
                            'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                            'usuario_ativo' => 1
                        ];
                        $result = $usuarioController->atualizarUsuario($dados);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Usuário ativado com sucesso. Aguarde...</div>';
                            echo '
                                    <script>
                                            setTimeout(function() {
                                                window.location.href = "?secao=usuario&id=' . $buscaUsuario['dados']['usuario_id'] . '";
                                            }, 1000);
                                        </script>';
                        } elseif ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar_usuario'])) {

                        $result = $usuarioController->apagarUsuario($buscaUsuario['dados']['usuario_id']);
                        if ($result['status'] == 'success') {
                            echo '
                                    <script>
                                            setTimeout(function() {
                                                window.location.href = "?secao=meu-gabinete";
                                            }, 1000);
                                        </script>';
                        } elseif ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-1 col-12">
                            <select class="form-select form-select-sm" name="usuario_tipo" required>
                                <?php
                                foreach ($usuarioController->listarTipoUsuario()['dados'] as $tipo) {
                                    if ($tipo['usuario_tipo_id'] != 1) {
                                        if ($tipo['usuario_tipo_id'] == $buscaUsuario['dados']['usuario_tipo']) {
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
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar_usuario"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <?php
                            if ($buscaUsuario['dados']['usuario_ativo']) {
                                echo '<button type="submit" class="btn btn-secondary btn-sm" name="btn_desativar_usuario"><i class="bi bi-floppy-fill"></i> Desativar</button>';
                            } else {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="btn_ativar_usuario"><i class="bi bi-floppy-fill"></i> Ativar</button>';
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body card_descricao_body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_gestor'])) {

                        $dados = [
                            'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                            'usuario_gestor' => $_POST['usuario_gestor']
                        ];

                        $result = $usuarioController->atualizarUsuario($dados);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Novo gestor adicionado com sucesso. Aguarde...</div>';
                            echo '<script>
                        setTimeout(function() {
                            window.location.href = "?secao=usuario&id=' . $buscaUsuario['dados']['usuario_id'] . '";
                        }, 1000);
                    </script>';
                        } elseif ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-1 col-12">
                            <select class="form-select form-select-sm" name="usuario_gestor" required>
                                <option value="1" <?php echo ($buscaUsuario['dados']['usuario_gestor'] == 1) ? 'selected' : ''; ?>>Gestor</option>
                                <option value="0" <?php echo ($buscaUsuario['dados']['usuario_gestor'] == 0) ? 'selected' : ''; ?>>Usuário</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar_gestor"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Últimos acessos</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $buscaLogs = $usuarioController->buscaLog($usuarioGet);
                                if ($buscaLogs['status'] == 'success') {
                                    foreach (array_slice($buscaLogs['dados'], 0, 5) as $log) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y H:i', strtotime($log['log_data'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaLogs['status'] == 'not_found') {
                                    echo '<tr><td colspan="6">' . $buscaLogs['message'] . '</td></tr>';
                                } else if ($buscaUbuscaLogssuarios['status'] == 'error') {
                                    echo '<tr><td colspan="6">' . $buscaLogs['message'] . ' | Código do erro: ' . $buscaLogs['error_id'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>