<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Middleware\Utils;

$usuarioController = new UsuarioController();
$gabineteController = new GabineteController();
$util = new Utils();

$configPath = dirname(__DIR__, 3) . '/src/Configs/config.php';
$config = require $configPath;

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$buscaTipoGabinete = $gabineteController->buscaTipoGabinete($buscaGabinete['dados']['gabinete_tipo']);

if ($buscaUsuario['status'] != 'success' || $buscaGabinete['status'] != 'success' || $buscaTipoGabinete['status'] != 'success') {
    header('Location:?secao=home');
}

?>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-person-gear"></i> Área do Usuário</div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text">Nesta área, você pode gerenciar seus dados pessoais e realizar a atualização de informações, incluindo a troca de senha.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-2">
            <div class="card-body card_descricao_body p-2">
                <h6 class="card-title mb-2"><?php echo $buscaGabinete['dados']['gabinete_nome'] ?> - <?php echo $buscaGabinete['dados']['gabinete_estado_autoridade'] ?></h6>
                <p class="card-text mb-0">Endereço: <?php echo $buscaGabinete['dados']['gabinete_endereco'] ?> - <?php echo $buscaGabinete['dados']['gabinete_municipio'] ?> <?php echo $buscaGabinete['dados']['gabinete_estado'] ?></p>
                <p class="card-text mb-0">Telefone: <?php echo $buscaGabinete['dados']['gabinete_telefone'] ?></p>
                <p class="card-text mb-0">E-mail: <?php echo $buscaGabinete['dados']['gabinete_email'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-2">
            <div class="card-body p-2">
                <p class="card-text mb-2">Meus dados: </p>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_usuario'])) {

                    $usuario = [
                        'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                        'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                        'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                        'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                        'usuario_aniversario' => $util->formatarAniversario(htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8')),// Data formatada
                        
                    ];

                    $result = $usuarioController->atualizarUsuario($usuario);

                    if ($result['status'] == 'success') {
                        echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        echo '
                        <script>
                             setTimeout(function() {
                                 window.location.href = "?secao=meu-gabinete";
                             }, 1000);
                         </script>';
                    } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                    } else if ($result['status'] == 'forbidden') {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                    } else if ($result['status'] == 'error') {
                        echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                    }
                }

                ?>

                <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                    <div class="col-md-3 col-12">
                        <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados']['usuario_nome'] ?>" required>
                    </div>
                    <div class="col-md-2 col-12">
                        <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados']['usuario_email'] ?>" required>
                    </div>
                    <div class="col-md-2 col-6">
                        <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" value="<?php echo $buscaUsuario['dados']['usuario_telefone'] ?>" maxlength="15" >
                    </div>
                    <div class="col-md-2 col-6">
                        <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo $buscaUsuario['dados']['usuario_aniversario'] ?  date('d/m', strtotime($buscaUsuario['dados']['usuario_aniversario'])) : '' ?>" >
                    </div>
                    <div class="col-md-3 col-12">
                        <button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar_usuario"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>