<?php
class Router {
    private string $basePath;

    public function __construct(string $basePath = '/jomvc/') {
        $this->basePath = $basePath;
    }

    function RouterProvider() {
        try {
            // ຮັບເອົາ URI path
            $uri = $_SERVER['REQUEST_URI'];
            
            // ລຶບ base path ອອກຈາກ URI
            $path = str_replace($this->basePath, '', $uri);
            $path = trim($path, '/');
            
            // ຖ້າ path ວ່າງເປົ່າ, ໃຫ້ເປັນໜ້າຫຼັກ
            if (empty($path)) {
                $path = 'home';
            }
            
            // ແຍກ parameters
            $params = explode('/', $path);
            $controller = ucfirst($params[0] ?? 'Home') . 'Controller';
            $action = $params[1] ?? 'index';
            $id = $params[2] ?? null;
            
            // ກວດສອບແລະໂຫຼດ controller
            if (file_exists("controllers/{$controller}.php")) {
                require_once "controllers/{$controller}.php";
                $controllerInstance = new $controller;
                
                if (method_exists($controllerInstance, $action)) {
                    return $controllerInstance->$action($id);
                } else {
                    // ເອີ້ນໃຊ້ ErrorController ສຳລັບ method ທີ່ບໍ່ພົບ
                    $errorController = new ErrorController();
                    return $errorController->handle('404', [
                        'error' => 'action_not_found',
                        'controller' => $controller,
                        'action' => $action,
                        'message' => "Method {$action} not found in controller {$controller}"
                    ]);
                }
            } else {
                // ເອີ້ນໃຊ້ ErrorController ສຳລັບ controller ທີ່ບໍ່ພົບ
                $errorController = new ErrorController();
                return $errorController->handle('404', [
                    'error' => 'controller_not_found',
                    'controller' => $controller,
                    'message' => "Controller not found: {$controller}"
                ]);
            }
        } catch (Exception $e) {
            // ຈັດການຂໍ້ຜິດພາດທົ່ວໄປ
            $errorController = new ErrorController();
            return $errorController->handle('500', [
                'error' => 'system_error',
                'message' => $e->getMessage(),
                'details' => ENVIRONMENT === 'development' ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ]);
        }
    }
}