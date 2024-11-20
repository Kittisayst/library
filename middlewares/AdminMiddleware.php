<?php
class AdminMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->error('ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້');
            return false;
        }
        return true;
    }
}
