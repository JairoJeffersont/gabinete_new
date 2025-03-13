<?php 


require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\ProposicaoController;
use GabineteMvc\Middleware\Utils;

$a = new ProposicaoController();
$utils = new Utils();

print_r($a->buscarProposicoesDeputado('Dr. Fernando Máximo', 2023, 10, 1, 'PL'));