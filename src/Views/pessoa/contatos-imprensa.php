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

$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $_GET['estado'] : null;

$buscaOrgaos = $orgaoController->listarOrgaos(10000, 1, 'asc', 'orgao_nome', null, $estado, $_SESSION['usuario_gabinete']);

$orgaosImprensa = [];

if ($buscaOrgaos['status'] == 'success') {
    foreach ($buscaOrgaos['dados'] as $orgao) {
        if ($orgao['orgao_tipo'] == 19) {
            $orgaosImprensa[] = $orgao;
        }
    }
} else {
    $orgaosImprensa = [];
}

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-telephone"></i> Contatos da imprensa</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-0">Lista de contatos da imprensa</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-1">
                    <form class="row g-2 form_custom mb-0" method="post" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <a href="?secao=orgaos" type="button" class="btn btn-primary btn-sm"><i class="bi bi-newspaper"></i> Novo veículo</a>
                            <a href="?secao=pessoas" type="button" class="btn btn-success btn-sm"><i class="bi bi-person"></i> Nova pessoa</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm mb-2">
                    <div class="card-body p-2">
                        <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                            <input type="hidden" name="secao" value="contatos-imprensa" />

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

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="accordion" id="accordionPanelsStayOpenExample">

                        <?php

                        if (count($orgaosImprensa) > 0) {
                            foreach ($orgaosImprensa as $orgaoImprensa) {
                                echo ' <div class="accordion-item" style="font-size:0.9em"><h2 class="accordion-header">
                                                <button class="accordion-button collapsed"  style="font-size:0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $orgaoImprensa['orgao_id'] . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $orgaoImprensa['orgao_id'] . '">
                                                    ' . $orgaoImprensa['orgao_nome'] . '
                                                </button>
                                            </h2>
                                            <div id="panelsStayOpen-collapse' . $orgaoImprensa['orgao_id'] . '" class="accordion-collapse collapse">
                                                <div class="accordion-body">';

                                $buscaPessoas = $pessoaController->listarPessoas(1000, 1, 'asc', 'pessoa_nome', null, null, $_SESSION['usuario_gabinete']);

                                if ($buscaPessoas['status'] == 'success') {
                                    $contatoEncontrado = false;
                                    foreach ($buscaPessoas['dados'] as $pessoa) {
                                        if ($pessoa['pessoa_orgao'] == $orgaoImprensa['orgao_id']) {
                                            echo '<p class="card-text mb-1"><a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '"><i class="bi bi-person"></i> ' . $pessoa['pessoa_nome'] . '</a></p>';
                                            echo '<p class="card-text mb-1"><i class="bi bi-envelope"></i> ' . $pessoa['pessoa_email'] . '</p>';
                                            echo '<p class="card-text mb-3"><i class="bi bi-telephone"></i> ' . $pessoa['pessoa_telefone'] . '</p>';
                                            $contatoEncontrado = true;
                                        }
                                    }

                                    if (!$contatoEncontrado) {
                                        echo '<p class="card-text mb-0"><i class="bi bi-person"></i> Não existe um contato para esse veículo. <a href="?secao=pessoas">Clique para adicionar</a></p>';
                                    }
                                } else {
                                    echo '<p class="card-text mb-0"><i class="bi bi-person"></i> Não existe um contato para esse veículo. <a href="?secao=pessoas">Clique para adicionar</a></p>';
                                }




                                echo '</div>
                                            </div>
                                            </div>';
                            }
                        } else {
                            echo '<p class="card-text mb-0" style="font-size:0.9em"><i class="bi bi-person"></i> Não existem veículos de imprensa. <a href="?secao=orgaos">Clique para adicionar</a></p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>