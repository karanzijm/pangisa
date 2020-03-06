<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/28/2020
 * Time: 9:40 PM
 */
include APPPATH . "controllers/CentralAccess.php";
class BaseController extends CI_Controller
{
    protected $helper = null;
    protected $ca_h = null;
    protected $post_data = null;
    protected $responseCode = 0;
    protected $responseMessage = null;
    protected $responseData = null;
    protected $limit = 50;
    protected $offset = 0;
    protected $sys_user = 4;
    protected $client_user = 6;
    protected $vendor_user = 5;
    protected $called_link = null;
    protected $unique_trace_id = null;
    protected $link = null;
    protected $ctrl_name = 'SharedActions';
    protected $error = null;

    public function __construct()
    {
        parent::__construct();

        ini_set('max_execution_time', 60);
        date_default_timezone_set("Africa/Kampala");
        $this->load->library("EmailHelper");
        $this->load->library("user_agent");
        $ca_h = new CentralAccess();
        $this->ca_h = $ca_h;
        $this->helper = $ca_h->getBallisticHelper();
        $json = file_get_contents('php://input');
        $this->post_data = json_decode($json, TRUE);

        $called_link = $this->uri->uri_string();
        $trace_uniq_id = strtoupper(uniqid("PN-MOBILE-"));

        $this->called_link = $called_link;
        $this->unique_trace_id = $trace_uniq_id;
        $_SESSION['called_link'] = $called_link;
        $_SESSION['unique_trace_id'] = $trace_uniq_id;
        //set_error_handler(array(&$this->helper, "my_error_handler"));

        //flush the buffer
        ob_flush();
    }

    public function getUserAccount($id){
        return $this->db->where(['id'=>$id])->from("users")->get()->row()?:null;
    }

    public function getCategoryById($id){
        return $this->db->where(['id' => $id])->get("categories")->row();
    }

    public function getSubCategoryById($id){
        return $this->db->where(['id' => $id])->get("sub_categories")->row();
    }

}
