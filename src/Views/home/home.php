<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\MensagemController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Middleware\Utils;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$usuarioController = new UsuarioController();
$gabineteController = new GabineteController();
$mensagemController = new MensagemController();

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
                    <hr>

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
            
        </div>
    </div>
</div>