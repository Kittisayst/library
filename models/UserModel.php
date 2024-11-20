<?php
class UserModel extends Model
{
    protected $table = 'users';
    private $session;
    public function __construct()
    {
        parent::__construct();
        $this->session = new Session();
    }

    public function getAllUsers()
    {
        return self::all();
    }

    public function login($username, $password)
    {
        try {
            return $this->where("Username=? AND Password=?", [$username, $password])[0] ?? [];
        } catch (\Throwable $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'ເກີດຂໍ້ຜິດພາດໃນການເຂົ້າສູ່ລະບົບ. ກະລຸນາລອງໃໝ່ອີກຄັ້ງ'
            ];
        }
    }
}
