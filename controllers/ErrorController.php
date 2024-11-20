<?php
class ErrorController {
    /**
     * ປະເພດຂໍ້ຜິດພາດທີ່ຮອງຮັບ
     */
    private const ERROR_TYPES = [
        '404' => 'Page Not Found',
        '403' => 'Forbidden',
        '500' => 'Internal Server Error',
        'database' => 'Database Error',
        'validation' => 'Validation Error',
        'unauthorized' => 'Unauthorized Access'
    ];

    /**
     * ຈັດການຂໍ້ຜິດພາດ
     * @param string $type ປະເພດຂໍ້ຜິດພາດ
     * @param array $params ຂໍ້ມູນເພີ່ມເຕີມ
     */
    public function handle($type = '404', array $params = []): void 
    {
        $errorData = $this->prepareErrorData($type, $params);
        $this->logError($errorData);

        // ສົ່ງ HTTP status code ທີ່ເໝາະສົມ
        http_response_code($this->getHttpCode($type));

        // ກວດສອບວ່າແມ່ນ AJAX request ຫຼື ບໍ່
        if ($this->isAjaxRequest()) {
            $this->handleAjaxError($errorData);
            return;
        }

        // ເລືອກ template ຕາມສະພາບແວດລ້ອມ
        $template = ENVIRONMENT === 'development' ? 'detailed' : 'simple';
        
        // ສະແດງໜ້າຂໍ້ຜິດພາດ
        $this->renderErrorPage($template, $errorData);
    }

    /**
     * ຈັດຕຽມຂໍ້ມູນຂໍ້ຜິດພາດ
     */
    private function prepareErrorData($type, $params): array 
    {
        return [
            'type' => $type,
            'title' => self::ERROR_TYPES[$type] ?? 'Unknown Error',
            'message' => $params['message'] ?? $this->getDefaultMessage($type),
            'code' => $params['code'] ?? null,
            'details' => $params['details'] ?? null,
            'timestamp' => date('Y-m-d H:i:s'),
            'url' => $_SERVER['REQUEST_URI'],
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'Direct Access',
            'ip' => $_SERVER['REMOTE_ADDR']
        ];
    }

    /**
     * ບັນທຶກຂໍ້ຜິດພາດ
     */
    private function logError($errorData): void 
    {
        $logMessage = sprintf(
            "[%s] %s: %s | Type: %s | Code: %s | URL: %s | IP: %s | Details: %s\n",
            $errorData['timestamp'],
            $errorData['title'],
            $errorData['message'],
            $errorData['type'],
            $errorData['code'],
            $errorData['url'],
            $errorData['ip'],
            json_encode($errorData['details'])
        );

        error_log($logMessage, 3, ERROR_LOG_FILE);
    }

    /**
     * ດຶງ HTTP status code
     */
    private function getHttpCode($type): int 
    {
        $codes = [
            '404' => 404,
            '403' => 403,
            '500' => 500,
            'database' => 500,
            'validation' => 400,
            'unauthorized' => 401
        ];

        return $codes[$type] ?? 500;
    }

    /**
     * ດຶງຂໍ້ຄວາມຂໍ້ຜິດພາດພື້ນຖານ
     */
    private function getDefaultMessage($type): string 
    {
        $messages = [
            '404' => 'ບໍ່ພົບໜ້າທີ່ຄົ້ນຫາ',
            '403' => 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້',
            '500' => 'ເກີດຂໍ້ຜິດພາດພາຍໃນເຊີເວີ',
            'database' => 'ເກີດຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ',
            'validation' => 'ຂໍ້ມູນທີ່ປ້ອນບໍ່ຖືກຕ້ອງ',
            'unauthorized' => 'ກະລຸນາເຂົ້າສູ່ລະບົບກ່ອນ'
        ];

        return $messages[$type] ?? 'ເກີດຂໍ້ຜິດພາດທີ່ບໍ່ຮູ້ຈັກ';
    }

    /**
     * ກວດສອບວ່າແມ່ນ AJAX request ຫຼື ບໍ່
     */
    private function isAjaxRequest(): bool 
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * ຈັດການ AJAX errors
     */
    private function handleAjaxError($errorData): void 
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => [
                'type' => $errorData['type'],
                'message' => $errorData['message'],
                'code' => $errorData['code']
            ]
        ]);
    }

    /**
     * ສະແດງໜ້າຂໍ້ຜິດພາດ
     */
    private function renderErrorPage($template, $errorData): void 
    {
        extract($errorData);
        require_once "views/errors/{$template}.php";
    }
}