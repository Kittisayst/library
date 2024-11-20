<?php
class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ".BASE_URL.trim('auth', '/'));
            return false;
        }
        session_regenerate_id(true);
        return true;
    }
}
