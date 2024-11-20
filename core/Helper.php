<?php
class Helper
{
    /**
     * ທຳຄວາມສະອາດຂໍ້ຄວາມ
     */
    public static function clean($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    /**
     * ສ້າງ URL
     */
    public static function url($path = '')
    {
        return BASE_URL . trim($path, '/');
    }

    /**
     * ເຂົ້າລະຫັດຂໍ້ຄວາມ
     */
    public static function encrypt($data)
    {
        $key = ENCRYPTION_KEY;
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * ຖອດລະຫັດຂໍ້ຄວາມ
     */
    public static function decrypt($data)
    {
        $key = ENCRYPTION_KEY;
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'AES-256-CBC', $key, 0, $iv);
    }

    /**
     * Format ວັນທີ
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        return date($format, strtotime($date));
    }

    /**
     * Format ເງິນ
     */
    public static function formatMoney($amount)
    {
        return number_format($amount, 0, ',', '.');
    }

    /**
     * Generate ລະຫັດແບບສຸ່ມ
     */
    public static function generateCode($length = 6)
    {
        return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * Upload file
     */
    public static function uploadFile($file, $folder = 'uploads')
    {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $path = $folder . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                return $path;
            }
        }
        return false;
    }

    /**
     * Redirect with flash message
     */
    public static function redirect($url, $message = '', $type = 'success')
    {
        if (!empty($message)) {
            $_SESSION['flash'] = [
                'message' => $message,
                'type' => $type
            ];
        }
        header("Location: " . self::url($url));
        exit;
    }

    /**
     * Flash message
     */
    public static function flash($showCloseButton = false)
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);

            // ສ້າງ random ID ສຳລັບ alert
            $alertId = 'flash-' . uniqid();

            // ກຽມ HTML ສຳລັບປຸ່ມປິດ
            $closeButton = '';
            if ($showCloseButton) {
                $closeButton = "<button type='button' class='flash-close-btn' onclick='closeFlash(\"{$alertId}\")'>
                                  <span>&times;</span>
                               </button>";
            }

            // CSS ສຳລັບ flash message
            $css = "<style>
                .flash-message {
                    padding: 15px;
                    margin-bottom: 20px;
                    border: 1px solid transparent;
                    border-radius: 4px;
                    position: relative;
                }
                .flash-close-btn {
                    position: absolute;
                    right: 10px;
                    top: 10px;
                    padding: 0;
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    opacity: 0.5;
                }
                .flash-close-btn:hover {
                    opacity: 1;
                }
                .alert-success { background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; }
                .alert-info { background-color: #d9edf7; border-color: #bce8f1; color: #31708f; }
                .alert-warning { background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b; }
                .alert-danger { background-color: #f2dede; border-color: #ebccd1; color: #a94442; }
                .fade-out {
                    opacity: 0;
                    transition: opacity 0.5s ease-out;
                }
            </style>";

            // JavaScript ສຳລັບປິດ flash message
            $js = "<script>
                function closeFlash(alertId) {
                    const alert = document.getElementById(alertId);
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }
            </script>";

            // ສ້າງ flash message ພ້ອມປຸ່ມປິດ
            return $css . $js . "<div id='{$alertId}' class='flash-message alert-{$flash['type']}'>
                        {$flash['message']}
                        {$closeButton}
                    </div>";
        }
        return '';
    }

    /**
     * Pagination
     */
    public static function paginate($total, $per_page, $current_page)
    {
        $total_pages = ceil($total / $per_page);
        $html = '<ul class="pagination">';

        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $current_page == $i ? 'active' : '';
            $html .= "<li class='page-item {$active}'>";
            $html .= "<a class='page-link' href='?page={$i}'>{$i}</a></li>";
        }

        return $html . '</ul>';
    }

    public static function asset($path)
    {
        return BASE_URL . "public/" . trim($path, '/');
    }
}
