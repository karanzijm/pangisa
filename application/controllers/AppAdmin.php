<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/18/2018
 * Time: 6:04 PM
 */

include "Response.php";
include APPPATH . "controllers/Ballistics.php";
include APPPATH . "libraries/MtnMomoHelper.php";

class AppAdmin extends CI_Controller
{
    private $ctrl_name = 'AppAdmin';
    private $error = null;
    private $link = null;
    private $title = null;
    private $form_header_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $filter_submit_link = null;
    private $limit = 50;
    private $sys_user = 4;
    private $client_user = 6;
    private $vendor_user = 5;
    private $portal = "admin_portal/";
    private $helper = null;
    private $pagination_display = null;
    private $userId = null;
    private $backLink = null;
    private $called_link = null;
    private $unique_trace_id = null;

    public function __construct()
    {
        parent::__construct();
        $_SERVER['CI_ENV'] = "production";
        $this->load->library("Lib");
        $this->load->model("BusinessModel");
        $this->load->library("user_agent");
        $this->link = base_url() . $this->ctrl_name . '/';
        $this->helper = new Ballistics();
        $this->userId = $this->session->user['id'];
        $this->backLink = $this->agent->referrer();

        $this->called_link = $this->uri->uri_string();
        $this->unique_trace_id = strtoupper(uniqid("PANGISA-ER"));

        $_SESSION['called_link'] = $this->called_link;
        $_SESSION['unique_trace_id'] = $this->unique_trace_id;

        set_error_handler(array(&$this->helper, "my_error_handler"));
    }

