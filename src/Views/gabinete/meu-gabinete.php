<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

use GabineteMvc\Controllers\ClienteController;
use GabineteMvc\Controllers\UsuarioController;

require 'vendor/autoload.php';

$clienteController = new ClienteController();
$usuarioController = new UsuarioController();

$buscaCliente = $clienteController->buscaCliente($_SESSION['usuario_cliente']);
$buscaUsuario = $usuarioController->listarUsuarios($buscaCliente['dados']['cliente_id']);
$bucaTipo = $usuarioController->listarUsuariosTipos();

if ($buscaCliente['status'] != 'success' || $buscaUsuario['status'] != 'success' || $bucaTipo['status'] != 'success') {
    header('Location: ?secao=sair');
}

$totalAssinaturas = $buscaCliente['dados']['cliente_usuarios'];
$totalUsuarios = count($buscaUsuario['dados']);

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-house-door"></i> Meu gabinete</div>
                <div class="card-body card_descricao_body p-2">
                    <h5 class="card-title mb-2">Gabinete: <?php echo $buscaCliente['dados']['cliente_gabinete_nome'] . '/' . $buscaCliente['dados']['cliente_gabinete_estado'] ?></h5>                    
                    <p class="card-text mb-0">Licenças: <?php echo $buscaCliente['dados']['cliente_usuarios'] ?><?php echo $_SESSION['usuario_tipo'] == 2 ? '<a href="#"> | faça upgrade do plano</a>' : '' ?></p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-people"></i> Usuários</div>

                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        if ($_POST['usuario_senha'] !== $_POST['usuario_senha2']) {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">As senha não conferem</div>';
                        } elseif (strlen($_POST['usuario_senha']) < 6) {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">A senha tem menos de 6 caracteres</div>';
                        } else if ($totalUsuarios >= $totalAssinaturas) {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Não existem mais licenças disponíveis.</div>';
                        } else if ($_POST['usuario_tipo'] == 0) {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Escolha um tipo de usuário.</div>';
                        } else {

                            $usuario_aniversario = htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8');

                            $data = DateTime::createFromFormat('d/m', $usuario_aniversario);
                            $usuario_aniversario_formatado = $data ? $data->format('2000-m-d') : null;

                            $usuario = [
                                'usuario_cliente' => $buscaCliente['dados']['cliente_id'],
                                'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                                'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                                'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                                'usuario_aniversario' => $usuario_aniversario_formatado, // Data formatada
                                'usuario_ativo' => htmlspecialchars($_POST['usuario_ativo'], ENT_QUOTES, 'UTF-8'),
                                'usuario_tipo' => htmlspecialchars($_POST['usuario_tipo'], ENT_QUOTES, 'UTF-8'),
                                'usuario_senha' => htmlspecialchars($_POST['usuario_senha'], ENT_QUOTES, 'UTF-8')
                            ];

                            $result = $usuarioController->novoUsuario($usuario);

                            if ($result['status'] == 'success') {
                                $buscaUsuario = $usuarioController->listarUsuarios($buscaCliente['dados']['cliente_id']);
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'forbidden') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                            }
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="1" selected>Ativado</option>
                                <option value="0">Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="usuario_tipo" required>
                                <option value="0">Escolha o tipo de usuário</option>
                                <?php
                                foreach ($bucaTipo['dados'] as $tipo) {
                                    if ($tipo['usuario_tipo_id'] != 1) {
                                        echo '<option value="' . $tipo['usuario_tipo_id'] . '">' . $tipo['usuario_tipo_nome'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha" name="usuario_senha" placeholder="Senha" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha2" name="usuario_senha2" placeholder="Confirme a senha" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body card_descricao_body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Nível</th>
                                    <th scope="col">Ativo</th>
                                    <th scope="col">Criado</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if ($buscaUsuario['status'] == 'success') {
                                    foreach ($buscaUsuario['dados'] as $usuario) {                                        
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=usuario&id=' . $usuario['usuario_id'] . '">' . $usuario['usuario_nome'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $usuario['usuario_email'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $usuario['usuario_telefone'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $usuario['usuario_tipo'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . ($usuario['usuario_ativo'] ? 'Ativo' : 'Desativado') . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($usuario['usuario_criado_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaUsuario['status'] == 'empty') {
                                    echo '<tr><td colspan="6">' . $buscaUsuario['message'] . '</td></tr>';
                                } else if ($buscaUsuario['status'] == 'error') {
                                    echo '<tr><td colspan="6">' . $buscaUsuario['message'] . ' | Código do erro: ' . $buscaUsuario['error_id'] . '</td></tr>';
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