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
    @media print {

        body {
            background-color: rgb(255, 255, 255);
        }

        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            background-color: rgb(255, 255, 255);
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

    <div class="row">
        <div class="col-12 text-center mb-1">
            <h5>Gabinete <?php echo $buscaGab['dados']['gabinete_nome'] ?></h5>
        </div>
        <div class="col-12 text-center mb-5">
            <h6>Ficha cadastral</h6>
        </div>
        <div class="col-12 p-2 mb-3">
            <h6 class="card-title">Informações Pessoais</h6>
            <hr>
            <p class="card-text mb-1">Nome: <?php echo $buscaPessoa['dados']['pessoa_nome'] ?></p>
            <p class="card-text mb-1">Aniversário: <?php echo date('d/m', strtotime($buscaPessoa['dados']['pessoa_aniversario'])) ?></p>
            <p class="card-text mb-1">Profissão: <?php echo $buscaPessoa['dados']['pessoas_profissoes_nome'] ?></p>
            <p class="card-text mb-1">Órgão/entidade: <?php echo $buscaPessoa['dados']['orgao_nome'] ?></p>
            <p class="card-text mb-1">Cargo: <?php echo !empty($buscaPessoa['dados']['pessoa_cargo']) ? $buscaPessoa['dados']['pessoa_cargo'] : 'Não informado' ?></p>
            <p class="card-text mb-1">Sexo: <?php echo !empty($buscaPessoa['dados']['pessoa_sexo']) ? $buscaPessoa['dados']['pessoa_sexo'] : 'Não informado' ?></p>
            <p class="card-text mb-1">Tipo: <?php echo !empty($buscaPessoa['dados']['pessoa_tipo_nome']) ? $buscaPessoa['dados']['pessoa_tipo_nome'] : 'Não informado' ?></p>
        </div>
        <div class="col-12 p-2">
            <h6 class="card-title">Informações de contato</h6>
            <hr>
            <p class="card-text mb-1">Telefone: <?php echo !empty($buscaPessoa['dados']['pessoa_telefone']) ? $buscaPessoa['dados']['pessoa_telefone'] : 'Não informado' ?></p>
            <p class="card-text mb-1">Endereço: <?php echo !empty($buscaPessoa['dados']['pessoa_endereco']) ? $buscaPessoa['dados']['pessoa_endereco'] : 'Não informado' ?></p>
            <p class="card-text mb-1">Bairro: <?php echo !empty($buscaPessoa['dados']['pessoa_bairro']) ? $buscaPessoa['dados']['pessoa_bairro'] : 'Não informado' ?>
            <p class="card-text mb-1">Município: <?php echo !empty($buscaPessoa['dados']['pessoa_municipio']) ? $buscaPessoa['dados']['pessoa_municipio'] : 'Não informado' ?></p>
            <p class="card-text mb-1">CEP: <?php echo !empty($buscaPessoa['dados']['pessoa_cep']) ? $buscaPessoa['dados']['pessoa_cep'] : 'Não informado' ?></p>
            </p>
        </div>
        <div class="col-12 p-2">
            <h6 class="card-title">Redes sociais</h6>
            <hr>
            <p class="card-text mb-1">Instagram: <?php echo !empty($buscaPessoa['dados']['pessoa_instagram']) ? $buscaPessoa['dados']['pessoa_instagram'] : 'Não informado' ?></p>
            <p class="card-text mb-1">Facebook: <?php echo !empty($buscaPessoa['dados']['pessoa_facebook']) ? $buscaPessoa['dados']['pessoa_facebook'] : 'Não informado' ?>
            <p class="card-text mb-1">X (Twitter): <?php echo !empty($buscaPessoa['dados']['pessoa_x']) ? $buscaPessoa['dados']['pessoa_x'] : 'Não informado' ?></p>
        </div>
        <div class="col-12 p-2">
            <h6 class="card-title">Informações adicionais</h6>
            <hr>
            <p class="card-text mb-1"> <?php echo $buscaPessoa['dados']['pessoa_informacoes'] == null ? 'Sem informações adicionais' : $buscaPessoa['dados']['pessoa_informacoes'] ?></p>
        </div>