<?php
define('COCKPIT_INSTALL', true);

require(__DIR__.'/installer.php');

$info = maybe_install();

if(! $info) {
    header('Location: '.cockpit()->baseUrl('/'));
    exit;
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System installation</title>
    <script src="../assets/lib/jquery.js"></script>
    <script src="../assets/lib/uikit/js/uikit.min.js"></script>
    <link rel="stylesheet" href="../assets/app/css/style.css">
    <style>
        .info-container {
            width: 460px;
            max-width: 90%;
        }

        .install-dialog {
            box-shadow: 0 30px 75px 0 rgba(10, 25, 41, 0.2);
        }
    </style>
</head>
<body class="uk-height-viewport uk-flex uk-flex-middle">


    <div class="info-container uk-container-center uk-text-center uk-animation-slide-fade">

        <div class="install-dialog uk-panel uk-panel-box uk-panel-space uk-animation-scale">

            <img src="../assets/app/media/logo.svg" width="80" height="80" alt="logo">

            <?php if ($info['failed']): ?>

                <h1 class="uk-text-bold">Installation failed</h1>

                <img src="../assets/app/media/icons/emoticon-sad.svg" width="100" alt="sad">

                <div class="uk-margin">

                    <?php foreach ($info['failed'] as &$desc): ?>
                    <div class="uk-alert uk-alert-danger">
                        <?php echo @$desc;?>
                    </div>
                    <?php endforeach; ?>

                </div>

                <div>
                    <a href="?<?php echo time();?>" class="uk-button uk-button-large uk-button-outline uk-button-primary uk-width-1-1">Retry installation</a>
                </div>


            <?php else: ?>

                <h1 class="uk-text-bold">Installation completed</h1>

                <img src="../assets/app/media/icons/party.svg" width="100" alt="success">

                <?php if($info['user']): ?>
                <div class="uk-margin-large">
                    <span class="uk-badge uk-badge-outline uk-text-muted">Login Credentials</span>
                    <p>
                        user: <?= $info['user'] ?> <br/>
                        password: <?=
                            $info['specified_password']
                            ? '<b>*defined via environment.*</b>'
                            : $info['default_password'] ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if(! $info['specified_password']): ?>
                <div class="uk-alert uk-alert-warning">
                    Please change the login information after your first login into the system for obvious security reasons.
                </div>
                <?php endif; ?>

                <div class="uk-margin-top">
                    <a href="../" class="uk-button uk-button-large uk-button-primary uk-button-outline uk-width-1-1">Login now</a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</body>
</html>
