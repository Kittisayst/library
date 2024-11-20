<?php
class HomeController extends Controller
{
    public  function __construct()
    {
        MiddlewareManager::run(['auth']);
    }
    function index()
    {
        try {
            $this->registerCss("main.css");
            $this->registerJs("main.js");
            $db = Database::getInstance();
            $computers = $db->query("SELECT * FROM departments")->fetchAll();
            $users =new UserModel();
            return $this->render("home/index", ["computers" => $computers, "users" => $users->all()]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->error($th->getMessage());
        }
    }
    function show()
    {
        echo "<h1>ok</h1>";
    }
    function user($id)
    {
        echo "<h1>user $id</h1>";
    }
}
