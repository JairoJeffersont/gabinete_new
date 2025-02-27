<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\EmendaController;
use GabineteMvc\Controllers\OrgaoController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$emendaController = new EmendaController();
$orgaosController = new OrgaoController();
$gabineteController = new GabineteController();


$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['emenda_numero', 'emenda_valor', 'emenda_municipio', 'emendas_status_nome']) ? htmlspecialchars($_GET['ordenarPor']) : 'emenda_numero';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';
$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 100000;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$anoGet = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$statusGet = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 0;
$objetivoGet = isset($_GET['objetivo']) ? htmlspecialchars($_GET['objetivo']) : 0;
$tipoGet = isset($_GET['tipo']) ? (int) $_GET['tipo'] : 1;

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estado = $buscaGab['dados']['gabinete_estado_autoridade'];

$estadoGet = isset($_GET['estado']) ? htmlspecialchars($_GET['estado']) : $estado;
$municipioGet = isset($_GET['municipio']) ? htmlspecialchars($_GET['municipio']) : null;

?>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
            window.close();
        };
    };
</script>

<style>
    @media print {

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }

        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }

        .custom-table-print {
            font-size: 0.7em;
        }


    }
</style>
<h6 class="text-center mb-4">Emendas do ano <?php echo $anoGet ?></h6>
<table class="table table-hover table-bordered table-striped mb-0 custom-table">
    <thead>
        <tr>
            <th scope="col">Número</th>
            <th scope="col">Valor</th>
            <th scope="col">Descrição</th>
            <th scope="col">Objetivo</th>
            <th scope="col">Status</th>
            <th scope="col">Órgão</th>
            <th scope="col">Município</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $emendas = $emendaController->listarEmendas($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $_SESSION['usuario_gabinete']);
        if ($emendas['status'] == 'success') {
            foreach ($emendas['dados'] as $emenda) {
                echo '<tr>';
                echo '<td style="white-space: nowrap;"><a href="?secao=emenda&id=' . $emenda['emenda_id'] . '">' . $emenda['emenda_numero'] . '</a></td>';
                echo '<td style="white-space: nowrap;">R$ ' . number_format($emenda['emenda_valor'], 2, ',', '.') . '</td>';
                echo '<td>' . $emenda['emenda_descricao'] . '</td>';
                echo '<td >' . $emenda['emendas_objetivos_nome'] . '</td>';
                echo '<td >' . $emenda['emendas_status_nome'] . '</td>';
                echo '<td >' . $emenda['orgao_nome'] . '</td>';
                echo '<td >' . $emenda['emenda_municipio'] . ' | ' . $emenda['emenda_estado'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">' . $emendas['message'] . '</td></tr>';
        }
        ?>
    </tbody>
</table>