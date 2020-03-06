<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/18/2018
 * Time: 10:04 AM
 */

//include "CentralAccess.php";
include "BaseController.php";

class Index extends BaseController
{
    protected $title = "Pangisa";
    protected $ctrl_name = 'Index';
    //private $error = null;
    //private $link = null;
    private $active_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $viewFolder = "user_portal/";
    private $breadcrumbs = null;
    //private $sys_user = 4;
    //private $client_user = 6;
    //private $vendor_user = 5;
    //private $helper = null;
    //private $limit = 100;
    private $usercodepadd = 100000;

    //private $called_link = null;
    //private $unique_trace_id = null;

    public function __construct()
    {
        parent::__construct();
        $_SERVER['CI_ENV'] = "production";
        $this->load->library("Lib");
        $this->load->library("user_agent");
        $this->load->library("EmailHelper");
        //load the form validation library
        $this->load->library("form_validation");
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

    public function index($action = null, $id = null, $page_id = null)
    {
        $this->title = "Pangisa";


        $this->db->select("id, name,verified, color, category as cat, size, is_negotiable,identification_number,(select name from categories where id=items.category) as category, price, front_view,brief_description, (select name from price_points where id=items.price_point) as price_point,")
            ->from("items")->where(["status" => 1, 'char_length(brief_description)>=' => 300]);

        $items = $this->db->limit(4, 0)->order_by("rand()")->get()->result_array();
        $items = $this->stripHtmlTagsFromStringOrAssociativeArray($items);

        $this->db->select("id, name, verified, color, category as cat, size, availability, DATE_FORMAT(date_added, '%M %D, %Y') as date, (select name from categories where id=items.category) as category, (select name from price_points where id=items.price_point) as price_point, price, front_view,brief_description")
            ->from("items");

        if ($action == "category" && $id <> "all") {
            $this->db->where(['category' => $id]);
        }else if ($action == "category" && empty($id)){
            Redirect(base_url() . "Index/login/");
        }

        $this->db->where("status", 1);
        $items_many = $this->db->limit(25, 0)->order_by("date_added", "desc")->get()->result_array();
        $items_many = $this->stripHtmlTagsFromStringOrAssociativeArray($items_many);

        $categories = $cats = $this->db
            ->select("ca.id, ca.name, ca.icon, (select count(*) from items where category=ca.id) as items")
            ->from("categories ca")
            ->order_by("items desc")
            ->limit(8)
            ->get()
            ->result_array();

        $data['categories'] = $categories;
        $data['items'] = $items;
        $data['items_many'] = $items_many;


        $this->load_view("landing_page", $data);
    }

    private function stripHtmlTagsFromStringOrAssociativeArray($data)
    {
        if (is_string($data)) {
            return strip_tags($data);
        } elseif (is_array($data)) {
            $result = [];

            foreach ($data as $k => $v) {
                $resObject = [];
                foreach ($v as $key => $value) {
                    $resObject[$key] = strip_tags($value);
                }

                array_push($result, $resObject);
            }

            return $result;
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

    public function logout()
    {
        $this->session->sess_destroy();
        Redirect(base_url());
    }

    public function myorders($action = null, $id = null)
    {
        $user_id = $this->session->user['id'];

        if (empty($user_id) || $user_id == null) {
            Redirect(base_url() . "Index/login/");
        } else {
            $login_type = $this->session->user['user_type'];

            if ($login_type <> "client") {
                Redirect(base_url() . "AppClient/index");
            } else {

                if ($action == "cancel") {

                    $this->db->where('id', $id)->set(["user_cancel" => 1, "status" => 2])->update("orders");
                    $this->saveLog("orders", "cancelled", false, $id);

                }

                $orders = $this->db->select(
                    "o.id,o.order_number, o.number_of_days,o.date,o.place_of_use, o.place_of_use,o.approved,o.user_cancel,o.comment, 
                    i.id as item_id, i.name,i.size,i.price,i.identification_number, p.name as price_point"
                )
                    ->from("orders o")
                    ->join("items i", "i.id=o.item")
                    ->join("price_points p", "p.id=i.price_point")
                    ->where(["o.client" => $this->session->user['id']])
                    ->order_by("o.date desc")
                    ->get()
                    ->result_array();

                $this->load_view("myorders", ["orders" => $orders]);
            }
        }

    }

    public function saveLog($table, $action, $save, $id = null)
    {

        $info = null;
        if ($save) {
            $info = $this->db->where("id", $id)->get($table)->row();
        }

        $data['original_data'] = json_encode($info);
        $data['user'] = $this->session->user['id'];
        $data['action'] = $action;
        $data['item_id'] = $id;
        $data['table'] = $table;

        $this->db->insert("logs", $data);
    }

    public function orderItem($id, $action = null)
    {
        $user_id = $this->session->user['id'];
        $this->session->set_userdata('nav_url', $this->link . 'orderItem/' . $id);

        if (empty($user_id) || $user_id == null) {
            Redirect(base_url() . "Index/login/");
        } else {
            $login_type = $this->session->user['user_type'];

            if ($login_type <> "client") {
                Redirect(base_url() . "AppClient/index");
            } else {
                $item = $this->db->select("i.*,(select name from price_points where id=i.price_point) as price_point, 
                (select name from users where id=i.added_by) as owner, 
                (select name from categories where id=i.category)
                 as category")
                    ->from("items i")
                    ->where(['i.id' => $id])
                    ->get()
                    ->row();

                if ($action == "confirm") {
                    $order = $this->input->post();

                    $this->db->trans_start();
                    $number_of_days = $order['number_of_days'] < 1 ? (-1 * $order['number_of_days']) : $order['number_of_days'];
                    $order_quantity = ($order['quantity'] > 0) ? $order['quantity'] : 1;
                    $order_amount = $item->price * $number_of_days * $order_quantity;

                    $order_dets = array(
                        "item" => $id,
                        "owner" => $item->added_by,
                        "number_of_days" => $number_of_days,
                        "place_of_use" => $order['place_of_use'],
                        "client" => $this->session->user['id'],
                        "quantity" => $order_quantity,
                        "usage_description" => $order['usage_description'],
                        "approved" => 0,
                        "order_amount" => $order_amount,
                        "item_price_at_order" => $item->price_without_markup,
                        "markup_at_order" => (($item->price * $number_of_days) - ($item->price_without_markup * $number_of_days)),
                        "markup_cleared" => 0,
                        "item_negotiation_completed" => 'False',
                        "is_negotiable" => $item->is_negotiable,
                        "order_source" => 'web',
                    );

                    //insert order
                    $this->db->insert("orders", $order_dets);
                    $order_insert_id = $this->db->insert_id();

                    //save logs
                    $this->saveLog("orders", "Placed Order", false, $order_insert_id);
                    $order_number = "PN" . str_pad($this->db->insert_id(), 8, 0, STR_PAD_LEFT);

                    $this->db->set(["orders" => "orders+1"]);
                    $this->db->where('id', $id);
                    $this->db->update('items');

                    //update order numbers
                    $this->db->set("order_number", $order_number)->where("id", $order_insert_id)->update("orders");


                    $item_client = $this->db->where("id", $this->session->user['id'])->select("id,phone, email, name")->get("users")->row();
                    $item_owner = $this->db->where("id", $item->added_by)->select("id,phone, email, name")->get("users")->row();
                    $item_ordered = $this->db->where("id", $id)->select("id,name, identification_number")->get("items")->row();


                    if ($item->is_sales_default_account) {
                        $vendor_phone_number = $item->vendor_phone_number;
                        //save message to que for client
                        $message = array(
                            "message" => "Dear $item->vendor_name , An Order " . $order_number . " has been placed on your item (" . $item->name . ") ",
                            "client" => $this->session->user['id'],
                            "owner" => $item->added_by,
                            "item" => $id,
                            "status" => 0,
                            "messageid" => null,
                            "phone_number" => $vendor_phone_number,
                        );

                        //inset into que for client
                        $this->db->insert("message_outbox", $message);
                    }

                    //save message to que for client
                    $message = array(
                        "message" => "Dear $item_client->name , Your order of " . $item->name . " has been received. Order Number " . $order_number . " Please dial *211# to complete payment. ",
                        "client" => $this->session->user['id'],
                        "owner" => $item->added_by,
                        "item" => $id,
                        "status" => 0,
                        "messageid" => null,
                        "phone_number" => $item_client->phone,
                    );

                    //inset into que for client
                    $this->db->insert("message_outbox", $message);

                    //insert into que for the application vendor
                    $message['message'] = "Dear $item_owner->name, You have a pending order for  $item_ordered->name -  $order_number, please login and  take action. ";
                    $message['phone_number'] = $item_owner->phone;

                    $this->db->insert("message_outbox", $message);

                    //send mail to vendor & client
                    $this->saveClientVendorOrderNotificationEmail($order_insert_id);

                    //record initial payment, this since been added to app client only after approval
                    /*record initial payment, this since been added to app client only after approval
                    $payment_balance = array(
                        "amount" => $item->price * $order_dets['number_of_days'],
                        "client" => $this->session->user['id'],
                        "owner" => $item->added_by,
                        "owner_name" => $item->owner,
                        "item" => $order_dets['item'],
                        "order_number" => $order_number,
                        "total_paid" => 0,
                        "balance" => $item->price * $order_dets['number_of_days'],
                    );
                    $this->db->insert("payment_balances", $payment_balance);
                    */

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    } else {
                        $this->db->trans_commit();
                    }

                    $_SESSION['nav_url'] = null;
                    Redirect($this->link . "myorders");
                }

                $data['user'] = $_SESSION['user'];
                $data['id'] = $id;
                $data['item'] = $item;

                $this->load_view("order_item", $data);
            }


        }

    }

    function testRegistrationcomplete()
    {
        $this->load_view("registration_complete");
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
                $vendor_account_gl['momo_collection_number'] = $input_data['phone'];
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

        $form_value = array(
            "first_name" => set_value('first_name'),
            "last_name" => set_value('last_name'),
            "email" => set_value('email'),
            "phone" => set_value('phone'),
            "location" => set_value('location'),
            "alternative_phone" => null,
            "admin_password" => set_value('admin_password'),
            "confirm_password" => set_value('confirm_password'),

        );

        if ($action == "save") {
            $this->valid_form();


            if($this->form_validation->run() == FALSE){
                $this->load_view("registerclient");

            }
            else{
                echo 'valid';
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
            }



        } elseif ($action == "add") {
            $this->error = $error;
            echo '  this is else if';
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

            $this->load_view("registerclient",$form_value);

        } else {
            echo 'this is else';
            Redirect($this->link . 'clientregistration/add');
        }


    }

    public function valid_form(){
        $this->form_validation->set_rules('first_name','first_name','required');
    }

    //type can be subcategory (sbc) or offset,
    //type_value can be subcategory id or offset value
    public function category($id=null, $type=null, $type_value = null,$pg_value_when_subcategory_is_Set=null)
    {
        if(!$id) Redirect();

        $this->limit = 10;
        $offset = 0;
        $subcategory=$type==='sbc'?$type_value:null;
        $pagination_offset=$type==='pg'?$type_value:$offset;
        $subCategoryDetails=null;

        //if the value of $pg_value_when_subcategory_is_Set is not null
        //then pagination on subcategories is being implemented
        if($pg_value_when_subcategory_is_Set){
            $pagination_offset=$pg_value_when_subcategory_is_Set;
        }

        //pick filter content via post method
        $filter = $this->input->post();

        //get the category details from the database
        $category_by_id = $this->db->where(['id' => $id])->get("categories")->row();
        if (!$category_by_id) {
            Redirect(base_url());
        }

        $this->db->select("id, name, verified, color, category as cat, size, availability, DATE_FORMAT(date_added, '%M %D, %Y %H:%i:%s') as date, (select name from categories where id=items.category) as category, (select name from price_points where id=items.price_point) as price_point, price, front_view,brief_description")
            ->from("items");

        if (count($filter) > 0) {
            if ($filter['min_price']) {
                $this->db->where("price>" . $filter['min_price']);
            }

            if ($filter['max_price']) {
                $this->db->where("price<" . $filter['max_price']);
            }

            if ($filter['name']) {
                $this->db->where("name like '%" . $filter['name'] . "%'");
            }
        }

        if ($subcategory) {
            $this->db->where(["sub_category" => $subcategory]);
        }

        $this->db->where(['category' => $id]);
        $this->db->where("status", 1);
        $items_many = $this->db->limit($this->limit, $pagination_offset)->order_by("date_added", "desc")->get()->result_array();
        $items_many = $this->stripHtmlTagsFromStringOrAssociativeArray($items_many);


        //build the pagination link
        $pagination_link="category/" . $id.'/pg/';

        //get the total items in the category
        // factor in the presence of a subcategory
        if ($subcategory) {
            $this->db->where(["sub_category" => $subcategory]);
            $pagination_link="category/" . $id.'/sbc/'.$subcategory.'/';
        }

        $total = $this->db->where(['category' => $id])->select("count(*)  as count ")->from("items")->get()->row()->count;
        $pagination = $this->pagination($pagination_link, $total, $this->limit);

        $category_name = $category_by_id->name;

        //get subcategory name
        if($subcategory){
            $subCategoryDetails=$this->getSubCategoryById($subcategory);
        }

        $data['category'] = $id;
        $data['category_name'] = $category_name;
        $data['sub_category_name'] = $subCategoryDetails!=null?$subCategoryDetails->name:null;
        $data['sub_category'] = $subCategoryDetails!=null?$subCategoryDetails->id:null;
        $data['items'] = $items_many;
        $data['pagination'] = $pagination;
        $this->load_view("view_categories", $data);

    }

    public function pagination($link, $total, $per_page)
    {
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = true;
        $config['last_link'] = true;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = 'PREVIOUS';
        $config['prev_tag_open'] = '<button class="btn btn-danger btn-sm">';
        $config['prev_tag_close'] = '</button>';
        $config['next_link'] = 'next';
        $config['next_tag_open'] = '<button class="btn btn-sm btn-success">';
        $config['next_tag_close'] = '</button>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['base_url'] = $this->link . $link;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    public function items($action = null, $id = null)
    {

        if (!$id) {
            Redirect(base_url() . "Index/#latestItems");
        }

        $filter = $this->input->post();

        $item = $this->db->select("i.*,(select name from price_points where id=i.price_point) as price_point, (select name from users where id=i.added_by) as owner, (select name from categories where id=i.category) as category_name, (select name from sub_categories where id=i.sub_category) as sub_category_name")
            ->from("items i")
            ->where(['i.id' => $id])
            ->get()
            ->result_array();

        $items = $this->stripHtmlTagsFromStringOrAssociativeArray($item);

        $related = $this->db->select("i.*")
            ->from("items i")
            ->where(["i.category" => $item ? $item[0]["category"] : "rand(i.category)", "i.id <>" => $id])
            ->limit(6, 0)
            ->order_by("rand()")
            ->get()
            ->result_array();

        $related = $this->stripHtmlTagsFromStringOrAssociativeArray($related);

        $comments = $this->db
            ->where("item", $id)
            ->select("i.*")
            ->from("comments i")
            ->limit(6, 0)
            ->order_by("rand()")
            ->get()
            ->result_array();


        if ($action == "comment") {
            $comment = array(
                "comment" => htmlentities(trim($this->input->post("comment"))),
                "email" => trim($this->input->post("email")),
                "item" => $id
            );

            $this->db->insert("comments", $comment);
            $this->saveLog("comments", "commented", false, $this->db->insert_id());
            Redirect($this->link . "items/view/" . $id);
        }

        $data['item'] = $item[0];
        $data['comments'] = $comments;
        $data['related'] = $related;
        $data['id'] = $id;

        $data['og_description'] = array(
            "url" => $this->link . "items/view/" . $item[0]['id'],
            "image" => base_url() . "items/" . $item[0]['front_view'],
            "title" => $item[0]['name'] . " @ " . number_format($item[0]['price']) . " - " . $item[0]['category_name'],
            "description" => strip_tags($item[0]['brief_description']),
        );

        $this->load_view("view_item_details", $data);
    }

    public function account()
    {
    }

    public function login($action = null)
    {
        $error = null;

        if ($action == "verify") {
            $email = htmlentities($this->input->post("email"));
            $password = sha1($this->input->post("password"));
            $userlogin = array("u.email" => $email, "u.password" => $password);

            $user = $this->db->select("u.*,app.user_type,app.level")
                ->from("users u")
                ->join("app_user_type app", "app.id=u.registration_type")
                ->where($userlogin)
                ->get()
                ->result_array();

            if (count($user) == 0) {
                $error = "Email or Password incorrect";
            } else {
                $user = $user[0];
                if ($user['approved'] == 0) {
                    $error = "You have not approved your account, please check your email address or contact support  for help";
                } else {

                    $user_role = $user['registration_type'] == $this->sys_user ? $user['role'] : null;
                    $session_user = array(
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "phone" => $user['phone'],
                        "user_type" => $user['user_type'],
                        "role" => $user_role,
                        "registration_type" => $user['registration_type'],
                    );

                    $this->session->set_userdata(["user" => $session_user]);
                    $this->saveLog("users", "logged in", false, $user['id']);
                    $user_type = $user['user_type'];

                    if ($this->session->nav_url && $user_type == "client") {
                        Redirect($this->session->nav_url);
                    } else if ($user_type == "vendor") {
                        Redirect(base_url() . "AppClient");
                    } elseif ($user_type == "sys") {
                        Redirect(base_url() . "AppAdmin");
                    } elseif ($user_type == "client") {
                        Redirect(base_url() . "Index");
                    }
                }
            }
        }

        $data['error'] = $error;
        $this->load->view($this->viewFolder . "login", $data);
    }

    public function searchItems($offset = null)
    {
        $post_filter = $this->input->post();
        $name = $this->helper->cleanInput(@$post_filter['name'] ?: null);
        $low_price = $this->helper->cleanInput($post_filter['min_price']);
        $highest_price = $this->helper->cleanInput($post_filter['max_price']);

        $filter = [];

        if ($name) {
            array_push($filter, "name like '%$name%'");
        }
        if ($low_price) {
            array_push($filter, "price>$low_price");
        }
        if ($highest_price) {
            array_push($filter, "price<$highest_price");
        }

        $lengthfilter = count($filter);
        $filterString = ' where status=1';

        if ($lengthfilter == 1) {
            $filterString .= " and  " . $filter[0];
        } elseif ($lengthfilter > 1) {
            $filterString .= $filter[0];

            for ($i = 1; $i < $lengthfilter; $i++) {
                $filterString .= " and " . $filter[$i];
            }
        }

        $itemsList = $this->db
            ->query("select id, name, color, category as cat, size, DATE_FORMAT(date_added, '%M %D, %Y %H:%i:%s') as date,availability,verified,
              (select name from categories where id=items.category) as category, 
              (select name from price_points where id=items.price_point) as price_point, 
              price, front_view,brief_description from items 
              $filterString
              order by price desc,name desc limit $this->limit ")
            ->result_array();

        $itemsList = $this->stripHtmlTagsFromStringOrAssociativeArray($itemsList);
        $data['items'] = $itemsList;


        $this->load_view("search_items", $data);
    }

    public function confirmaccount($approvalstring)
    {
        if ($this->db->where("approval_code", $approvalstring)->set("approved", 1)->update("users")) {
            Redirect(base_url() . "Index/login");
        }

    }

    public function payments($order_number = null)
    {
        $this->checkLogin();

        $filter = $this->input->post();
        $filter_information = $this->helper->load_params($filter);

        $this->db->select("
        t.id,t.order_number,t.transactionid,t.ext_transaction_id,t.payee_number, t.amountpayable, t.amount,t.channel_name as channel,
         t.payment_date,
        date(t.date) as date");
        $this->db->from("transaction_history t");
        $this->db->where(array_merge(["client" => $this->session->user['id'], "status" => 1], $filter_information));

        $transactions = $this->db->limit($this->limit)->order_by("t.date desc")->get()->result_array();

        $table = $this->lib->DrawTable($transactions, null);
        $total = $this->db->count_all("transaction_history");
        $pagination = $this->pagination("payments/", $total, $this->limit);

        $this->title = "Pangisa - My payments";
        $this->form_header_link = null;
        $this->form_header_title = null;
        $this->page_title = "Payments";
        $this->load_view_simple($table, $pagination);
    }

    public function load_view_simple($info)
    {
        $data['view'] = null;
        $data['title'] = $this->title;
        $data['error'] = $this->error;
        $data['active_link'] = $this->active_link;
        $this->load->view($this->viewFolder . "user_portal_header", $data);
        $this->load->view($this->viewFolder . "render_page", ["info" => $info, "error" => $this->error]);
        $this->load->view($this->viewFolder . "footer");
    }

    public function how()
    {
        $this->load_view("how_it_works");
    }

    public function faq()
    {
        $this->load_view("frequently_asked");
    }

    public function AjaxSearchItemByName()
    {
        $post_filter = $this->input->post();
        $name = $this->helper->cleanInput($post_filter['item_name']);
        $low_price = $this->helper->cleanInput($post_filter['min_price']);
        $highest_price = $this->helper->cleanInput($post_filter['max_price']);
        $item_category = $this->helper->cleanInput($post_filter['item_category']) ?: null;

        $filter = [];

        if ($name) {
            array_push($filter, "name like '%$name%'");
        }
        if ($low_price) {
            array_push($filter, "price>$low_price");
        }
        if ($highest_price) {
            array_push($filter, "price<$highest_price");
        }

        if ($item_category <> null) {
            array_push($filter, "category=$item_category");
        }

        $lengthfilter = count($filter);
        $filterString = '';

        if ($lengthfilter == 1) {
            $filterString .= " where " . $filter[0];
        } elseif ($lengthfilter > 1) {
            $filterString .= " where " . $filter[0];

            for ($i = 1; $i < $lengthfilter; $i++) {
                $filterString .= " and " . $filter[$i];
            }
        }

        $itemsList = $this->db
            ->query("select id, name, color, category as cat, size,
              (select name from categories where id=items.category) as category, 
              (select name from price_points where id=items.price_point) as price_point, 
              price, front_view,brief_description from items 
              $filterString
              order by price desc,name desc limit 20 ")
            ->result_array();

        if (count($itemsList) <= 0) {
            echo $result = "<div class='alert alert-info' style='text-align: left;'>Your search for $name returned no result</div>";
        } else {
            $result = "<div style='text-align: left;'><ul>";

            foreach ($itemsList as $item):
                $result .= "<a href='" . base_url() . "Index/items/view/" . $item['id'] . "'>
            <li style='padding:5px; margin:4px; border-bottom: 1px solid #255; display: inline-block; '>
            " . $item['name'] . " UGX " . number_format($item['price']) . " " . $item['price_point'] . "</li></a>";
            endforeach;
            $result .= "<ul></div>";

            echo $result;
        }


    }

    public function terms_and_conditions()
    {
        $this->title = "Pangisa - Terms And Conditions";
        $this->load_view("../terms_and_conditions", null);
    }

    public function AjaxLoginSimple()
    {
        $error = 0;

        $email = htmlentities($this->input->post("email"));
        $password = sha1($this->input->post("password"));
        $userlogin = array("u.email" => $email, "u.password" => $password);

        $user = $this->db->select("u.*,app.user_type")
            ->from("users u")
            ->join("app_user_type app", "app.id=u.registration_type")
            ->where($userlogin)
            ->get()
            ->result_array();

        if (count($user) == 0) {
            $error = "Username or Password is incorrect";
        } else {
            $user = $user[0];

            if ($user['approved'] == 0) {
                $error = "You have not approved your account, please check your email address or contact support  for help";
            } elseif ($user['status'] == 0) {
                $error = "Your Account " . $user['user_code'] . " was suspended, Please contact the system administrator for more information";
            } else {

                $user_type = $user['user_type'];

                if ($this->session->user['id']) {
                    $error = "You are Already logged in as " . $this->session->user['user_type'] . " , Please logout and login again as client";
                } elseif ($user_type == "client") {
                    $session_user = array(
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "phone" => $user['phone'],
                        "user_type" => $user['user_type'],
                        "role" => $user['registration_type'] == $this->sys_user ? $user['role'] : null,
                        "registration_type" => $user['registration_type'],
                    );

                    $this->session->set_userdata(["user" => $session_user]);
                    $this->saveLog("users", "logged in", false, $user['id']);

                    echo json_encode(new Response(0, "Login success", $this->session->nav_url));

                } else {
                    $error = "Your User Type (" . ($user_type == 'sys' ? 'systems Administrator' : 'Vendor') . ")doesnt allow ordering items off Pangisa ";
                }

            }
        }

        echo $error;
    }

    //random password generator
    function password_generate($chars)
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz!@#$%^&*()_+-=?';
        return substr(str_shuffle($data), 0, $chars);
    }

    public function acceptpasswordreset($id, $email)
    {
        $this->db->where(["id" => $id, 'email' => urldecode($email)]);
        $this->db->where("temp_password is not null");
        $user = $this->db->get("users")->result_array();

        if (count($user) == 1) {
            $user = $user[0];
            $pwd = $user['temp_password'];
            $this->db->where(["id" => $user['id']])->set(['password' => $pwd, "temp_password" => null])->update("users");
            Redirect(base_url() . $this->ctrl_name . "/login");
        } else {
            echo "<p>This ink is invalid, Please Reset your password again and try </p>";
            sleep(1);
            Redirect(base_url() . $this->ctrl_name);
        }

    }

    public function forgotpassword($action = null)
    {
        $error = null;
        $title = "Pangisa - Forgot Password";;

        if ($this->session->user['id']) {
            Redirect($_SERVER['HOST']);
        } elseif ($action == "verify") {
            $email = $this->input->post('email');
            $mode = $this->input->post('mode');

            $user = $this->db->where(['email' => $email])->get("users")->row();

            if (!$user) {
                $error = "Account with given email not found";
                $data['error'] = $error;
                $data['title'] = $title;
                $this->load->view("forgotpassword", $data);
                return;

            } else {
                $new_password = $this->password_generate(10);
                $new_password_hash = sha1($new_password);
                $this->db->where(["id" => $user->id])->set(['temp_password' => $new_password_hash])->update("users");

                if ($mode == "sms") {
                    $message = array(
                        "message" => "Dear $user->name , Your new password is  " . $new_password . ". Please click " . base_url() . $this->ctrl_name . "/acceptpasswordreset/" . $user->id . "/" . urlencode($user->email),
                        "client" => $user->id,
                        "owner" => null,
                        "item" => null,
                        "status" => 0,
                        "messageid" => null,
                        "phone_number" => $user->phone,
                    );

                    //inset into que for client
                    $this->db->insert("message_outbox", $message);
                } else {
                    $SUBJECT = "PANGISA - Account Password Reset";
                    $emailResponseToUser = "
                        <div >
                        <p>Dear $user->name</p>, <a>Your new password is <b style='background: #255; padding:20px; font-weight:bold; color:#FFA000'>$new_password</b>. 
                            Please Remember to Reset it just after you login. 
                            To use the given password please click the following link <a href='" . base_url() . $this->ctrl_name . "/acceptpasswordreset/" . $user->id . "/" . urlencode($user->email) . "' target='_top'> <button>Confirm Password Reset</button></a> </p>
                        <br><br><p><b>NOTE: Keep your passwords a secret</b></p> 
                        </div>
                    ";
                    $emailObjectForDatabaseSave['email_address'] = $user->email;
                    $emailObjectForDatabaseSave['client'] = $user->id;
                    $emailObjectForDatabaseSave['body'] = $emailResponseToUser;
                    $emailObjectForDatabaseSave['action'] = "RESET_PASSWORD";
                    $emailObjectForDatabaseSave['subject'] = $SUBJECT;
                    $emailObjectForDatabaseSave['status'] = 0;

                    $this->db->insert("email_outbox", $emailObjectForDatabaseSave);
                }

                $error = "Your " . $mode . " has been sent, please login with your new password";
                $data['error'] = $error;
                $data['title'] = $title;
                $this->load->view("forgotpassword", $data);

            }


        } else {
            $data['error'] = $error;
            $data['title'] = $title;
            $this->load->view("forgotpassword", $data);
        }
    }

    //save to database
    private function saveToDatabase($table, $data)
    {
        $insert_id = null;
        $this->db->trans_begin();
        $this->db->insert($table, $data);
        $insert_id = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        return $insert_id;
    }

    //save the notifications for sending to clients and vendor
    private function saveClientVendorOrderNotificationEmail($orderId)
    {
        $order = $this->db->where('id', $orderId)->get('orders')->row();
        $item = $this->db->where('id', $order->item)->get('items')->row();
        $client = $this->db->where('id', $order->client)->get('users')->row();
        $vendor = $this->db->where('id', $order->owner)->get('users')->row();

        $clientNotification = "
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #607D8B; color: #FFC107; font-weight: 700'>
                    Dear " . $client->name . ", Your Order for " . $item->name . " has been received
                </section>
                <br>
                <table cellpadding='5' cellspacing='0'>
                    <tr>
                        <td>Order Number</td>
                        <td>" . $order->order_number . "</td>
                    </tr>
                    <tr>
                        <td>Item</td>
                        <td>" . $item->name . "</td>
                    </tr>
                    <tr>
                        <td>Usage Description</td>
                        <td>" . $order->usage_description . "</td>
                    </tr>
                    <tr>
                        <td>Pick Up Location</td>
                        <td>" . $item->pick_up_location . "</td>
                    </tr>
                    <tr>
                        <td>Number of Days</td>
                        <td>" . $order->number_of_days . "</td>
                    </tr>
                    <tr>
                        <td>Place of Use</td>
                        <td>" . $order->place_of_use . "</td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td>" . $order->quantity . "</td>
                    </tr>
                    <tr>
                        <td>Rate</td>
                        <td>" . number_format($item->price) . "</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td>" . number_format($order->order_amount) . "</td>
                    </tr>
                </table>
                <br>
                <hr/>
            
                You can view the status of your order by clicking the following link <a href='" . base_url() . "Customer/Orders/view/" . $order->id . "'>
                " . base_url() . "Customer/Orders/view/" . $order->id . "</a> Once your order is approved, you will be notified to Proceed with
                payments
            
            </div>
        ";


        //save for client
        $clientEmailNotification = [];
        $clientEmailNotification['client'] = $client->id;
        $clientEmailNotification['owner'] = $vendor->id;
        $clientEmailNotification['action'] = 'PLACED_ORDER';
        $clientEmailNotification['body'] = $clientNotification;
        $clientEmailNotification['email_address'] = $client->email;
        $clientEmailNotification['status'] = 0;
        $clientEmailNotification['subject'] = $order->order_number . " has been received";
        $this->db->insert("email_outbox", $clientEmailNotification);

        $vendorNotification = "
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #607D8B; color: #FFC107; font-weight: 700'>
                    Dear " . $vendor->name . ", You have a pending Order for " . $item->name . "
                </section>
                <br>
                <table cellpadding='5' cellspacing='0'>
                    <tr>
                        <td>Order Number</td>
                        <td>" . $order->order_number . "</td>
                    </tr>
                    
                    <tr>
                        <td>Client</td>
                        <td>" . $client->name . "</td>
                    </tr>
                    
                    <tr>
                        <td>Order Date</td>
                        <td>" . $order->date . "</td>
                    </tr>
                    <tr>
                        <td>Item</td>
                        <td>" . $item->name . "</td>
                    </tr>
                    <tr>
                        <td>Usage Description</td>
                        <td>" . $order->usage_description . "</td>
                    </tr>
                    <tr>
                        <td>Pick Up Location</td>
                        <td>" . $item->pick_up_location . "</td>
                    </tr>
                    <tr>
                        <td>Number of Days</td>
                        <td>" . $order->number_of_days . "</td>
                    </tr>
                    <tr>
                        <td>Place of Use</td>
                        <td>" . $order->place_of_use . "</td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td>" . $order->quantity . "</td>
                    </tr>
                    <tr>
                        <td>Rate</td>
                        <td>" . number_format($order->item_price_at_order) . "</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td>" . number_format($order->order_amount - $order->markup_at_order) . "</td>
                    </tr>
                </table>
                <br>
                <hr/>
            
                You can  Approve or Reject the Order by clicking the following link <a href='" . base_url() . "AppClient/orders/view/" . $order->id . "'>
                " . base_url() . "AppClient/orders/view/" . $order->id . "</a> 
            
            </div>
        ";

        //save for vendor
        $vendorEmailNotification = [];
        $vendorEmailNotification['client'] = $client->id;
        $vendorEmailNotification['owner'] = $vendor->id;
        $vendorEmailNotification['action'] = 'PLACED_ORDER';
        $vendorEmailNotification['body'] = $vendorNotification;
        $vendorEmailNotification['email_address'] = $vendor->email;
        $vendorEmailNotification['status'] = 0;
        $vendorEmailNotification['subject'] = $order->order_number . " - You have a pending order";
        $this->db->insert("email_outbox", $vendorEmailNotification);
    }

    public function ContactUsMobileApp()
    {
        $this->load->view($this->viewFolder . "ContactUsMobileApp");
    }
}
