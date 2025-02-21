<?php

use GabineteMvc\Controllers\MensagemController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$mensagemController = new MensagemController();

$buscaMensagens = $mensagemController->listarMensagens(10, 1, 'desc', 'mensagem_enviada_em', $_SESSION['usuario_id']);

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
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Assunto</th>

                                    <th scope="col">Remetente</th>
                                    <th scope="col">Recebida em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if ($buscaMensagens['status'] == 'success') {
                                    foreach ($buscaMensagens['dados'] as $mensagem) {
                                        echo '<tr>';
                                        echo '<td>' . $mensagem['mensagem_titulo'] . '</td>';
                                        echo '<td>' . $mensagem['usuario_nome'] . '</td>';
                                        echo '<td>' . $mensagem['mensagem_enviada_em'] . '</td>';
                                        echo '</tr>';
                                    }
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