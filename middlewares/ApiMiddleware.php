<?php
class ApiMiddleware extends Middleware
{
    public function handle(): bool
    {
        $headers = getallheaders();
        if (
            !isset($headers['X-API-Key']) ||
            $headers['X-API-Key'] !== API_KEY
        ) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'API key ບໍ່ຖືກຕ້ອງ'
            ]);
            return false;
        }
        return true;
    }
}
