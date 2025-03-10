<?php

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


$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $buscaUsuario['dados']['usuario_gabinete']);
$buscaMensagens = $mensagemController->listarMensagens(1000, 1, 'asc', 'mensagem_enviada_em', $_SESSION['usuario_id'], 0);
$utils = new Utils();

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
            <div class="card mb-2">
                <div class="card-body card_descricao_body">
                    <h6 class="card-title mb-2">Aniversariantes do dia</h6>
                    <div class="list-group">
                        <?php
                        $buscaPessoas = $pessoaController->buscarAniversarianteMes(date('m'), null, $_SESSION['usuario_gabinete']);

                        if ($buscaPessoas['status'] == 'success') {
                            usort($buscaPessoas['dados'], function ($a, $b) {
                                return strcmp($a['pessoa_nome'], $b['pessoa_nome']);
                            });

                            foreach ($buscaPessoas['dados'] as $pessoa) {
                                if (date('d') == date('d', strtotime($pessoa['pessoa_aniversario']))) {
                                    if ($pessoa['pessoa_id'] != $_SESSION['usuario_id']) {
                                        echo '<a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '" style="font-size: 0.9em" class="list-group-item list-group-item-action d-flex align-items-center">';
                                        echo '<img src="' . (!empty($pessoa['pessoa_foto']) ? $pessoa['pessoa_foto'] : 'public/img/not_found.jpg') . '" alt="Foto de ' . htmlspecialchars($pessoa['pessoa_nome'], ENT_QUOTES, 'UTF-8') . '" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">';
                                        echo '<div>';
                                        echo '<h5 class="mb-1" style="font-size: 1.2em; font-weight: 600">' . htmlspecialchars($pessoa['pessoa_nome'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                        echo '<p class="mb-1" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all;">' . htmlspecialchars($pessoa['pessoa_email'], ENT_QUOTES, 'UTF-8') . '</p>';
                                        echo '</div>';
                                        echo '</a>';
                                    }
                                }
                            }
                        } else {
                            echo '<p class="card-text mb-0 mt-1"><i class="bi bi-envelope"></i> Nenhum aniversáriante para hoje.</p>';
                        }
                        ?>

                    </div>
                </div>
            </div>
            <?php if ($_SESSION['usuario_tipo'] == 2 || $_SESSION['usuario_tipo'] == 3) { ?>
                <div class="card mb-2">
                    <div class="card-body card_descricao_body">
                        <h6 class="card-title mb-3">Postagens programadas para hoje</h6>
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
        </div>
    </div>
</div>