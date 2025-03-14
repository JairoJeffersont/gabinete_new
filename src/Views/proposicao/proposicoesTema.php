<?php

use GabineteMvc\Controllers\ProposicaoController;

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';


$proposicaoTemaController = new ProposicaoController();

$busca = $proposicaoTemaController->listarProposicoesTemas($_SESSION['usuario_gabinete']);

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-people-fill"></i> Adicionar Proposição de Tema</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar as proposições de tema, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'proposicao_tema_nome' => htmlspecialchars($_POST['proposicao_tema_nome'], ENT_QUOTES, 'UTF-8'),
                            'proposicao_tema_criado_por' => $_SESSION['usuario_id'],
                            'proposicao_tema_gabinete' => $_SESSION['usuario_gabinete'],
                        ];

                        if (isset($_POST['proposicao_tema_descricao'])) {
                            $dados['proposicao_tema_descricao'] = htmlspecialchars($_POST['proposicao_tema_descricao'], ENT_QUOTES, 'UTF-8');
                        }

                        $result = $proposicaoTemaController->criarProposicaoTema($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $busca = $proposicaoTemaController->listarProposicoesTemas($_SESSION['usuario_gabinete']);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="proposicao_tema_nome" placeholder="Nome do Tema" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $proposicaoTema) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=tema-proposicao&id=' . $proposicaoTema['proposicao_tema_id'] . '">' . $proposicaoTema['proposicao_tema_nome'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $proposicaoTema['usuario_nome'] . ' - ' . date('d/m', strtotime($proposicaoTema['proposicao_tema_criado_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="4">Erro ao carregar os dados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>