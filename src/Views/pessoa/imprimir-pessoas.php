<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\OrgaoController;
use GabineteMvc\Controllers\PessoaController;
use GabineteMvc\Middleware\Utils;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$pessoaController = new PessoaController();
$orgaoController = new OrgaoController();
$gabineteController = new GabineteController();

$busca = $pessoaController->listarProfissoesPessoa($_SESSION['usuario_gabinete']);
$utils = new Utils();

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 1000000;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['pessoa_nome', 'pessoa_estado', 'pessoa_municipio', 'pessoa_tipo_nome', 'pessoa_criada_por']) ? htmlspecialchars($_GET['ordenarPor']) : 'pessoa_nome';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';
$termo = isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : null;
$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $_GET['estado'] : null;

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
    body {
        background-image: url(public/img/print_bg.jpeg);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    @media print {
        @page {
            margin: 10mm;
            size: A4 landscape;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: none;
            background-color: white;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        header,
        footer {
            display: none !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tbody tr {
            page-break-inside: avoid;
        }
    }
</style>
<h6 class="text-center mb-4">Lista de pessoas <?php echo $estado ? ' - ' . $estado : ' - Todos os estados' ?></h6>
<table class="table table-hover table-bordered table-striped mb-0 custom-table">
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Telefone</th>
            <th scope="col">Endereço</th>
            <th scope="col">UF/Município</th>
            <th scope="col">Órgão</th>
            <th scope="col">Tipo</th>
            <th scope="col">Profissão</th>
        </tr>
    </thead>
    <tbody>
        <?php

        $busca = $pessoaController->listarPessoas($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $_SESSION['usuario_gabinete']);

        if ($busca['status'] == 'success') {
            $total_de_registros = count($busca['dados']);
            foreach ($busca['dados'] as $pessoa) {
                echo '<tr>';
                echo '<td><a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '">' . $pessoa['pessoa_nome'] . '</a></td>';
                echo '<td>' . $pessoa['pessoa_email'] . '</td>';
                echo '<td>' . $pessoa['pessoa_telefone'] . '</td>';
                echo '<td>' . $pessoa['pessoa_endereco'] . '</td>';
                echo '<td>' . $pessoa['pessoa_municipio'] . '/' . $pessoa['pessoa_estado'] . '</td>';
                echo '<td>' . $pessoa['orgao_nome'] . '</td>';
                echo '<td>' . $pessoa['pessoa_tipo_nome'] . '</td>';
                echo '<td>' . $pessoa['pessoas_profissoes_nome'] . '</td>';
                echo '</tr>';
            }
        } else if ($busca['status'] == 'not_found') {
            echo '<tr><td colspan="11">' . $busca['message'] . '</td></tr>';
        } else if ($busca['status'] == 'error') {
            echo '<tr><td colspan="11">' . $busca['message'] . ' | Código do erro: ' . $busca['id_erro'] . '</td></tr>';
        }
        ?>
    </tbody>
</table>