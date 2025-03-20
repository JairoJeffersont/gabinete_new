<?php

use GabineteMvc\Controllers\AgendaController;
use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\MensagemController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Controllers\PessoaController;
use GabineteMvc\Controllers\PostagemController;
use GabineteMvc\Middleware\Utils;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$usuarioController = new UsuarioController();
$gabineteController = new GabineteController();
$mensagemController = new MensagemController();
$pessoaController = new PessoaController();
$postagemController = new PostagemController();
$agendaController = new AgendaController();

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $buscaUsuario['dados']['usuario_gabinete']);
$buscaMensagens = $mensagemController->listarMensagens(1000, 1, 'asc', 'mensagem_enviada_em', $_SESSION['usuario_id'], 0);
$utils = new Utils();

$dataGet = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$tipoGet = (isset($_GET['tipo']) && $_GET['tipo'] !== 'null') ? $_GET['tipo'] : null;
$situacaoGet = (isset($_GET['situacao']) && $_GET['situacao'] !== 'null') ? $_GET['situacao'] : null;

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body card_descricao_body">
                    <h6 class="card-title mb-2">Seja bem-vindo: <?php echo $buscaUsuario['dados']['usuario_nome']; ?>! </h6>
                    <hr>
                    <?php
                    if ((empty($buscaGabinete['dados']['gabinete_endereco']) || empty($buscaGabinete['dados']['gabinete_municipio']) || empty($buscaGabinete['dados']['gabinete_email']) || empty($buscaGabinete['dados']['gabinete_telefone']) || empty($buscaGabinete['dados']['gabinete_estado'])) && $buscaUsuario['dados']['usuario_gestor']) {
                        echo '<p class="card-text mb-0"><i class="bi bi-exclamation-triangle"></i> Atualize os dados do seu gabinete. | <a href="?secao=meu-gabinete">clique aqui</a></p>';
                    }

                    if (empty($buscaUsuario['dados']['usuario_aniversario'])) {
                        echo '<p class="card-text mb-0 mt-1"><i class="bi bi-exclamation-triangle"></i> Atualize seus dados pessoais. | <a href="?secao=meu-gabinete">clique aqui</a></p>';
                    } else {
                        $aniversario = $utils->calculaAniversario($buscaUsuario['dados']['usuario_aniversario']);
                        echo '<p class="card-text mb-0 mt-1">' . $aniversario . '</p>';
                    }
                    ?>


                    <?php
                    if ($buscaMensagens['status'] == 'success') {
                        $mensagensNaoLidas = count(array_filter($buscaMensagens['dados'], function ($mensagem) {
                            return isset($mensagem['mensagem_status']) && $mensagem['mensagem_status'] === 0;
                        }));

                        if ($mensagensNaoLidas > 0) {
                            echo '<p class="card-text mb-0 mt-1"><i class="bi bi-envelope"></i> <a href="?secao=minhas-mensagens"><b>Você tem ' . $mensagensNaoLidas . ' novas mensagens</b></a></p>';
                        } else {
                            echo '<p class="card-text mb-0 mt-1"><i class="bi bi-envelope"></i> Você não tem novas mensagens.</p>';
                        }
                    } else if ($buscaMensagens['status'] == 'not_found') {
                        echo '<p class="card-text mb-0 mt-1"><i class="bi bi-envelope"></i> Você não tem novas mensagens.</p>';
                    }

                    ?>
                </div>
            </div>
           
            <?php if ($_SESSION['usuario_tipo'] == 2 || $_SESSION['usuario_tipo'] == 3) { ?>
                <div class="card mb-2">
                    <div class="card-body card_descricao_body">
                        <h6 class="card-title mb-3">Postagens de hoje</h6>
                        <div class="list-group">
                            <?php
                            $buscaPostagens = $postagemController->listarPostagens(1000, 1, 'asc', 'postagem_titulo', 'all', 2025, $_SESSION['usuario_gabinete']);
                            if ($buscaPostagens['status'] == 'success') {
                                foreach ($buscaPostagens['dados'] as $postagem) {
                                    if (date('d/m') == date('d/m', strtotime($postagem['postagem_data']))) {
                                        echo '<a href="?secao=postagem&id=' . $postagem['postagem_id'] . '" class="list-group-item list-group-item-action">' . date('d/m', strtotime($postagem['postagem_data'])) . ' - ' . $postagem['postagem_titulo'] . '</a>';
                                    }
                                }
                            } else {
                                echo '<li class="list-group-item">Nenhuma postagem encontrada</li>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="card mb-2">
                <div class="card-body card_descricao_body">
                    <h6 class="card-title mb-3">Compromissos de hoje </h6>
                    <div class="list-group">
                        <?php
                        $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_gabinete']);

                        if ($buscaAgendas['status'] == 'success') {
                            foreach ($buscaAgendas['dados'] as $agenda) {
                                echo '<a href="?secao=agendas" class="list-group-item list-group-item-action">' . date('d/m', strtotime($agenda['agenda_data'])) . ' - ' . $agenda['agenda_titulo'] . '</a>';
                            }
                        } else {
                            echo '<li class="list-group-item">Nenhuma agenda para o dia <b>' . date('d/m', strtotime($dataGet)) . '</li>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>