<?php
// ໂຫຼດການຕັ້ງຄ່າ
require_once 'config/config.php';
require_once 'core/MiddlewareManager.php';

// ເລີ່ມ session
session_start();

// ລົງທະບຽນ middleware
MiddlewareManager::add('auth', AuthMiddleware::class);

// Autoload classes
spl_autoload_register(function($class) {
    $paths = [
        'controllers/',
        'models/',
        'core/',
        'middlewares/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});



// ສ້າງ Router instance
$router = new Router(BASE_URL);
$router->RouterProvider();