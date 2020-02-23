<?php
if(getenv('COCKPIT_ADMIN_PASSWORD')) {
    require(__DIR__.'/installer.php');

    $info = maybe_install();
    if(!$info) {} // user already exists
    else if($info['success']) { // remove password so this only runs once
        putenv('COCKPIT_ADMIN_PASSWORD');
    }
    else if ($info['failed']) {
         foreach ($info['failed'] as &$desc) {
            error_log($desc);
        }
    }
}
