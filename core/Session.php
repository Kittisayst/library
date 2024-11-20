<?php
class Session
{
    private $flashData = [];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // ຕັ້ງຄ່າ session ທີ່ປອດໄພ
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);

            session_start();
        }

        // ດຶງ flash data ຈາກ session
        $this->flashData = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
    }

    /**
     * ເກັບຄ່າໃນ session
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * ດຶງຄ່າຈາກ session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * ກວດສອບວ່າມີຄ່າໃນ session ຫຼືບໍ່
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * ລຶບຄ່າຈາກ session
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * ລຶບທຸກຢ່າງໃນ session
     * @return void
     */
    public function clear()
    {
        session_unset();
    }

    /**
     * ທຳລາຍ session
     * @return void
     */
    public function destroy()
    {
        session_destroy();
    }

    /**
     * ຕັ້ງຄ່າ flash data (ຂໍ້ມູນທີ່ຈະຖືກລຶບຫຼັງຈາກໃຊ້ຄັ້ງດຽວ)
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setFlash($key, $value)
    {
        $this->flashData[$key] = $value;
    }

    /**
     * ດຶງຄ່າ flash data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getFlash($key, $default = null)
    {
        return $this->flashData[$key] ?? $default;
    }

    /**
     * ກວດສອບວ່າມີ flash data ຫຼືບໍ່
     * @param string $key
     * @return bool
     */
    public function hasFlash($key): bool
    {
        return isset($this->flashData[$key]);
    }

    /**
     * ຕໍ່ອາຍຸ session
     * @return void
     */
    public function regenerate()
    {
        session_regenerate_id(true);
    }

    /**
     * ບັນທຶກ flash data ໄວ້ໃນ session ກ່ອນປິດ
     */
    public function __destruct()
    {
        if (!empty($this->flashData)) {
            $_SESSION['_flash'] = $this->flashData;
        }
    }

    /**
     * ຕັ້ງຄ່າ CSRF token
     * @return string
     */
    public function setCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        return $token;
    }

    /**
     * ກວດສອບ CSRF token
     * @param string $token
     * @return bool
     */
    public function validateCsrfToken($token): bool
    {
        $storedToken = $this->get('csrf_token');
        if (!$storedToken || !$token || $token !== $storedToken) {
            return false;
        }
        return true;
    }

    /**
     * ຕັ້ງຄ່າໝົດອາຍຸຂອງ session
     * @param int $minutes
     * @return void
     */
    public function setExpiration($minutes)
    {
        $this->set('_session_expires', time() + ($minutes * 60));
    }

    /**
     * ກວດສອບວ່າ session ໝົດອາຍຸຫຼືບໍ່
     * @return bool
     */
    public function isExpired(): bool
    {
        $expirationTime = $this->get('_session_expires');
        if (!$expirationTime) {
            return false;
        }
        return time() > $expirationTime;
    }

    /**
     * ດຶງຂໍ້ມູນທັງໝົດຈາກ session
     * @return array
     */
    public function all(): array
    {
        return $_SESSION;
    }
}
