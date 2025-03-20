<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\PessoaController;
use GabineteMvc\Middleware\GetJson;
use GabineteMvc\Middleware\Utils;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$pessoaController = new PessoaController();
$gabineteController = new GabineteController();
$getJson = new GetJson();

$busca = $pessoaController->listarProfissoesPessoa($_SESSION['usuario_gabinete']);
$utils = new Utils();

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];
$estado = (isset($_GET['estado']) && $_GET['estado'] === 'null') ?  null : $estadoDep;


$mes = $_GET['mes'] ?? date('m');

$configPath = dirname(__DIR__, 3) . '/src/Configs/config.php';
$config = require $configPath;

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-people-fill"></i> Aniversariantes do Mês</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível visualizar os aniversariantes do mês, garantindo a correta gestão e acompanhamento dessas informações no sistema.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm mb-2">
                    <div class="card-body p-2">
                        <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                            <div class="col-md-2 col-4">
                                <input type="hidden" name="secao" value="aniversariantes" />
                                <select class="form-select form-select-sm" name="mes" required>
                                    <option value="">Selecione um mês</option>
                                    <?php

                                    $meses = [
                                        1 => 'Janeiro',
                                        2 => 'Fevereiro',
                                        3 => 'Março',
                                        4 => 'Abril',
                                        5 => 'Maio',
                                        6 => 'Junho',
                                        7 => 'Julho',
                                        8 => 'Agosto',
                                        9 => 'Setembro',
                                        10 => 'Outubro',
                                        11 => 'Novembro',
                                        12 => 'Dezembro'
                                    ];

                                    foreach ($meses as $numero => $nome) {
                                        if ($mes == $numero) {
                                            echo "<option value=\"$numero\" selected>$nome</option>";
                                        } else {
                                            echo "<option value=\"$numero\">$nome</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <select class="form-select form-select-sm" name="estado" required>
                                    <option value="null" <?php echo $estado === null ? 'selected' : ''; ?>>Todos os estados</option>
                                    <option value="<?php echo $estadoDep ?>" <?php echo $estado === $estadoDep ? 'selected' : ''; ?>>Somente <?php echo $estadoDep ?></option>
                                </select>
                            </div>

                            <div class="col-md-1 col-2">
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mb-2 card_descricao_body">
                <div class="card-body p-2">
                    <div class="list-group">

                        <?php
                        $buscaMes = $pessoaController->buscarAniversarianteMes($mes, $estado, $_SESSION['usuario_gabinete']);
                        if ($buscaMes['status'] == 'success') {
                            $grupos = [];
                            foreach ($buscaMes['dados'] as $aniversariante) {
                                $foto = isset($aniversariante['pessoa_foto']) && file_exists($aniversariante['pessoa_foto']) ? $aniversariante['pessoa_foto'] : 'public/img/not_found.jpg';
                                $dia = date('d/m', strtotime($aniversariante['pessoa_aniversario']));
                                $grupos[$dia][] = [
                                    'nome' => $aniversariante['pessoa_nome'],
                                    'id' => $aniversariante['pessoa_id'],
                                    'email' => $aniversariante['pessoa_email'],
                                    'foto' => $foto,
                                ];
                            }
                        ?>

                            <div class="accordion" id="accordionAniversariantes">
                                <?php foreach ($grupos as $dia => $aniversariantesDoDia): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $dia ?>">
                                            <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $dia ?>" aria-expanded="false" aria-controls="collapse<?= $dia ?>">
                                                Dia <?= date('d/m') == $dia ? $dia . ' | <b>&nbsp;Hoje</b>' : $dia ?>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $dia ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $dia ?>" data-bs-parent="#accordionAniversariantes">
                                            <div class="accordion-body p-3">
                                                <?php foreach ($aniversariantesDoDia as $aniversariante): ?>
                                                    <a href="?secao=pessoa&id=<?= $aniversariante['id'] ?>" class="shadow-sm list-group-item list-group-item-action d-flex align-items-center">
                                                        <img src="<?= $aniversariante['foto'] ?>" alt="Foto de <?= $aniversariante['nome'] ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <div>
                                                            <h5 class="mb-1" style="font-size: 1.2em; font-weight: 600"><?= $aniversariante['nome'] ?></h5>
                                                            <p class="mb-1" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all;"><?= $aniversariante['email'] ?></p>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php
                        } else if ($buscaMes['status'] == 'not_found') {
                            echo '<div class="list-group-item list-group-item-action d-flex align-items-center">                                       
                                        <div>
                                            <h5 class="mb-0" style="font-size: 1em;">Nenhum aniversariante neste mês</h5>
                                        </div>
                                </div>';
                        } else if ($buscaMes['status'] == 'error') {
                            echo '<div class="list-group-item list-group-item-action d-flex align-items-center">                                       
                                        <div>
                                            <h5 class="mb-0" style="font-size: 1em;">' . $buscaMes['message'] . ' | Código do erro: ' . $buscaMes['error_id'] . '</h5>
                                        </div>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_body">
                    <i class="bi bi-people-fill"></i> Deputados aniversariantes do mês
                </div>
                <div class="card-body p-2">
                    <?php
                    $deputados = $getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/arquivos/deputados/json/deputados.json');
                    $aniversariantes = [];

                    foreach ($deputados['dados'] as $deputado) {
                        if ($deputado['idLegislaturaFinal'] == $config['app']['legislatura_atual'] && date('m', strtotime($deputado['dataNascimento'])) == date('m')) {

                            if ($estado == null) {
                                $dia = date('d/m', strtotime($deputado['dataNascimento']));
                                $aniversariantes[$dia][] = [
                                    'id' => basename($deputado['uri']),
                                    'nome' => $deputado['nome']
                                ];
                            } else {
                                if ($estadoDep == $deputado['ufNascimento']) {
                                    $dia = date('d/m', strtotime($deputado['dataNascimento']));
                                    $aniversariantes[$dia][] = [
                                        'id' => basename($deputado['uri']),
                                        'nome' => $deputado['nome']
                                    ];
                                }
                            }
                        }
                    }

                    ksort($aniversariantes); // Ordena por data
                    $diaAtual = date('d/m');
                    ?>

                    <table class="table table-striped table-bordered custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Deputados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aniversariantes as $dia => $deputados): ?>
                                <tr>
                                    <td><?= $dia ?><?= ($dia == $diaAtual) ? ' | <b>Hoje</b>' : ''; ?></td>
                                    <td>
                                        <?php foreach ($deputados as $index => $aniversariante): ?>
                                            <a href="https://www.camara.leg.br/deputados/<?= $aniversariante['id'] ?>" target="_blank">
                                                <?= $aniversariante['nome'] ?>
                                            </a><?= $index < count($deputados) - 1 ? '<br> ' : '' ?>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>