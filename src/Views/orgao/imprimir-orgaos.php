<?php

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\OrgaoController;

$orgaoController = new OrgaoController();
$gabineteController = new GabineteController();

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10000000;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['orgao_nome', 'orgao_estado', 'orgao_municipio', 'orgao_tipo_nome', 'orgao_criado_por', 'orgao_criado_em']) ? htmlspecialchars($_GET['ordenarPor']) : 'orgao_nome';
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
<h6 class="text-center mb-4">Lista de órgãos e entidades <?php echo $estado ? ' - ' . $estado : ' - Todos os estados' ?></h6>
<table class="table table-hover table-bordered table-striped mb-0 custom-table">
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Telefone</th>
            <th scope="col">Endereço</th>
            <th scope="col">UF/Município</th>
            <th scope="col">Tipo</th>
            <th scope="col">Criado em | por</th>
        </tr>
    </thead>
    <tbody>
        <?php

        $busca = $orgaoController->listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $_SESSION['usuario_gabinete']);

        if ($busca['status'] == 'success') {
            $total_de_registros = count($busca['dados']);
            foreach ($busca['dados'] as $orgao) {
                if ($orgao['orgao_id'] <> 1) {
                    echo '<tr>';
                    echo '<td style="white-space: nowrap;"><a href="?secao=orgao&id=' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</a></td>';
                    echo '<td style="white-space: nowrap;">' . $orgao['orgao_email'] . '</td>';
                    echo '<td style="white-space: nowrap;">' . $orgao['orgao_telefone'] . '</td>';
                    echo '<td style="white-space: nowrap;">' . $orgao['orgao_endereco'] . '</td>';
                    echo '<td style="white-space: nowrap;">' . $orgao['orgao_municipio'] . '/' . $orgao['orgao_estado'] . '</td>';
                    echo '<td style="white-space: nowrap;">' . $orgao['orgao_tipo_nome'] . '</td>';
                    echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($orgao['orgao_criado_em'])) . ' | ' . $orgao['usuario_nome'] . '</td>';
                    echo '</tr>';
                }
            }
        } else if ($busca['status'] == 'not_found') {
            echo '<tr><td colspan="7">' . $busca['message'] . '</td></tr>';
        } else if ($busca['status'] == 'error') {
            echo '<tr><td colspan="7">Erro ao carregar os dados. ' . (isset($busca['error_id']) ? ' | Código do erro: ' . $busca['error_id'] : '') . '</td></tr>';
        }
        ?>
    </tbody>
</table>