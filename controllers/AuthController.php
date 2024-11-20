<?php
class AuthController extends Controller
{
    private $session;
    public function __construct()
    {
        $this->session = new Session();
    }
    public function index()
    {
        return $this->render("auth/index", ["title" => "ເຂົ້າສູ່ລະບົບ"], "simple");
    }

    public function login()
    {
        var_dump($_POST);
        if ($this->isCsrfToken()) {
            $usermodel = new UserModel();
            $result = $usermodel->login($_POST['username'], $_POST['password']);
            var_dump($result);
            if ($result) {
                $this->session->set('user_id', $result->UserID);
                $this->session->setExpiration(60);
                $this->redirectWith("/home", "ເຂົ້າສູ່ລະບົບສຳເລັດ", "success");
            } else {
                $this->redirectWith("/auth","ຊື່ຜູ້ໃຊ້ງານ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ","danger");
            }
        }
    }

    public function logout()
    {
        $this->session->destroy();
        $this->redirect("/home");
    }
}
