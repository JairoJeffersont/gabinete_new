<?php
ob_start();

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\PessoaController;

$pessoaController = new PessoaController();
$gabineteController = new GabineteController();

$pessoaGet = $_GET['id'];

$buscaPessoa = $pessoaController->buscaPessoa('pessoa_id', $pessoaGet);

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];

if ($buscaPessoa['status'] == 'not_found' || is_integer($pessoaGet) || $buscaPessoa['status'] == 'error') {
    header('Location: ?secao=pessoas');
}

?>

<style>
    body {
        background-image: url(public/img/print_bg.jpeg);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    @media print {

        @page {
            margin: 0;
            size: A4 portrait;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url(public/img/print_bg.jpeg);
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
    }
</style>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
            window.close();
        };
    };
</script>

<div class="container-fluid p-2">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" style="background: none; border: none;">
                <div class="card-body text-center" style="background: none;">
                    <img src="public/img/brasaooficialcolorido.png" class="img-fluid mb-2" style="width: 150px;" />
                    <h5 class="card-title mb-2">Gabinete <?php echo $buscaGab['dados']['gabinete_nome'] ?></h5>
                    <p class="card-text" style="font-size: 1.4em;">Ficha cadastral </p>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-3 mb-2 d-flex justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Informações Pessoais</h6>
                    <hr>
                    <p class="card-text mb-1">Nome: <?php echo $buscaPessoa['dados']['pessoa_nome'] ?></p>
                    <p class="card-text mb-1">Aniversário: <?php echo $buscaPessoa['dados']['pessoa_aniversario'] ?></p>
                    <p class="card-text mb-1">Profissão: <?php echo $buscaPessoa['dados']['pessoas_profissoes_nome'] ?></p>
                    <p class="card-text mb-1">Órgão/entidade: <?php echo $buscaPessoa['dados']['orgao_nome'] ?></p>
                    <p class="card-text mb-1">Cargo: <?php echo $buscaPessoa['dados']['pessoa_cargo'] ?></p>
                </div>
            </div>
        </div>
    </div>


    <div class="row d-flex mb-2 justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Informações de contato</h6>
                    <hr>
                    <p class="card-text mb-1">Telefone: <?php echo $buscaPessoa['dados']['pessoa_telefone'] ?></p>
                    <p class="card-text mb-1">Endereço: <?php echo $buscaPessoa['dados']['pessoa_endereco'] ?></p>
                    <p class="card-text mb-1">Bairro: <?php echo $buscaPessoa['dados']['pessoa_bairro'] ?></p>
                    <p class="card-text mb-1">Município: <?php echo $buscaPessoa['dados']['pessoa_municipio'] . '/' . $buscaPessoa['dados']['pessoa_estado'] ?></p>
                    <p class="card-text mb-1">CEP: <?php echo $buscaPessoa['dados']['pessoa_cep'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center mb-2">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Redes sociais</h6>
                    <hr>
                    <p class="card-text mb-1">Instagram: <?php echo $buscaPessoa['dados']['pessoa_instagram'] ?></p>
                    <p class="card-text mb-1">Facebook: <?php echo $buscaPessoa['dados']['pessoa_facebook'] ?></p>
                    <p class="card-text mb-1">X (Twitter): <?php echo $buscaPessoa['dados']['pessoa_x'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Informações adicionais</h6>
                    <hr>
                    <p class="card-text mb-1"> <?php echo $buscaPessoa['dados']['pessoa_informacoes'] == null ? 'Sem informações adicionais' : $buscaPessoa['dados']['pessoa_informacoes'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>



</div>