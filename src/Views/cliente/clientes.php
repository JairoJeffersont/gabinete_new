<?php

use GabineteMvc\Controllers\ClienteController;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$clienteController = new ClienteController();

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-newspaper"></i> Clientes</div>
                <div class="card-body p-2 card_descricao_body">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os clientes no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
        </div>
    </div>
</div>