<?php
class CsrfMiddleware extends Middleware
{
    public function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !isset($_SESSION['csrf_token']) ||
                $_POST['csrf_token'] !== $_SESSION['csrf_token']
            ) {
                $this->error('CSRF token ບໍ່ຖືກຕ້ອງ');
                return false;
            }
        }
        return true;
    }
}
