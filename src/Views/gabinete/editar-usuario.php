<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

use GabineteMvc\Controllers\ClienteController;
use GabineteMvc\Controllers\UsuarioController;

require 'vendor/autoload.php';

$clienteController = new ClienteController();
$usuarioController = new UsuarioController();

$usuarioGet = $_GET['id'];

$buscaCliente = $clienteController->buscaCliente($_SESSION['usuario_cliente']);
$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $usuarioGet);
$bucaTipo = $usuarioController->listarUsuariosTipos();

if ($buscaUsuario['status'] == 'not_found' || $buscaUsuario['status'] == 'error') {
    header('Location: ?secao=meu-gabinete');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=meu-gabinete" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-people"></i> Editar usuários</div>

                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $usuario_aniversario = htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8');

                        $data = DateTime::createFromFormat('d/m', $usuario_aniversario);
                        $usuario_aniversario_formatado = $data ? $data->format('2000-m-d') : null;

                        $usuario = [
                            'usuario_id' => $usuarioGet,
                            'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                            'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                            'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                            'usuario_aniversario' => $usuario_aniversario_formatado, // Data formatada
                            'usuario_ativo' => htmlspecialchars($_POST['usuario_ativo'], ENT_QUOTES, 'UTF-8'),
                            'usuario_tipo' => htmlspecialchars($_POST['usuario_tipo'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $usuarioController->atualizarUsuario($usuario);

                        if ($result['status'] == 'success') {
                            $buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $usuarioGet);
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'forbidden') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $usuarioController->apagarUsuario($usuarioGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=meu-gabinete');
                            exit;
                        } else if ($result['status'] == 'forbidden' || $result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['v']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }


                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados']['usuario_nome'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados']['usuario_email'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="11" value="<?php echo $buscaUsuario['dados']['usuario_telefone'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo isset($buscaUsuario['dados']['usuario_aniversario']) ? date('d/m', strtotime($buscaUsuario['dados']['usuario_aniversario'])) : '' ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="1" <?= $buscaUsuario['dados']['usuario_ativo'] == 1 ? 'selected' : '' ?>>Ativado</option>
                                <option value="0" <?= $buscaUsuario['dados']['usuario_ativo'] == 0 ? 'selected' : '' ?>>Desativado</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="usuario_tipo" required>
                                <option value="0">Escolha o tipo de usuário</option>
                                <?php
                                foreach ($bucaTipo['dados'] as $tipo) {
                                    if ($tipo['usuario_tipo_id'] != 1) {
                                        if ($tipo['usuario_tipo_id'] == $buscaUsuario['dados']['usuario_tipo']) {
                                            echo '<option value="' . $tipo['usuario_tipo_id'] . '" selected>' . $tipo['usuario_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['usuario_tipo_id'] . '">' . $tipo['usuario_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>