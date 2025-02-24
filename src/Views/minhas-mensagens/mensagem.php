<?php

use GabineteMvc\Controllers\MensagemController;
use GabineteMvc\Controllers\UsuarioController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$mensagemController = new MensagemController();
$usuarioController = new UsuarioController();

$idGet = $_GET['id'];

$buscaMensagem = $mensagemController->buscaMensagem('mensagem_id', $idGet);


if ($buscaMensagem['status'] != 'success') {
    header('Location: ?secao=minhas-mensagens');
}

$mensagemController->marcarLida($idGet);

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=minhas-mensagens" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-envelope"></i> Ler mensagens</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2"><i class="bi bi-person"></i> Mensagem enviada por: <?php echo $buscaMensagem['dados']['usuario_nome'] ?> às <?php echo date('d/m/Y H:i', strtotime($buscaMensagem['dados']['mensagem_enviada_em'])) ?></p>
                    <p class="card-text mb-3"><i class="bi bi-envelope"></i> <b><?php echo $buscaMensagem['dados']['mensagem_titulo'] ?></b></p>
                    <p class="card-text mb-3 border p-2 shadow-sm"><?php echo $buscaMensagem['dados']['mensagem_texto'] ?></p>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $mensagemController->apagarMensagem($idGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=minhas-mensagens');
                        }
                    }
                    ?>
                    <form method="POST" enctype="multipart/form-data">
                        <button class="btn btn-danger btn-sm custom-nav barra_navegacao" role="button" type="submit" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                    </form>


                </div>
            </div>


        </div>
    </div>
</div>