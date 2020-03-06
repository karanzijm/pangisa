<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 10/24/2019
 * Time: 5:40 PM
 */

include "CentralAccess.php";

class Accounts extends CI_Controller
{
    private $title = "Pangisa";
    private $ctrl_name = 'Accounts';
    private $error = null;
    private $link = null;
    private $active_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $viewFolder = "user_portal/";
    private $breadcrumbs = null;
    private $sys_user = 4;
    private $client_user = 6;
    private $vendor_user = 5;
    private $helper = null;
    private $limit = 100;
    private $usercodepadd = 100000;

    private $called_link = null;
    private $unique_trace_id = null;

    public function __construct()
    {
        parent::__construct();
        $_SERVER['CI_ENV'] = "production";
        $this->load->library("Lib");
        $this->load->library("user_agent");
        $this->load->library("EmailHelper");
        $this->load->model("BusinessModel");
        $this->link = base_url() . $this->ctrl_name . '/';

        $ca_h = new CentralAccess();
        $this->helper = $ca_h->getBallisticHelper();

        $this->called_link = $this->uri->uri_string();
        $this->unique_trace_id = strtoupper(uniqid("PANGISA-ER"));
        $_SESSION['called_link'] = $this->called_link;
        $_SESSION['unique_trace_id'] = $this->unique_trace_id;

        @set_error_handler(array(&$this->helper, "my_error_handler"));
    }

    public function businessregistration($action = null, $error = null)
    {
        $this->title = "Pangisa * Create Trading Account";
        $link = $this->link . "businessregistration/save";
        $this->active_link = "businessregistration";

        if ($action == "save") {

            $input_data = $this->input->post();
            $this->helper->cleanArrayInput($input_data);

            $vendor_phone_number = $input_data['phone'];
            $vendor_phone_number_len = strlen($vendor_phone_number);

            if ($vendor_phone_number_len < 9 || $vendor_phone_number_len > 12) {
                $this->error = "Phone Number $vendor_phone_number is not the right length must be in the format 256770123456 or 0770123456 ";
                $this->load_view("registerbusiness");
                return;
            }

            //check email and phone
            $userCheck = $this->db->where(["email" => $input_data['email']])->get("users")->result_array();
            if (count($userCheck) > 0) {
                $this->error = "A user with that Email Address already exists";
                $this->load_view("registerbusiness");
                return;
            }

            //check email and phone
            $userCheck = $this->db->where(["phone" => $input_data['phone']])->get("users")->result_array();
            if (count($userCheck) > 0) {
                $this->error = "A user with that phone number already exists";
                $this->load_view("registerbusiness");
                return;
            }

            if ($input_data['admin_password'] <> $input_data['confirm_password']) {
                $this->error = "Passwords are  not the same";
                $this->load_view("registerbusiness");
                return;
            }

            $companycertificate = $this->uploadImages('companyidetification', 'id_photo');

            $user_account = array(
                "name" => $input_data['name'],
                "email" => $input_data['email'],
                "phone" => $input_data['phone'],
                "location" => $input_data['location'],
                "alternative_phone" => $input_data['alternative_phone'],
                "password" => $input_data['admin_password'],
                "registration_type" => $this->vendor_user,
                "status" => 1,
                "role" => "vendor",
                "approved" => 0,
                "id_photo" => $companycertificate,
            );

            $user_account['password'] = sha1($input_data['admin_password']);

            //create user account for the company account
            $this->db->insert("users", $user_account);

            $uid = $this->db->insert_id();

            if ($uid) {
                $approval_string = $this->helper->generateHashUrl($uid, $input_data['email']);
                $user_code = "V" . ($this->usercodepadd + $uid);

                $this->db->set(["approval_code" => $approval_string, "user_code" => $user_code])->where("id", $uid)->update("users");
                $msg = $this->emailhelper->composeEmailOnAccountCreation("user", $uid, $approval_string);

                //create a vendor transaction account
                $vendor_account_gl = [];
                $vendor_account_gl['vendor_id'] = $uid;
                $vendor_account_gl['account_number'] = $user_code;
                $vendor_account_gl['total_amount_collected'] = 0;
                $vendor_account_gl['total_amount_withdrawn'] = 0;
                $vendor_account_gl['date_created'] = date('Y-m-d');
                $this->db->insert("vendor_account_gl", $vendor_account_gl);

                //send an email to user after account creation
                $emailObjectForDatabaseSave['client'] = $uid;
                $emailObjectForDatabaseSave['action'] = "CREATE_ACCOUNT";
                $emailObjectForDatabaseSave['body'] = $msg;
                $emailObjectForDatabaseSave['email_address'] = $input_data['email'];
                $emailObjectForDatabaseSave['status'] = 0;
                $emailObjectForDatabaseSave['subject'] = "PANGISA - APPROVE ACCOUNT";
                $this->db->insert("email_outbox", $emailObjectForDatabaseSave);

                $this->saveLog("users", "created account", false, $uid);
                $this->load_view("registration_complete");
            } else {
                echo " A database error has occured";
            }


        } elseif ($action == "add") {
            $this->error = $error;
            $form = $this->BusinessModel->getForm();
            $form = $this->lib->DrawFormTabular($link, $form, null);
            $this->load_view("registerbusiness");

        } else {
            Redirect($this->link . 'businessregistration/add');
        }

    }

