<?php

require_once(__DIR__.'/../bootstrap.php');

function ensure_writable($path) {
    try {
        $dir = COCKPIT_STORAGE_FOLDER.$path;
        if (!file_exists($dir)) {
            mkdir($dir, 0700, true);
            if ($path === '/data') {
                if (file_put_contents($dir.'/.htaccess', 'deny from all') === false) {
                    return false;
                }
            }
        }
        return is_writable($dir);
    } catch (Exception $e) {
        error_log($e);
        return false;
    }
}

function check_install() {
    // check whether sqlite is supported
    $sqlitesupport = false;
    try {
        if (extension_loaded('pdo')) {
            $test = new PDO('sqlite::memory:');
            $sqlitesupport = true;
        }
    } catch (Exception $e) { }

    // misc checks
    $checks = array(
        'Php version >= 7.1.0'                              => (version_compare(PHP_VERSION, '7.1.0') >= 0),
        'Missing PDO extension with Sqlite support'         => $sqlitesupport,
        'GD extension not available'                        => extension_loaded('gd'),
        'MBString extension not available'                  => extension_loaded('mbstring'),
        'Data folder is not writable: /storage/data'        => ensure_writable('/data'),
        'Cache folder is not writable: /storage/cache'      => ensure_writable('/cache'),
        'Temp folder is not writable: /storage/tmp'         => ensure_writable('/tmp'),
        'Thumbs folder is not writable: /storage/thumbs'    => ensure_writable('/thumbs'),
        'Uploads folder is not writable: /storage/uploads'  => ensure_writable('/uploads'),
    );

    $failed = [];
    foreach ($checks as $info => $check) {
        if (!$check) {
            $failed[] = $info;
        }
    }
    return $failed;
}

function has_min_1_user(&$app=null) {
    $app = $app ?: $cockpit ?: cockpit();
    // check whether cockpit is already installed
    try {
        if ($app->storage->getCollection('cockpit/accounts')->count()) {
            return true;
        }
    } catch(Exception $e) { }
    return false;
}

function do_admin_install(&$app=null) {
    $created = time();
    $password = getenv('COCKPIT_ADMIN_PASSWORD');
    $default_password = 'admin';

    $account = [
        'user'     => getenv('COCKPIT_ADMIN_USER') ?: 'admin',
        'name'     => getenv('COCKPIT_ADMIN_NAME') ?: 'Admin',
        'email'    => getenv('COCKPIT_ADMIN_EMAIL') ?: 'admin@yourdomain.de',
        'active'   => true,
        'group'    => 'admin',
        'password' => $app->hash($password ?: $default_password),
        'i18n'     => $app('i18n')->locale,
        '_created' => $created,
        '_modified'=> $created,
    ];

    error_log('Creating admin user "' . $account['user'] . '" ...');
    $app->storage->insert("cockpit/accounts", $account);
    return [
        'user' => $account ? $account['user'] : null,
        'specified_password' => (bool)$password,
        'default_password' => $default_password,
    ];
}

function maybe_install(&$app=null) {
    // error_log('Trying install...');
    $app = $app ?: $cockpit ?: cockpit();
    if(has_min_1_user($app)) {
        // error_log('User Exists!');
        return [];
    }

    $failed = check_install();
    if ($failed) {
        foreach ($failed as &$desc) {
            error_log('Error checking install: '.$desc);
        }
        return ['failed' => $failed];
    }
    do_admin_install($app);
}
