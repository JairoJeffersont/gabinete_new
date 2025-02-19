<?php

use GabineteMvc\Controllers\LoginController;

require_once './vendor/autoload.php';


$controller = new LoginController();

print_r($controller->logar('USUARIO@SISTEMA.COM', '123456789'));