    public function load_view($view, $data = null)
    {
        $data['view'] = $view ? $this->viewFolder . $view : $this->viewFolder . "landing_page";
        $data['data'] = $data;
        $data['title'] = $this->title;
        $data['error'] = $this->error;
        $data['active_link'] = $this->active_link;

        $this->load->view($this->viewFolder . "user_portal_header", $data);
        $this->load->view($this->viewFolder . "footer");
    }

    private function uploadImages($type, $image)
    {
        $filename = strtolower(md5($type . "_" . time()));
        $upload_path = "partners";

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => "jpg|png|jpeg",
            'overwrite' => false,
            'max_size' => 40240,
            'encrypt_name' => false,
            'file_name' => $filename
        ];

        $this->upload->initialize($config);

        if (!$this->upload->do_upload($image)) {
            throw new Exception(json_encode($this->upload->display_errors()));
        }
        $file_data = $this->upload->data();
        return $file_data['file_name'];
    }

    public function clientregistration($action = null, $error = null)
    {
        $this->title = "Pangisa * Create Client Account";
        $link = $this->link . "clientregistration/save";
        $this->active_link = "clientregistration";

        if ($action == "save") {
            $uid = null;

            $input_data = $this->input->post();

            //pass value by reference
            $this->helper->cleanArrayInput($input_data);

            //check if the phone number or email exists
            $existing_user = $this->db->where(["phone" => $input_data['phone']])->count_all_results("users");

            if ($existing_user > 0) {
                $this->error = "A user with this number already exists";
                $this->load_view("registerclient");
                return;
            }

            $existing_user = $this->db->where(["email" => $input_data['email']])->count_all_results("users");

            if ($existing_user > 0) {
                $this->error = "A user with this Email already exists";
                $this->load_view("registerclient");
                return;
            }


            $user_account = array(
                "name" => $input_data['first_name'] . " " . $input_data['last_name'],
                "email" => $input_data['email'],
                "phone" => $input_data['phone'],
                "location" => $input_data['location'],
                "alternative_phone" => null,
                "password" => $input_data['admin_password'],
                "registration_type" => $this->client_user,
                "status" => 1,
                "role" => "client",
                "approved" => 0,
            );

            $user_account['password'] = sha1($input_data['admin_password']);

            //save to database
            $uid = $this->saveToDatabase("users", $user_account);

            if (!$uid) {
                $this->error = $this->db->error()->message;
                $this->load_view("registerclient");

            } else {
                //generate hash code for account approval
                $approval_string = $this->helper->generateHashUrl($uid, $input_data['email']);
                $this->db->set(["approval_code" => $approval_string, "user_code" => "C" . ($this->usercodepadd + $uid)])->where("id", $uid)->update("users");

                $msg = $this->emailhelper->composeEmailOnAccountCreation("user", $uid, $approval_string);

                //send an email to user after account creation
                $emailObjectForDatabaseSave['client'] = $uid;
                $emailObjectForDatabaseSave['action'] = "CREATE_ACCOUNT";
                $emailObjectForDatabaseSave['body'] = $msg;
                $emailObjectForDatabaseSave['email_address'] = $input_data['email'];
                $emailObjectForDatabaseSave['status'] = 0;
                $emailObjectForDatabaseSave['subject'] = "PANGISA - APPROVE ACCOUNT";
                $this->db->insert("email_outbox", $emailObjectForDatabaseSave);

                $this->saveLog("users", "created account", false, $this->db->insert_id());
                Redirect($this->link . 'login');
            }


        } elseif ($action == "add") {
            $this->error = $error;
            $form = array(
                ["name" => "Full Names", "variable" => "name", "type" => "text", "input_type" => "text", "required" => "true"],
                ["name" => "Phone Number", "variable" => "phone", "type" => "text", "input_type" => "text", "required" => "true"],
                ["name" => "Alternative Telephone", "variable" => "alternative_phone", "type" => "text", "input_type" => "text", "required" => "true"],
                ["name" => "Email", "variable" => "email", "type" => "text", "input_type" => "text", "required" => "true"],
                ["name" => "Password", "variable" => "admin_password", "type" => "text", "input_type" => "password", "required" => "true"],
                ["name" => "Confirm Password", "variable" => "confirm_password", "type" => "text", "input_type" => "password", "required" => "true"],
                ["name" => "Location ", "variable" => "location", "type" => "text", "input_type" => "text", "required" => "true"],

            );
            $form = $this->lib->DrawFormTabular($link, $form, null);
            //$this->load_view_simple($form);

            $this->load_view("registerclient");

        } else {
            Redirect($this->link . 'clientregistration/add');
        }


    }

    private function createvendoraccounts()
    {
        $vendors = $this->db->where("registration_type", $this->vendor_user)->select("id,user_code")->from("users")->get()->result_array();
        foreach ($vendors as $vendor) {
            $this->db->insert('vendor_account_gl', ['vendor_id' => $vendor['id'], 'account_number' => $vendor['user_code']]);
        }
    }


}