    public function index()
    {

        $this->checkLogin();
        $data = [];

        $users = $this->db->query("select registration_type as type, count(*) as total from users group by registration_type")->result_array();

        foreach ($users as $user):
            if ($user['type'] == $this->sys_user) $data['users'] = $user['total'];
            if ($user['type'] == $this->client_user) $data['clients'] = $user['total'];
            if ($user['type'] == $this->vendor_user) $data['companies'] = $user['total'];
        endforeach;

        $orders = $this->db->count_all('orders');

        $data['total_orders'] = $orders;


        $this->title = "PANGISA - DASHBOARD";
        $this->page_title = "Dashboard";
        $this->loadView($this->portal . "dashboard", $data);

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

    //payments to pangisa
    public function payments_to_pangisa($offset = 0)
    {
        $this->checkLogin();
        $paymentsInOwing = null;

        $filter_items = $this->input->post();
        $expected = ["reference_number", "vendor_name", "status"];

        $this->db
            ->select("pc.id,pc.amount, net_amount, date(pc.date) as date, pc.paid, pc.balance,pc.reference_number, pc.order_amount,  pc.last_transaction_date, pc.reason")
            ->select("(select name from users where id=pc.owner) as vendor")
            ->from("pangisa_collections pc");

        if (count($filter_items) > 0) {
            if ($filter_items['reference_number']) {
                $this->db->where("reference_number", $filter_items['reference_number']);
            }

            if ($filter_items['status']) {
                $this->db->where("paid", $filter_items['status']);
            }

            if ($filter_items['enddate']) {
                $this->db->where("date(date)>=date(" . $filter_items['enddate']);
            }

            if ($filter_items['startdate']) {
                $this->db->where("date(date)<=date(" . $filter_items['startdate']);
            }
        }

        $paymentsInOwing = $this->db->limit($this->limit, $offset)
            ->get()
            ->result_array();


        $total = $this->db->select("count(*)  as count ")->from("pangisa_collections")->get()->row()->count;
        $this->pagination_display = $this->pagination("payments_to_pangisa/", $total, $this->limit);

        $data['paymentsInOwing'] = $paymentsInOwing;
        $this->loadView($this->portal . "view_pangisa_collections", $paymentsInOwing);
    }


    public function payments($offset = null)
    {
        $this->checkLogin();
        $this->page_title = "PAYMENTS";
        $filtet_string = $this->input->post();

        if (count($filtet_string) > 0):
            $ordernumber = $filtet_string['filter_search_item'];
            $startdate = $filtet_string['startdate'];
            $enddate = $filtet_string['enddate'];

            $filter_string = " order_number like '%" . $ordernumber . "%' ";
            if ($startdate) $filter_string .= " and date(last_transaction_date)>='$startdate' ";
            if ($enddate) $filter_string .= " and date(last_transaction_date)<='$enddate'";
            $this->db->where($filter_string);
        endif;

        $payments = $this->db->select("p.*, u.name as client, i.name as item, i.identification_number")
            ->from("payment_balances p")
            ->join("users u", "u.id=p.client")
            ->join("items i", "i.id=p.item")
            ->limit($this->limit, $offset)
            ->order_by("p.last_transaction_date", "desc")
            ->get()
            ->result_array();

        $this->filter_submit_link = $this->link . "payments";
        $this->loadView($this->portal . "payments", $payments);
    }

    public function view_transaction_details($txid)
    {
        $this->checkLogin();
        $this->db->select("
        t.order_number as 'Order Number',
        t.client_name as 'Client Name',
        t.transactionid as 'Transaction Id',
        t.ext_transaction_id as 'external Transaction Id',
        t.payee_number as 'Payee Number', 
        t.amountpayable as 'Amount Payable', 
        t.min_amount as 'Min Payment Amount',
        t.amount as 'Amount Paid',
        t.channel_name as 'Channel Name',
        t.item_reg_number as 'item Identification Number', 
        t.owner_name as 'Vendor Name', 
        t.payment_date as 'Date of Payment', t.status as Status,
        date(t.date) as Date,
        t.narration as Narration");
        $this->db->from("transaction_history t");
        $this->db->where(["id" => $txid]);
        $transactions = $this->db->get()->result_array();

        $result_string = "<table class='table lib_table table-hover'>";

        foreach ($transactions[0] as $key => $value) {
            $result_string .= "<tr><td>" . $key . "</td><td>" . $value . "</td>";
        }

        $result_string .= "</table>";
        echo $result_string;
    }

    public function transactions($offset = null)
    {
        $this->checkLogin();

        $filter = $this->input->post();
        $filter_information = $this->helper->load_params($filter);

        if (count($filter_information) > 0) {
            $filter_params = [];
            $filter_params[$filter_information['search_mode']] = $filter_information['search_key'];
            $filter_information = $filter_params;
        }

        $this->db->select("
        t.id,t.order_number,t.client_name as client,t.transactionid,t.ext_transaction_id,t.payee_number, t.amountpayable, t.min_amount,t.amount,t.channel_name,t.item_reg_number, 
        t.owner_name as owner, t.payment_date, t.status,
        date(t.date) as date,t.item_reg_number,
        t.narration");
        $this->db->from("transaction_history t");
        $this->db->where($filter_information);

        $transactions = $this->db->limit($this->limit, $offset)->order_by("t.date desc")->get()->result_array();

        $total = $this->db->count_all("transaction_history");
        $pagination = $this->pagination("transactions/", $total, $this->limit);

        $this->title = "Pangisa - Transactions";
        $this->form_header_link = $this->link . "addItems";
        $this->form_header_title = null;
        $this->page_title = "Transactions";

        $data['transactions'] = $transactions;
        $data['pagination'] = $pagination;

        $this->loadView($this->portal . "transactions_history", $data);
    }

    private function loadSimpleView($information, $pagination = null)
    {
        $data['title'] = $this->title ? $this->title : "Pangisa";
        $data['view'] = "render_view";
        $data['page_title'] = $this->page_title;
        $data['information'] = $information;
        $data['error'] = $this->error;
        $data['pagination'] = $pagination;
        $data['form_header_title'] = $this->form_header_title;
        $data['form_header_link'] = $this->form_header_link;
        $data['filter'] = $this->lib->BuildFilter($this->current_page_filter, $this->filter_submit_link);
        $portal = "admin_portal/";


        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($portal . "home", array("menu" => $this->buildSideMenu()));
    }

    public function checkLogin()
    {
        $usertype = $this->session->user['user_type'];

        if (!$this->session->user['id'] || $usertype <> "sys") {
            Redirect(base_url() . "Index/login");
        }
    }

    public function orders($action = null, $id = null, $offset = 0)
    {

        $this->checkLogin();
        $db_offset = $action == "page" ? $id : 0;

        if ($action == "approve") {
            $update = array(
                "pick_up_date" => $this->input->post("pick_up_date"),
                "comment" => $this->input->post("comment"),
                "approved" => 1,
                "approval_date" => date("y-m-d h:i:s")
            );

            $this->db->where("id", $id)->update("orders", $update);
            $this->saveLog("orders", "approved", false, $id);

            Redirect($this->link . "orders/view/" . $id);
        }

        if ($action == "reject") {
            $update = array(
                "comment" => $this->input->post("comment"),
                "approved" => 2,
                "approval_date" => date("y-m-d h:i:s")
            );


            $this->db->where("id", $id)->update("orders", $update);
            $this->saveLog("orders", "rejected", false, $id);

            Redirect($this->link . "orders/view/" . $id);
        }

        if ($action == "view") {
            $this->db->where("o.id", $id);
            $this->db->select("
                    o.id as orderId,o.order_number,o.number_of_days,o.place_of_use,o.date as orderDate,o.usage_description,o.approved as orderStatus,o.comment as orderComments,o.approval_date as orderApprovalDate,o.user_cancel as orderUserCancel,o.pick_up_date as orderPickUpDate,o.admin_aproval as orderAdminApproval,o.admin_aproval_date as orderAdminApprovalDate,o.order_number as orderNumber,o.order_amount as orderAmount, o.return_date  as orderReturnDate,o.item_price_at_order as orderPricePerUnit,o.order_closed_successfully as orderClosedStatus,
                    i.id as itemId,i.name as itemName,i.category as itemCategory,i.color as itemColor,i.size as itemSize,i.rear_view,i.front_view,i.side_view,i.identification_number,i.brief_description,i.year_of_make,i.price,i.pick_up_location,i.price_point,i.features,i.views,i.orders,i.brand,i.item_number,
                    u.name as owner, u.id as ownerId");
            $this->db->from("orders o");
            $this->db->join("items i", "i.id=o.item");
            $this->db->join("users u", "u.id=o.owner");
            $orderToView = $this->db->get()->row();

            $paymentBalance = $this->db->where(['order_number' => $orderToView->order_number])->get('payment_balances')->row();

            if (!$orderToView) {
                $this->error = "Order with given Id was not found";
            } else {
                $orderDetailsData['order'] = $orderToView;
                $orderDetailsData['payment_balances'] = $paymentBalance;
                $this->loadView($this->portal . "order_details", $orderDetailsData);
                return;
            }
        }

        $orders = $this->db
            ->select("o.id, o.order_number as number, u.name as client, u.phone, i.name,  i.identification_number as Number,i.price, 
                concat( o.number_of_days,' Days') as 'Period', format((o.number_of_days*i.price),0) as Amount, o.place_of_use as 'Place of use', 
                date(o.date) as Date, 
                (select name from users where id=o.owner) as owner,
                (select case when o.approved=1 then 'Approved'  when o.approved=0 then 'Pending' when o.approved=3 then 'User Canceled' else 'Rejected' end) as status 
                ")
            ->from("orders o")
            ->join("items i", "i.id=o.item")
            ->join("users u", "u.id=o.client")
            ->limit($this->limit, $db_offset)
            ->order_by("o.date desc")
            ->get()->result_array();


        $actions = array(
            "<i class='fa fa-eye'></i>" => $this->link . "orders/view",
        );

        $table = $this->lib->DrawTable($orders, $actions);

        $total = $this->db->count_all("orders");
        $pagination = $this->pagination("orders/page/", $total, $this->limit);

        $this->title = "Pangisa - Orders";
        $this->form_header_link = $this->link . "addItems";
        $this->form_header_title = null;
        $this->page_title = "View Orders";
        $this->loadSimpleView($table, $pagination);

    }

    public function partners($action = null, $id = null, $offset = 0)
    {
        $this->checkLogin();
        $this->page_title = "Vendors";
        $companyList = $this->db->where("registration_type", $this->vendor_user)->select("id,name")->from("users")->get()->result_array();
        $filter = array(["name" => "Partner Name", "variable" => "id", "data" => $companyList]);
        $this->filter_submit_link = $this->link . "partners";
        $this->current_page_filter = $filter;

        $post_company_filter = $this->input->post();
        $filter_string = null;

        if ($action == "suspend") {
            $this->db->where(["id", $id])->update("users", ['status' => 0]);
            $this->saveLog("users", "suspended", false, $id);
            Redirect($this->link . "/partners");
        } elseif ($action == "unsuspend") {
            $this->db->where(["id", $id])->update("users", ['status' => 1]);
            $this->saveLog("users", "un suspended", false, $id);
            Redirect($this->link . "/partners");
        }


        if (count($post_company_filter) > 0):
            $filter_item = $post_company_filter['filter_search_item'];
            $startdate = $post_company_filter['startdate'];
            $enddate = $post_company_filter['enddate'];

            $filter_string .= " name like '%$filter_item%' or phone like '%$filter_item%' or email like '%$filter_item%' or location='%$filter_item%' ";

            if ($startdate) $filter_string .= " and date(date)>='$startdate' ";
            if ($enddate) $filter_string .= " and date(date)<='$enddate'";

//            $filter_string= $filter_array;
        endif;

        if ($action == "display") {
            $offset = $id;
        }

        $companies = $this->BusinessModel->retrieve($this->vendor_user, $filter_string, 90, $offset);

        $total_partners = $this->db->where("registration_type", $this->vendor_user)->count_all("users");
        $pages = $this->pagination("partners/display/", $total_partners, $this->limit);
        $this->pagination_display = $pages;
        $this->loadView($this->portal . "partners", $companies);
    }

    public function clients($action = null, $id = null, $offset = null)
    {
        $this->checkLogin();
        $this->page_title = "Clients";
        $data = [];

        if ($action == "suspend") {
            $this->db->where(["id" => $id])->update("users", ['status' => 0]);
            $this->saveLog("users", "suspended", false, $id);
            Redirect($this->backLink);

        } elseif ($action == "unsuspend") {
            $this->db->where(["id" => $id])->update("users", ['status' => 1]);
            $this->saveLog("users", "un suspended", false, $id);
            Redirect($this->link . "clients");
        } elseif ($action == "view") {
            $clientObject = $this->db->where("id", $id)->get("users")->row();
            $paymentBalances = $this->db->select("pb.*,i.name")
                ->where("pb.client", $id)
                ->from("payment_balances pb")
                ->join("orders o", "pb.order_number=o.order_number")
                ->join("items i", "i.id=o.id")
                ->order_by("o.date desc")
                ->get()
                ->result_array();

            $data['client'] = $clientObject <> null ? $clientObject : null;
            $data['payment_balances'] = $paymentBalances;

            $this->loadView($this->portal . "view_client_details", $data);
            return;
        }

        $companyList = $this->db->where("registration_type", $this->client_user)->select("id,name")->from("users")->get()->result_array();
        $filter = array(["name" => "Client Name", "variable" => "id", "data" => $companyList]);
        $this->filter_submit_link = $this->link . "clients";
        $this->current_page_filter = $filter;
        $post_company_filter = $this->input->post();

        $companies = $this->BusinessModel->retrieve($this->client_user, ($post_company_filter ? $post_company_filter : []), $this->limit, $offset ? $offset : 0);

        $actions = array(
            "<i class='fa fa-eye'></i>" => $this->link . "clients/view",
            "<i class='fa fa-lock'></i>" => $this->link . "clients/suspend",
        );

        $companies = $this->lib->DrawTable($companies, $actions);
        $this->loadSimpleView($companies);
    }

    public function users($action = null, $id = null, $offset = null)
    {
        $this->checkLogin();
        $this->page_title = "System Administrators";

        if ($action == "add") {
            $this->loadView($this->portal . "add_new_user", null);
            return;
        }


        if ($action == "save") {
            $user = $this->input->post();
            $this->helper->cleanArrayInput($user);

            if ($user['password'] <> $user['confirm_password']) {
                $this->error = "Passwords are not the same";

                Redirect($this->link . "users/add");
            }

            $phone_number = $this->helper->checkPhoneNumberInternationalStandard($user['phone']);

            $user_account = array(
                "name" => $user['name'],
                "phone" => $phone_number,
                "location" => $user['location'],
                "alternative_phone" => null,
                "password" => sha1($user['password']),
                "registration_type" => $this->sys_user,
                "status" => 1,
                "role" => $user['role'] == null ? $user['role'] : "sys_user",
                "approved" => 0,
                "user_mail_created" => 0,
            );

            $this->db->insert("users", $user_account);
            $this->saveLog("users", "added", false, $this->db->insert_id());
            Redirect($this->link . "/users");
        }

        $usersList = $this->db->where("registration_type", $this->sys_user)->select("id,name")->from("users")->get()->result_array();

        if ($action == "block") {
            $this->userCan("block_user");
            $this->db->where(["id", $id])->set(['status' => 0])->update("users");
            $this->saveLog("users", "suspended", false, $id);
            Redirect($this->link . "/users");

        } elseif ($action == "unblock") {
            $this->userCan("block_user");
            $this->db->where(["id", $id])->update("users", ['status' => 1]);
            $this->saveLog("users", "un suspended", false, $id);
            Redirect($this->link . "users");
        }

        $filter = array(["name" => "User Name", "variable" => "id", "data" => $usersList]);
        $this->filter_submit_link = $this->link . "users";
        $this->current_page_filter = $filter;

        //check if there is post info
        $post_company_filter = $this->input->post();

        $companies = $this->BusinessModel->retrieve($this->sys_user, ($post_company_filter ? $post_company_filter : []), 90, 0);

        $this->loadView($this->portal . "users", $companies);
    }

    //vendor accounts
    public function vendor_collections_account($offset = 0)
    {
        $this->checkLogin();
        $vendor_accounts = $this->db->select("v.*, (select name from users where id=v.vendor_id) as vendor_name")->from("vendor_account_gl v")->order_by("v.balance", 'desc')->limit($this->limit, $offset)->get()->result_array();
        $data['vendor_accounts'] = $vendor_accounts;

        $total = $this->db->count_all("vendor_account_gl");
        $pagination = $this->pagination("vendor_collections_account/", $total, $this->limit);

        $data['pagination'] = $pagination;
        $this->loadView($this->portal . "vendor_account_display", $data);
    }

    //view liquidation requests
    public function liquidation_requests($action = null, $offset = 0)
    {
        $this->checkLogin();
        $responseData = [];
        $filter = $this->input->post() ?: [];

        try {
            //get a summary of the liquidation requests
            $liquidation_requests = $this->db->select("l.*,(select name from users where l.vendor_id=id) as name")->from('liquidation_requests l')->where($filter)->order_by("status", 'asc')->order_by("id", "asc")->limit($this->limit, $offset)->get()->result_array();
            $responseData['liquidation_requests'] = $liquidation_requests;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        $this->loadView($this->portal . "liquidation_requests", $responseData);
    }

    public function approve_liquidation_request($liquidation_request_id = null)
    {
        try {
            $liquidation_request = null;
            $response = null;

            if (!$liquidation_request_id) {
                echo "Liquidation ID cannot be null";
            } else {
                $input_data = $this->input->post();
                $status = $input_data['status'];
                $remarks = $input_data['comments'];

                if ($status == 2 && empty($remarks)) {
                    echo $status . " IF the Request is Rejected then you must supply a remark";
                    return;
                }

                //start transaction
                $this->db->trans_start();

                //update the status of the transaction in the vendor account gl table
                $LiquidationRequestUpdateObject = ["updated_by" => $this->userId, "status" => $status, 'comments' => $remarks];
                $this->db->where(['id' => $liquidation_request_id])->set($LiquidationRequestUpdateObject)->update("liquidation_requests");

                $liquidation_request = $this->db->where(['id' => $liquidation_request_id])->get("liquidation_requests")->row();

                //vendor details
                $vendor = $this->db->where(['id' => $liquidation_request->vendor_id])->limit(2)->get("users")->row();

                if ($status == 1) {
                    //create a debit transaction in transaction history
                    $debit_request_history = [];
                    $narration = /*"Withdraw of " . $liquidation_request->amount . " Ref# " . */
                        $liquidation_request->reference_number;
                    $transactionId = strtoupper(uniqid("DBT"));

                    $debit_request_history['payment_channel'] = 0;
                    $debit_request_history['transactionid'] = $transactionId;
                    $debit_request_history['channel_name'] = 'MTN_MOMO';
                    $debit_request_history['min_amount'] = $liquidation_request->amount;
                    $debit_request_history['amountpayable'] = $liquidation_request->amount;
                    $debit_request_history['client'] = $liquidation_request->vendor_id;
                    $debit_request_history['client_name'] = $vendor->name;
                    $debit_request_history['order_number'] = $liquidation_request->reference_number;
                    $debit_request_history['date'] = date('Y-m-d h:i:s');
                    $debit_request_history['status'] = 0;
                    $debit_request_history['narration'] = $narration;
                    $debit_request_history['customer_number'] = $liquidation_request->receiving_account;
                    $debit_request_history['payee_number'] = '256787269844';
                    $debit_request_history['payee_name'] = 'PANGI2.sp5';
                    $debit_request_history['amount'] = $liquidation_request->amount;
                    $debit_request_history['order_id'] = $liquidation_request->reference_number;
                    $debit_request_history['transaction_type'] = "disbursement";
                    $this->db->insert("transaction_history", $debit_request_history);
                    $transaction_history_insert_id = $this->db->insert_id();

                    //send the debit request to momo
                    $momo = new MtnMomoHelper();
                    $paymentResult = $momo->DepositToUserAccount($liquidation_request->receiving_account, $liquidation_request->amount, $narration, $transactionId, $transaction_history_insert_id);

                    $resultCode = $paymentResult->getResultCode();
                    $resultMessage = $paymentResult->getMessage();

                    $this->db->where(["id" => $transaction_history_insert_id])
                        ->set(
                            [
                                "rave_payment_response_object" => json_encode($paymentResult->getData()),
                                "http_request_status" => $resultCode,
                                "http_request_response" => $resultMessage,
                                "http_request_response" => $resultMessage
                            ])
                        ->update("transaction_history");

                    //record a transaction history record in the vendor account transaction history

                    //adjust the balance of the vendor if success

                    //adjust the pangisa account gl

                    //create a pangisa account transaction history record
                    $response = $resultCode == 202 ? $resultMessage : $resultCode . " Your request was received successfuly";

                } else {
                    $response = "The following request has been rejected";
                }

                //commit transaction
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo "Internal Server";
                } else {
                    $this->db->trans_commit();
                    echo $response;
                }
            }

        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function vendor_collections_account_details($txid)
    {
        $this->checkLogin();
        $this->db->select("amount as Amount, balance_before as 'Balance Before',balance_after as 'Balance After',transaction_type as 'Type', mode_of_transaction as Channel, date(date) as Date");
        $this->db->from("vendor_account_gl_transaction_history t");
        $this->db->where(["vendor_id" => $txid]);
        $transactions = $this->db->order_by("id", "desc")->limit(20)->get()->result_array();

        if (count($transactions) < 1) {
            echo "<b>No results found</b>";
            exit();
        }

        $result_string = "<table class='table lib_table table-hover'>";

        $result_string .= "<tr>";

        foreach ($transactions[0] as $key => $value) {
            $result_string .= "<td>" . $key . "</td>";
        }

        $result_string .= "</tr>";

        foreach ($transactions as $transaction) {
            $result_string .= "<tr>";


            foreach ($transaction as $key => $value) {
                $result_string .= "<td>" . (is_numeric($value) ? number_format($value) : strtolower($value)) . "</td>";
            }
            $result_string .= "</tr>";
        }


        $result_string .= "</table>";
        echo $result_string;
    }

    //pangisa escrow
    public function pangisa_escrow($offset = 0)
    {
        $this->checkLogin();
        $main_account = $this->db->limit(1)->get("pangisa_account_gl")->row();
        $pangisa_account_gl_transaction_history = $this->db->select("*")->from("pangisa_account_transaction_history")->limit($this->limit, $offset)->order_by("date", "desc")->get()->result_array();

        $total = $this->db->count_all("pangisa_account_transaction_history");
        $pagination = $this->pagination("pangisa_escrow/", $total, $this->limit);

        $data['main_account'] = $main_account;
        $data['transactions'] = $pangisa_account_gl_transaction_history;
        $data['pagination'] = $pagination;
        $this->loadView($this->portal . "pangisa_transaction_history", $data);
    }

    //transaction details
    public function pangisa_escrow_details($txid)
    {
        $this->checkLogin();
        $this->db->select("*");
        $this->db->from("pangisa_account_transaction_history t");
        $this->db->where(["id" => $txid]);
        $transactions = $this->db->get()->result_array();

        if (count($transactions) < 1) {
            echo "<b>No results found</b>";
        }

        $result_string = "<table class='table lib_table table-hover'>";

        foreach ($transactions[0] as $key => $value) {
            $result_string .= "<tr><td>" . $key . "</td><td>" . $value . "</td>";
        }

        $result_string .= "</table>";
        echo $result_string;
    }

    private function buildSideMenu()
    {
        $link = $this->link;
        $menu = array(
            ["icon_color" => "#fff;", "name" => "Dashboard", "icon" => "fa fa-dashboard", "link" => $link . 'index'],
            ["icon_color" => "#fff", "name" => "Vendors", "icon" => "fa fa-users", "link" => $link . 'partners'],
            ["icon_color" => "#fff", "name" => "Clients", "icon" => "fa fa-user-o", "link" => $link . 'clients'],
            ["icon_color" => "#fff", "name" => "Users", "icon" => "fa fa-user", "link" => $link . 'users'],
            ["icon_color" => "#fff", "name" => "Transactions", "icon" => "fa fa-money", "link" => $link . 'transactions'],
            ["icon_color" => "#fff", "name" => "Payments", "icon" => "fa fa-money", "link" => $link . 'payments'],
            ["icon_color" => "#fff", "name" => "Vendor Liquidation", "icon" => "fa fa-money", "link" => $link . 'liquidation_requests'],
            ["icon_color" => "#fff", "name" => "Collections", "icon" => "fa fa-money", "link" => $link . 'payments_to_pangisa'],
            ["icon_color" => "#fff", "name" => "Vendor Accounts", "icon" => "fa fa-money", "link" => $link . 'vendor_collections_account'],
            ["icon_color" => "#fff", "name" => "Pangisa Escrow", "icon" => "fa fa-money", "link" => $link . 'pangisa_escrow'],
            ["icon_color" => "#fff", "name" => "Items", "icon" => "fa fa-list", "link" => $link . 'items'],
            ["icon_color" => "#fff", "name" => "Categories", "icon" => "fa fa-list-alt", "link" => $link . 'categories'],
            ["icon_color" => "#fff", "name" => "Orders", "icon" => "fa fa-th-list", "link" => $link . 'orders'],
            ["icon_color" => "#fff", "name" => "My Account", "icon" => "fa fa-edit", "link" => $link . 'account'],
            ["icon_color" => "#fff", "name" => "Logs", "icon" => "fa fa-terminal", "link" => $link . 'logs'],
            ["icon_color" => "#fff", "name" => "Reports", "icon" => "fa fa-file-pdf-o", "link" => $link . 'Reports/General'],
            ["icon_color" => "#fff", "name" => "Feed Back", "icon" => "fa fa-comments-o", "link" => $link . 'publicfeedback'],
            ["icon_color" => "#FF0000", "name" => "Logout", "icon" => "fa fa-power-off", "link" => $link . 'logout']
        );


        return $menu;
    }

    public function items($action = null, $offset = null)
    {
        $this->checkLogin();
        $this->userCan("view_items");

        $post_company_filter = $this->input->post();
        $this->helper->cleanArrayInput($post_company_filter);
        $filter_string = '';

        if ($action == "view") {
            $itemToView = $this->db->select("*, (select name from users where id=added_by) as owner")->from("items")->where("id", $offset)->get()->row();
            $this->loadView($this->portal . "view_item_details", $itemToView);
            return;
        } else if ($action == "verify") {
            $score = $post_company_filter['score'];

            $inputObject = array(
                "verified_by" => $this->userId,
                "verified" => 1,
                "verification_date" => date('Y-m-d h:i:s'),
                "verification_score" => $score,
            );
            $this->db->where("id", $offset)->update("items", $inputObject);

            Redirect($this->link . 'items/');
        } elseif ($action == "suspend") {

            $inputObject = array("status" => 0);
            $this->db->where("id", $offset)->update("items", $inputObject);
            Redirect($this->link . 'items/');

        } elseif ($action == "unsuspend") {
            $inputObject = array("status" => 1);
            $this->db->where("id", $offset)->update("items", $inputObject);
            Redirect($this->link . 'items/');
        }


        if (count($post_company_filter) > 0):
            $filter_item = $post_company_filter['filter_search_item'];
            $startdate = $post_company_filter['startdate'];
            $enddate = $post_company_filter['enddate'];
            $filter_string .= " i.name like '%$filter_item%' or i.identification_number='%$filter_item%' ";
            if ($startdate) $filter_string .= " and date(date)>='$startdate' ";
            if ($enddate) $filter_string .= " and date(date)<='$enddate'";
            $this->db->where($filter_string);
        endif;


        $this->db
            ->select("i.id,i.name,i.item_number,i.verified,i.status, (select name from users where id=i.added_by) as owner, ca.name as  category,i.identification_number as 'Identification',i.year_of_make as 'Year of Make',
                ,(select count(*) from orders where item=i.id) as Orders,
                date(i.date_added) as date")
            ->from("items i")
            ->join("categories ca", "ca.id=i.category");
        $items = $this->db->limit($this->limit, $offset)->order_by("i.date_added", "desc")->get()->result_array();

        $this->title = "Pangisa - Items";
        $this->form_header_link = null;
        $this->form_header_title = null;
        $this->page_title = "VIEW ITEMS";

        $total = $this->db->count_all("items");
        $pagination = $this->pagination("items/pg/", $total, $this->limit);

        $this->pagination_display = $pagination;

        $this->loadView($this->portal . "items", $items);
    }

    public function pagination($link, $total, $per_page)
    {
        $config['base_url'] = $this->link . $link;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = true;
        $config['last_link'] = true;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = 'previous';
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


        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    private function loadView($view = null, $information)
    {
        $portal = "admin_portal/";
        $data['title'] = $this->title ? $this->title : "Pangisa";
        $data['view'] = $view ? $view : "dashboard";
        $data['form_header_title'] = $this->form_header_title;
        $data['form_header_link'] = $this->form_header_link;
        $data['data'] = $information;
        $data['error'] = $this->error;
        $data['pagination'] = $this->pagination_display;

        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($portal . "home", array("menu" => $this->buildSideMenu()));
    }

    private function uploadImages($path, $type, $image)
    {
        $filename = $type . "_" . time();
        $upload_path = $path;

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

    //view categories
    public function categories($action = null, $id = null)
    {
        $this->form_header_link = $this->link . "categories/save/";
        $this->checkLogin();

        if ($action == "save") {
            $icon = $this->uploadImages("category_icons", "icon", "icon");
            $categoriesData = $this->input->post();
            $categoriesData['icon'] = $icon;

            $this->db->insert("categories", $categoriesData);
            $this->saveLog("categories", "added", false, $this->db->insert_id());
            Redirect($this->link . "categories");

        } elseif ($action == "subcategories") {
            $input_data = $this->input->post();
            $this->db->insert("sub_categories", $input_data);
            Redirect($this->link . "categories");

        } elseif ($action == "edit_category") {
            $category = $this->db->where("id", $id)->get("categories")->row();
            $data['category'] = $category;
            $data['edit_cat_link'] = $this->link . "/Categories/save_edit_category/" . $id;
            $this->loadView($this->portal . "edit_category", $data);
            return;
        } elseif ($action == "save_edit_category") {
            $icon = null;
            if (isset($_FILES['icon']) && $_FILES['icon']['name']):
                $icon = $this->uploadImages("category_icons", "icon", "icon");
                $category = $this->db->where("id", $id)->get("categories")->row();
                if ($category->icon) unlink(APPPATH . "../category_icons/" . $category->icon);

            endif;

            $categoriesData = $this->input->post();
            if ($icon != null):$categoriesData['icon'] = $icon;endif;

            $this->db->where("id", $id)->set($categoriesData)->update("categories");
            $this->saveLog("categories", "edited", false, $this->db->insert_id());
            Redirect($this->link . "categories");
        }

        if ($action == "delete") {
            $this->db->where("id", $id)->delete("categories", $this->input->post());
            $this->saveLog("categories", "deleted", false, $id);
            Redirect($this->link . "categories");
        }

        if ($action == "delete_sub_category") {
            $this->db->where("id", $id)->delete("sub_categories", $this->input->post());
            $this->saveLog("sub_categories", "deleted", false, $id);
            Redirect($this->link . "categories");
        }

        $categories = $this->db->get("categories")->result_array();
        $data['categories'] = $categories;
        $data['categories_submit_link'] = $this->form_header_link;
        $data['sub_categories_submit_link'] = $this->link . "/Categories/subcategories/save";

        $this->loadView($this->portal . "categories", $data);

    }

    private function userCan($task = null)
    {
        $role = $this->session->user['role'];

        $roles = [];

        if ($role == "sys_user") {
            $roles = ["view_items"];
        } elseif ($role == "sys_admin") {
            $roles = ['add_user', "block_user", "view_items", "view_logs"];
        }

        if (!in_array($task, $roles)) {
            Redirect($this->link . "not_allowed_to_perform_task");
        }
    }

    public function publicfeedback()
    {
        $this->loadView("publicfeedback", null);
    }

    public function not_allowed_to_perform_task()
    {
        $this->load->view("not_allowed_to_perform_task");
    }

    //save logs
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

    // display logs
    public function logs($offset = null)
    {
        $this->checkLogin();
        $this->userCan("view_logs");

        $total_logs = $this->db->count_all("logs");

        $logs = $this->db->select("l.id,u.name,,a.name as Type,l.table,l.action,l.item_id as 'Item id',l.original_data as 'original Data',l.date")
            ->from("logs l")
            ->join("users u", "u.id=l.user")
            ->join("app_user_type a", "a.id=u.registration_type")
            ->limit($this->limit, $offset)
            ->order_by("l.date", "desc")
            ->get()
            ->result_array();

        $table = $this->lib->DrawTable($logs, null);
        $pagination = $this->pagination("logs/", $total_logs, $this->limit);

        $this->title = "Pangisa - Logs";
        $this->page_title = "SYSTEM LOGS";

        $this->loadSimpleView($table, $pagination);
    }

    //account
    public function account($action = null, $id = null)
    {
        $this->checkLogin();
        $user = $this->db->where(["id" => $this->session->user['id']])->get("users")->result_array();
        $data['user'] = $user[0];

        $this->title = "Pangisa - Edit Account";
        $this->page_title = "Addd New User";
        $data['error'] = null;

        if ($action == 'update') {
            $input_data = $this->input->post();

            if ($input_data['password'] <> null && $input_data['password'] <> $input_data['confirm_password']) {
                $data['error'] = "Password do not match";
            } else {

                $update_user = array(
                    "name" => $input_data['name'],
                    "email" => $input_data['email'],
                    "password" => sha1($input_data['password']),
                    "password_last_modified" => date("y-m-d h:i:s"),
                    "date_modified" => date("y-m-d h:i:s"),
                    "phone" => $input_data['phone'],
                    "location" => $input_data['location'],
                    "alternative_phone" => $input_data['alternative_phone']
                );

                $this->db->where(["id" => $this->session->user['id']])->update("users", $update_user);
                Redirect($this->link . "account");
            }
        }

        $this->loadView($this->portal . "myaccount", $data);
    }

    public function logout()
    {
        $this->saveLog("users", "logged out", false, null);

        $this->session->sess_destroy();
        Redirect(base_url());
    }

    public function Reports($type = null, $offset = null)
    {
        //$submitLink=$this->agent->referrer();
        $type = strtolower($type);
        $this->checkLogin();

        //return object result
        $data = [];
        $responseObject = array();
        $ReportHeading = null;

        //check port information from server
        $postInformation = $this->input->post();
        $this->helper->cleanArrayInput($postInformation);
        $filterParamCount = sizeof($postInformation);

        //generate transactions report
        if ($type == "transactions") {
            $ReportHeading = "Transactions Report";

            if ($filterParamCount > 0) {
                $startDate = $postInformation['start_date'];
                $stopDate = $postInformation['stop_date'];
                $this->db->where(["date(date) between $startDate and $stopDate"]);
            }

            $this->db->select("
                 count(*) as total, date(date) as transaction_date,    
                (select count(*) from  transaction_history where status=1 and date(date)=transaction_date) as successful,
                (select count(*) from  transaction_history where status=0 and date(date)=transaction_date) as pending
            ")->from("transaction_history");

            $transactionsResult = $this->db->order_by("date desc, owner desc, total desc, successful desc")->group_by("transaction_date, owner")->get()->result_array();
            $responseObject['transactionResultsGeneral'] = $transactionsResult;


            $this->db->select("channel_name as channel, count(*) as total, date(date) as transaction_date,    (select count(*) from  transaction_history where status=1 and date(date)=transaction_date) as successful,(select count(*) from  transaction_history where status=0 and date(date)=transaction_date) as pending ")
                ->from("transaction_history");
            $transactionsByChannel = $this->db->order_by("date desc,  total desc, successful desc")->group_by("date(date), owner")->get()->result_array();

            $responseObject['transactionsByChannel'] = $transactionsByChannel;

        } elseif ($type == "items") {
            $ReportHeading = "Items Report";

            if ($filterParamCount > 0) {
                $startDate = $postInformation['start_date'];
                $stopDate = $postInformation['stop_date'];
                $this->db->where(["date(i.date_added) between $startDate and $stopDate"]);
            }

            $this->db->select("
                 i.*,   
                (select count(*) from  orders where item=i.id) as   orders,
                (select count(*) from  orders where item=i.id and  approved=1) as successful,
                (select count(*) from  orders where item=i.id and  approved=0) as pending,
                (select count(*) from  orders where item=i.id and  user_cancel=1) as user_cancelled,
                (select sum(order_amount) from  orders where item=i.id ) as order_amount,
                (select sum(order_amount) from  orders where item=i.id  and approved=1) as approved_amount,
                (select sum(amount) from  transaction_history where item=i.id  and status=1) as collected_amount,
            ")->from("items i");

            $itemsResult = $this->db->order_by("i.date_added desc,  successful desc, collected_amount desc")->group_by("i.id")->get()->result_array();
            $responseObject['itemsResultsGeneral'] = $itemsResult;


        } elseif ($type == "payments") {
            $ReportHeading = "Items Report";

            if ($filterParamCount > 0) {
                $startDate = $postInformation['start_date'];
                $stopDate = $postInformation['stop_date'];
                $this->db->where(["date(i.date_added) between $startDate and $stopDate"]);
            }

            $this->db->select("
                 p.*,   
                (select name from users where id=p.client) as   client,
                (select name from items where id=p.item) as   item_name,
                (select  item_number from items where id=p.item) as   item_number,               
                ")
                ->from("payment_balances p");

            $itemsResult = $this->db->order_by("p.last_transaction_date desc")->group_by("p.id")->get()->result_array();
            $responseObject['paymentsResultsGeneral'] = $itemsResult;


        } elseif ($type == "orders") {
            $ReportHeading = "Orders Report";

            $dbDateString = null;

            if ($filterParamCount > 0) {
                $startDate = $postInformation['start_date'];
                $stopDate = $postInformation['stop_date'];
                $dbDateString = " and o.date between '" . date('Y-m-d', strtotime($startDate)) . "' and '" . date('Y-m-d', strtotime($stopDate)) . "' ";
                $this->db->where("o.date between '" . date('Y-m-d', strtotime($startDate)) . "' and '" . date('Y-m-d', strtotime($stopDate)) . "' ");
            }

            $this->db->select("
                 o.order_number,o.date, 
                (select name  from  items where id=o.item ) as item,  
                (select count(*) from  orders where item=o.item   $dbDateString ) as orders,
                (select count(*) from  orders where item=o.item and  approved=1  $dbDateString ) as successful,
                (select count(*) from  orders where item=o.item and  approved=0  $dbDateString) as pending,
                (select count(*) from  orders where item=o.item and  user_cancel=1  $dbDateString) as user_cancelled,
                (select sum(order_amount) from  orders where item=o.item     $dbDateString) as order_amount
            ")->from("orders o");

            $itemsResult = $this->db->order_by("o.date", "desc")->order_by("successful", "desc")->group_by("o.date, o.item")->get()->result_array();
            $responseObject['ordersResultsGeneral'] = $itemsResult;


        } else if ($type == "general") {
            $ReportHeading = "General Perfomance Report";
            $cur_date = date('Y-m-d');
            $cur_date_minu_7 = new DateTime($cur_date);
            $cur_date_minu_7 = $cur_date_minu_7->modify('-100 Days')->format('Y-m-d');
            $cur_date_minu_7 = (array)$cur_date_minu_7;
            $cur_date_minu_7 = $cur_date_minu_7[0];

            $transactions = $this->db->select("Count(*) as total, date(date) as date, sum(amount) as amount")->from("transaction_history")->where("date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();
            $orders = $this->db->select("Count(*) as total, date(date) as date, sum(order_amount) as amount")->from("orders")->where("date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();
            $clients = $this->db->select("Count(*) as total, date(date) as date")->from("users")->where("registration_type=5 and date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();
            $vendors = $this->db->select("Count(*) as total, date(date) as date")->from("users")->where("registration_type=6 and  date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();


            $responseObject['orders'] = $orders;
            $responseObject['vendors'] = $vendors;
            $responseObject['clients'] = $clients;
            $responseObject['transactions'] = $transactions;
            $responseObject['date_range'] = $cur_date_minu_7 . " and " . $cur_date;
        }

        $data['submit_link'] = $this->link . "Reports/" . $type;
        $data['reportInformation'] = $responseObject;
        $data['reportType'] = strtolower($type);
        $data['reportHeading'] = $ReportHeading;

        $this->loadView($this->portal . "report_index", $data);
    }

}
