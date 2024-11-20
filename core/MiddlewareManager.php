<?php

/**
 * Class ສຳລັບຈັດການ Middleware
 */
class MiddlewareManager
{
    private static $middlewares = [];

    /**
     * ເພີ່ມ middleware
     * @param string $name
     * @param string $class
     */
    public static function add($name, $class)
    {
        self::$middlewares[$name] = $class;
    }

    /**
     * ເອີ້ນໃຊ້ middleware
     * @param string|array $middlewares
     * @return bool
     */
    public static function run($middlewares): bool
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            if (isset(self::$middlewares[$middleware])) {
                $class = self::$middlewares[$middleware];
                $instance = new $class();
                if (!$instance->handle()) {
                    return false;
                }
            }
        }

        return true;
    }
}
