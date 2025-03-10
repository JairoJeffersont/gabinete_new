<?php 
ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$gabineteController = new GabineteController();
$usuarioController = new UsuarioController();

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $buscaUsuario['dados']['usuario_gabinete']);



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

            <div class="row g-3">
                <!-- Card com logo -->
                <div class="col-12 col-lg-6">
                    <div class="card  shadow-sm rounded" style="min-height: 260px;">
                        <div class="card-body text-center">
                            <img class="img-fluid mb-3" src="public/img/logo.png" width="150" />
                            <h4 class="card-title text-primary">Mandato Digital</h4>
                            <p class="card-text text-muted">Sistema de gestão política</p>
                        </div>
                    </div>
                </div>

                <!-- Informações do cliente -->
                <div class="col-12 col-lg-6">
                    <div class="card  shadow-sm rounded" style="min-height: 260px;">
                        <div class="card-body px-4 py-3">
                            <h4 class="card-title text-success">Informações</h4>
                            <p class="card-text mb-2"><strong>Gestor do sistema:</strong> <?php 
                            
                            if($buscaUsuario['dados']['usuario_gestor']){
                                echo $buscaUsuario['dados']['usuario_nome'];

                            }
                            
                            ?></p>
                            <p class="card-text mb-2"><strong>Político do gabinete:</strong> <?php echo $buscaGabinete['dados']['gabinete_nome'] ?> - <?php echo $buscaGabinete['dados']['gabinete_estado_autoridade'] ?></p>
                            <p class="card-text mb-5"><strong>Quantidade de licenças:</strong> <?php echo $buscaGabinete['dados']['gabinete_usuarios'] ?></p>
                            <p class="card-text mb-0"><i class="bi bi-check-circle-fill text-success"></i> <strong>Assinatura ativa</strong></p>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>