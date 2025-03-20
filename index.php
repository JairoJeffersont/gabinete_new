<?php
if (!ob_start("ob_gzhandler")) {
    ob_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandato Digital</title>
    <link href="public/vendor/startbootstrap-simple-sidebar-master/dist/css/styles.css" rel="stylesheet">
    <link href="public/vendor/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/custom.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="57x57" href="public/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="public/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="public/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="public/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="public/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="public/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="public/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="public/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/icons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="public/icons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="public/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/icons/favicon-16x16.png">
    <link rel="manifest" href="public/icons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:url" content="https://jscloud.com.br/gabinete/?secao=login">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Mandato Digital">
    <meta property="og:description" content="Sistema de gestão de gabinete político">
    <meta property="og:image" content="https://jscloud.com.br/gabinete/public/img/logo.png"><!-- Load error, please check URL -->

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="jscloud.com.br">
    <meta property="twitter:url" content="https://jscloud.com.br/gabinete/?secao=login">
    <meta name="twitter:title" content="Mandato Digital">
    <meta name="twitter:description" content="Sistema de gestão de gabinete político">
    <meta name="twitter:image" content="https://jscloud.com.br/gabinete/public/img/logo.png">

    <script src="public/vendor/jquery/jquery.min.js"></script>
</head>

<body>

    <?php include './src/Views/router.php' ?>

    <!-- Modal -->
    <div class="modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <!-- Ícone de carregamento e texto -->
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Aguarde, carregando...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="public/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/vendor/startbootstrap-simple-sidebar-master/dist/js/scripts.js"></script>
    <script src="public/vendor/jquery-mask/jquery.mask.min.js"></script>
    <script src="public/js/app.js"></script>
</body>

</html>