<?php
abstract class Middleware {
    /**
     * ຟັງຊັນຫຼັກສຳລັບການກວດສອບ
     * @return bool
     */
    abstract public function handle(): bool;

    /**
     * ຟັງຊັນສຳລັບການ redirect
     * @param string $url
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    /**
     * ສົ່ງຂໍ້ຄວາມ error ກັບຄືນ
     * @param string $message
     */
    protected function error($message) {
        $errorController = new ErrorController();
        $errorController->handle('403', [
            'message' => $message
        ]);
        exit();
    }
}