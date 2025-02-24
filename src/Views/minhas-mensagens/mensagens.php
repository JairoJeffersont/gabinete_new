<?php

use GabineteMvc\Controllers\MensagemController;
use GabineteMvc\Controllers\UsuarioController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$mensagemController = new MensagemController();
$usuarioController = new UsuarioController();

$arquivada = isset($_GET['arquivada']) ? $_GET['arquivada'] : 0;

$buscaMensagens = $mensagemController->listarMensagens(1000, 1, 'asc', 'mensagem_enviada_em', $_SESSION['usuario_id'], $arquivada);

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-envelope"></i> Minhas mensagens</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text">Esta área é destinada a comunicação entre os usuários do gabinete.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_enviar'])) {
                        $dados = [
                            'mensagem_titulo' => htmlspecialchars($_POST['mensagem_titulo'], ENT_QUOTES, 'UTF-8'),
                            'mensagem_texto' => htmlspecialchars($_POST['mensagem_texto'], ENT_QUOTES, 'UTF-8'),
                            'mensagem_status' => 0,
                            'mensagem_remetente' => $_SESSION['usuario_id'],
                            'mensagem_destinatario' => htmlspecialchars($_POST['mensagem_destinatario'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $mensagemController->novaMensagem($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-12 col-12">
                            <input type="text" class="form-control form-control-sm" name="mensagem_titulo" placeholder="Assunto" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="mensagem_destinatario" required>
                                <option>Selecione o destinatário</option>
                                <?php
                                $buscaUsuarios = $usuarioController->listarUsuarios(1000, 1, 'asc', 'usuario_nome', $_SESSION['usuario_gabinete']);
                                if ($buscaUsuarios['status'] == 'success') {
                                    foreach ($buscaUsuarios['dados'] as $usuario) {
                                        if ($usuario['usuario_id'] != $_SESSION['usuario_id']) {
                                            echo '<option value="' . $usuario['usuario_id'] . '">' . $usuario['usuario_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_enviar"><i class="bi bi-forward-fill"></i> Enviar</button>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="mensagem_texto" rows="10" placeholder="Texto da mensagem"></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-2" id="form_novo" method="GET" enctype="multipart/form-data">
                        <input type="hidden" name="secao" value="minhas-mensagens" />
                        <div class="col-md-1 col-12">
                            <select class="form-select form-select-sm" name="arquivada" required>
                                <option value="0" <?php echo $arquivada == 0 ? 'selected' : ''; ?>>Caixa de entrada</option>
                                <option value="1" <?php echo $arquivada == 1 ? 'selected' : ''; ?>>Lixeira</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm">OK</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Assunto</th>
                                    <th scope="col">Remetente</th>
                                    <th scope="col">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($buscaMensagens['status'] == 'success') {
                                    foreach ($buscaMensagens['dados'] as $mensagem) {
                                        echo '<tr>';
                                        echo '<td><a href="?secao=mensagem&id=' . $mensagem['mensagem_id'] . '">' . ($mensagem['mensagem_status'] == 0 ? '<b>' . $mensagem['mensagem_titulo'] . '</b>' : $mensagem['mensagem_titulo']) . '</a></td>';
                                        echo '<td>' . $mensagem['usuario_nome'] . '</td>';
                                        echo '<td>' . date('d/m H:i', strtotime($mensagem['mensagem_enviada_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaMensagens['status'] == 'not_found' || $buscaMensagens['status'] == 'error') {
                                    echo '<tr><td colspan="3">' . $buscaMensagens['message'] . '</td></>';
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