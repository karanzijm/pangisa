<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 8/25/2019
 * Time: 8:18 PM
 */

include APPPATH . "controllers/CentralAccess.php";

class AccountManagement extends CI_Controller
{
    private $helper = null;
    private $ca_h = null;
    private $post_data = null;
    private $responseCode = 0;
    private $responseMessage = null;
    private $responseData = null;

    public function __construct()
    {
        parent::__construct();
        $ca_h = new CentralAccess();
        $this->ca_h = $ca_h;
        $this->helper = $ca_h->getBallisticHelper();

        $json = file_get_contents('php://input');
        $this->post_data = json_decode($json, TRUE);
    }

    public function index()
    {
        echo "hi mobile application";
    }

    public function login()
    {
        $email = htmlentities($this->post_data["email"]);
        $password = sha1($this->post_data["password"]);
        $userLogin = array("u.email" => $email, "u.password" => $password);

        $user = $this->db->select("u.*,app.user_type,app.level")
            ->from("users u")
            ->join("app_user_type app", "app.id=u.registration_type")
            ->where($userLogin)
            ->get()
            ->result_array();

        if (count($user) == 0) {
            $this->responseMessage = "Email or Password is incorrect";
            $this->responseCode = 100;
        } else {
            $user = $user[0];
            if ($user['approved'] == 0) {
                $this->responseMessage = "You have not approved your account, please check your email address or contact support  for help";
                $this->responseCode = 100;
            } else {
                $this->responseMessage = "Successfully Logged in";
                $this->responseData = $user;
                $this->responseCode = 0;
            }
        }

        $this->returnResponse();
    }

    public function createAccount()
    {
    }

    public function resetAccountPassword()
    {
    }

    private function returnResponse()
    {
        echo json_encode($this->ca_h->getGenericResponse($this->responseCode, $this->responseMessage, $this->responseData));
        return;
    }
}
