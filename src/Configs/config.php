<?php
return [

    'database' => [
        'host' => 'localhost',
        'name' => 'gabinete_digital_mvc',
        'user' => 'root',
        'password' => 'root',
    ],

    'app' => [
        'base_url' => rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/', ''),
        'legislatura_atual' => 57
    ],

    'email' => [
        'smtp_host' => 'smtp.kinghost.net',
        'smtp_port' => 587,
        'smtp_user' => 'contato@politikaassessoria.com.br',
        'smtp_password' => 'Intell@3103',
        'smtp_sender' => 'contato@jscloud.com.br',
        'smtp_from' => 'contato@jscloud.com.br',
        'smtp_from_name' => 'Gabinete Digital'
    ]
];