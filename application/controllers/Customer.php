<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/23/2019
 * Time: 1:00 PM
 */
//include APPPATH . "controllers/Ballistics.php";
////include APPPATH . "controllers/constants_new.php";
//include APPPATH . "/libraries/MtnMomoHelper.php";

include "CentralAccess.php";

class Customer extends CI_Controller
{
    private $title = "Pangisa";
    private $ctrl_name = 'Customer';
    private $error = null;
    private $link = null;
    private $active_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $viewFolder = "customer/";
    private $breadcrumbs = null;
    private $sys_user = 4;
    private $client_user = 6;
    private $vendor_user = 5;
    private $helper = null;
    private $limit = 100;
    private $pagination_display = null;
    private $form_header_link;
    private $userId = null;
    private $ca_h = null;

    public function __construct()
    {
        parent::__construct();
        $this->link = base_url() . $this->ctrl_name . '/';
        $_SERVER['CI_ENV'] = "production";
        $this->load->library("Lib");
        $this->load->library("EmailHelper");
        $this->load->library("user_agent");
        $this->ca_h = new CentralAccess();
        $this->helper = $this->ca_h->getBallisticHelper();
        $this->userId = $this->session->user['id'];
    }

    public function index()
    {

        $this->dashboard();
    }

    public function account($action = null)
    {

    }

    public function dashboard()
    {
        $this->loadView(null, null);
    }

    public function Orders($action = null, $id = null)
    {
        $user_id = $this->session->user['id'];

        if (empty($user_id) || $user_id == null) {
            Redirect(base_url() . "Index/login/");
        } else {
            $login_type = $this->session->user['user_type'];

            if ($login_type <> "client") {
                Redirect(base_url() . "AppClient/index");
            } else {

                //user can cancel an order
                if ($action == "cancel") {
                    $orderToCancel = $this->db->where(["client" => $this->userId, "id" => $id])->get("orders")->row();

                    if ($orderToCancel != null) {
                        $this->db->where('id', $id)->set(["user_cancel" => 1, "approved" => 2])->update("orders");
                        $this->saveLog("orders", "cancelled", false, $id);
                    }

                }

                if ($action == "view") {
                    $this->db->select("
                    o.id as orderId,o.number_of_days,o.place_of_use,o.date as orderDate,o.usage_description,o.approved as orderStatus,o.comment as orderComments,o.approval_date as orderApprovalDate,o.user_cancel as orderUserCancel,o.pick_up_date as orderPickUpDate,o.admin_aproval as orderAdminApproval,o.admin_aproval_date as orderAdminApprovalDate,o.order_number as orderNumber,o.order_amount as orderAmount, o.return_date  as orderReturnDate,o.item_price_at_order as orderPricePerUnit,o.order_closed_successfully as orderClosedStatus,
                    i.id as itemId,i.name as itemName,i.category as itemCategory,i.color as itemColor,i.size as itemSize,i.rear_view,i.front_view,i.side_view,i.identification_number,i.brief_description,i.year_of_make,i.price,i.pick_up_location,i.price_point,i.features,i.views,i.orders,i.brand,i.item_number,
                    u.name as owner, u.id as ownerId,pb.amount,pb.order_number,pb.total_paid,pb.balance, pb.last_transaction_date");
                    $this->db->from("orders o");
                    $this->db->join("items i", "i.id=o.item");
                    $this->db->join("users u", "u.id=o.owner");
                    $this->db->join("payment_balances pb", "pb.order_number=o.order_number");
                    $this->db->where("o.id", $id);

                    $orderToView = $this->db->get()->row();

                    $this->loadView($this->viewFolder . "vieworderdetails", $orderToView);
                    return;
                }

                $orders = $this->db->select(
                    "o.id,o.order_number, o.number_of_days,o.date,o.place_of_use, o.place_of_use,o.approved,o.user_cancel,o.comment, 
                    i.id as item_id, i.name,i.size,i.price,i.identification_number, p.name as price_point"
                )
                    ->from("orders o")
                    ->join("items i", "i.id=o.item")
                    ->join("price_points p", "p.id=i.price_point")
                    ->where(["o.client" => $this->userId])
                    ->order_by("o.date desc")
                    ->get()
                    ->result_array();

                $this->loadView($this->viewFolder . "myorders", $orders);
            }
        }

    }

    public function payments_balances($offset = null)
    {
        $this->checkLogin();
        $this->page_title = "PAYMENTS";
        $filtet_string = $this->input->post();

        $this->db->where("client", $this->session->user['id']);

        if (count($filtet_string) > 0):
            $ordernumber = $filtet_string['filter_search_item'];
            $startdate = $filtet_string['startdate'];
            $enddate = $filtet_string['enddate'];

            $filter_string = " order_number like '%" . $ordernumber . "%' ";
            if ($startdate) $filter_string .= " and date(last_transaction_date)>='$startdate' ";
            if ($enddate) $filter_string .= " and date(last_transaction_date)<='$enddate'";
            $this->db->where($filter_string);
        endif;

        $payments = $this->db->select("p.id,p.amount,p.owner,i.identification_number, p.owner_name,p.order_number,p.total_paid,p.balance,p.last_transaction_date,p.date, `u`.`name` as `client`, `i`.`name` as `item`, `i`.`identification_number`")
            ->from("payment_balances p")
            ->join("users u", "u.id=p.client")
            ->join("items i", "i.id=p.item")
            ->limit($this->limit, $offset)
            ->order_by("p.last_transaction_date", "desc")
            ->get()
            ->result_array();

        $this->filter_submit_link = $this->link . "payments";
        $this->loadView($this->viewFolder . "payments", $payments);
    }

    public function transactions_history()
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
        $this->form_header_link = "";
        $this->page_title = "Payment Transaction History";
        $this->form_header_title = "Payments";
        $this->load_view_simple($table, $pagination);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        Redirect(base_url());
    }

    public function checkLogin()
    {
        $usertype = $this->session->user['user_type'];

        if (!$this->session->user['id'] || $usertype <> "client") {
            Redirect(base_url() . "Index/login");
        }
    }

    public function ExtendItemOrderRequest($order_number = null, $action = null)
    {
        $this->checkLogin();
        $orderToExtend = null;
        $order_number = ($order_number);

        if ($order_number) {
            $orderToExtend = $this->db->select(
                "o.id,o.order_number, o.number_of_days,o.date,o.place_of_use, o.place_of_use,o.approved,o.user_cancel,o.comment,
                cl.name as client_name, ow.name as owner_name, cl.phone as client_phone, ow.phone as owner_phone,
                i.id as item_id, i.name,i.size,i.price, p.name as price_point, o.return_date, o.order_amount, o.owner,o.client"
            )
                ->from("orders o")
                ->join("items i", "i.id=o.item")
                ->join("users cl", "cl.id=o.client")
                ->join("users ow", "ow.id=o.owner")
                ->join("price_points p", "p.id=i.price_point")
                ->where(["o.order_number" => $order_number])
                ->order_by("o.date desc")
                ->get()
                ->row();
        }

        if ($action == "save") {
            $extendOrderInput = $this->input->post();
            $extendOrderInput = $this->security->xss_clean($extendOrderInput);

            $this->helper->cleanArrayInput($extendOrderInput);

            $returnDate = $extendOrderInput['return_date'];
            $startdate = (array)date_add(new DateTime($returnDate), date_interval_create_from_date_string('1 Day'));
            $stopdate = (array)date_add(new DateTime($returnDate), date_interval_create_from_date_string($extendOrderInput['number_of_days'] . " Days"));

            $extendItemOrderObjectForInsert['order_number'] = $orderToExtend->order_number;
            $extendItemOrderObjectForInsert['order_amount'] = $orderToExtend->order_amount;
            $extendItemOrderObjectForInsert['client'] = $orderToExtend->client;
            $extendItemOrderObjectForInsert['owner'] = $orderToExtend->owner;
            $extendItemOrderObjectForInsert['number_of_days'] = $extendOrderInput['number_of_days'];
            $extendItemOrderObjectForInsert['start_date'] = $startdate['date'];
            $extendItemOrderObjectForInsert['stop_date'] = $stopdate['date'];
            $extendItemOrderObjectForInsert['reason'] = $extendOrderInput['reason'];
            $extendItemOrderObjectForInsert['current_place_of_use'] = $orderToExtend->place_of_use;
            $extendItemOrderObjectForInsert['approved'] = 2000;
            $extendItemOrderObjectForInsert['status_reason'] = null;
            $extendItemOrderObjectForInsert['extend_rate'] = $orderToExtend->order_amount;;
            $extendItemOrderObjectForInsert['extend_days_amount'] = $extendOrderInput['number_of_days'] * $extendItemOrderObjectForInsert['extend_rate'];

            $this->db->insert("extended_orders", $extendItemOrderObjectForInsert);

            //save message to que for client
            $message = array(
                "message" => "Dear $orderToExtend->client_name , Your Order Extension Request of " . $orderToExtend->name . " has been received. Order Number " . $order_number . ", Please be patient as we process. ",
                "client" => $this->session->user['id'],
                "owner" => $orderToExtend->owner,
                "item" => $orderToExtend->item_id,
                "status" => 0,
                "messageid" => null,
                "phone_number" => $orderToExtend->client_phone,
            );

            $this->db->insert("message_outbox", $message);

            //vendor message
            $message = array(
                "message" => "Dear $orderToExtend->owner_name , A " . $extendOrderInput['number_of_days'] . " Day Order Extension Request for " . $orderToExtend->name . " has been received. Order Number " . $order_number . ", Please login and take action. ",
                "client" => $this->session->user['id'],
                "owner" => $orderToExtend->owner,
                "item" => $orderToExtend->item_id,
                "status" => 0,
                "messageid" => null,
                "phone_number" => $orderToExtend->owner_phone,
            );

            //inset into que for client
            $this->db->insert("message_outbox", $message);

            Redirect($this->link . 'ExtendItemOrderRequest');
        }

        $extendedOrders = $this->db->select(
            "ext.id,o.order_number, o.number_of_days, ext.number_of_days as ext_number_of_days, date(ext.start_date) as start_date, date(ext.stop_date) as stop_date, ext.reason, ext.approved, ext.status_reason,
             i.id as item_id, i.name,i.size,i.price, p.name as price_point, date(ext.date) as date"
        )
            ->from("extended_orders ext")
            ->join("orders o", "o.order_number=ext.order_number")
            ->join("items i", "i.id=o.item")
            ->join("price_points p", "p.id=i.price_point")
            ->where("ext.client=" . $this->session->user['id'])
            ->order_by("ext.date desc")
            ->limit(50)
            ->get()
            ->result_array();

        $data['orderToExtend'] = $orderToExtend;
        $data['extendedOrders'] = $extendedOrders;

        $this->loadView($this->viewFolder . "extendOrder", $data);

    }

    public function Messages($senderId = null)
    {
        $this->checkLogin();
        $chatsMessages = [];
        $personToChatWith = [];

        //update the unread for every message one views
        if ($senderId) {
            $this->db->where(['sender' => $senderId, "receiver" => $this->userId])->set(['seen' => 1])->update('chats');
        };

        //check if there is a chat post
        $postChatData = $this->input->post();
        if (count($postChatData) > 0) {
            $receiver = $postChatData['receiver'];
            $sender = $this->userId;
            $message = $postChatData['message'];

            $saveMessageObject = [];
            $saveMessageObject['sender'] = $sender;
            $saveMessageObject['receiver'] = $receiver;
            $saveMessageObject['message'] = $message;
            $saveMessageObject['date'] = date('Y-m-d h:i:s');
            $saveMessageObject['seen'] = 0;
            $saveMessageObject['deleted'] = 0;

            $this->db->insert('chats', $saveMessageObject);
        }

        $chats = $this->db->select("distinct(sender) as senderId, DATE_FORMAT(date, '%M %D, %Y %H:%i:%s') as date, message ,(select name from users where id=senderId) as sender, (select count(*) from chats where sender=senderId and receiver=$this->userId and seen=0) as unread ")
            ->from("chats")
            ->where(["receiver" => $this->userId])
            ->order_by("date", "desc")
            ->order_by("seen", "asc")
            ->group_by("sender")
            ->get()
            ->result_array();

        if (count($chats) < 1) {
            $data['chatWith'] = null;
            $data['chatsMessages'] = [];
            $data['chats'] = [];
            $this->loadView("chats", $data);
            return;
        }

        $senderIdToRetrieveMessage = $senderId ? $senderId : $chats[0]['senderId'];

        $chatsMessages = $this->db->select("sender as senderId, DATE_FORMAT(date, '%M %D, %Y %H:%i:%s') as date, message ,(select name from users where id=senderId) as sender")
            ->from("chats")
            ->where("(receiver=" . $this->userId . " and sender=" . $senderIdToRetrieveMessage . ") or (receiver=" . $senderIdToRetrieveMessage . " and sender=" . $this->userId . ")")
            ->order_by("id", "asc")
            ->get()
            ->result_array();

        $personToChatWith = $this->db->select("id,name, email")->from("users")->where("id", $senderIdToRetrieveMessage)->get()->row();

        $data['chatWith'] = $personToChatWith ?: null;
        $data['chatsMessages'] = count($chatsMessages) ? $chatsMessages : [];
        $data['chats'] = $chats;
        $this->loadView("chats", $data);
    }

    public function AjaxSearchItemOrderNumber()
    {
        $orderNumber = $this->input->post("orderNumber");
        $orderNumber = $this->helper->cleanInput($orderNumber);

        $orders = $this->db->select(
            "o.id,o.order_number, o.number_of_days,
                    i.id as item_id, i.name,i.size,i.price,i.identification_number, p.name as price_point"
        )
            ->from("orders o")
            ->join("items i", "i.id=o.item")
            ->join("price_points p", "p.id=i.price_point")
            ->where(["o.client" => $this->session->user['id'], "o.approved" => 1, "o.order_number like " => '%' . $orderNumber . '%'])
            ->order_by("o.date desc")
            ->get()
            ->result_array();

        echo json_encode($orders);
    }

    private function buildSideMenu()
    {
        $link = $this->link;
        $menu = array(
            ["icon_color" => "#81ee78;", "name" => "Dashboard", "icon" => "fa fa-dashboard", "link" => $link . 'index'],
            ["icon_color" => "#81ee78;", "name" => "My Orders", "icon" => "fa fa-dashboard", "link" => $link . 'Orders'],
            ["icon_color" => "#81ee78;", "name" => "Payments", "icon" => "fa fa-dashboard", "link" => $link . 'payments_balances'],
            ["icon_color" => "#81ee78;", "name" => "Messages", "icon" => "fa fa-dashboard", "link" => $link . 'Messages'],
            ["icon_color" => "#81ee78;", "name" => "Transaction History", "icon" => "fa fa-dashboard", "link" => $link . 'transactions_history'],
            ["icon_color" => "#81ee78;", "name" => "Extend Order", "icon" => "fa fa-dashboard", "link" => $link . 'ExtendItemOrderRequest'],
            ["icon_color" => "#81ee78;", "name" => "Back to Portal", "icon" => "fa fa-home", "link" => base_url() . 'Index'],
            ["icon_color" => "#FF0000", "name" => "Logout", "icon" => "fa fa-power-off", "link" => $link . 'logout']
        );

        return $menu;
    }

    public function pagination($link, $total, $per_page)
    {
        $config['base_url'] = $this->link . $link;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    private function loadView($view = null, $information)
    {
        $portal = $this->viewFolder;
        $data['title'] = $this->title ? $this->title : "Pangisa";
        $data['view'] = $view ? $view : $this->viewFolder . "dashboard";
        $data['data'] = $information;
        $data['pagination'] = $this->pagination_display;

        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($portal . "home", array("menu" => $this->buildSideMenu()));
    }

    private function load_view_simple($info)
    {
        $data['view'] = $this->viewFolder . "generic";
        $data['title'] = $this->title;
        $data['error'] = $this->error;
        $data['active_link'] = $this->active_link;
        $data["information"] = $info;
        $data["error"] = $this->error;
        $data["menu"] = $this->buildSideMenu();
        $data["pagination"] = $this->pagination_display;

        $data['page_title'] = $this->page_title;

        $data['form_header_title'] = $this->form_header_title;
        $data['form_header_link'] = $this->form_header_link;

        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($this->viewFolder . "home", array("menu" => $this->buildSideMenu()));
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

    //momo payments
    public function ProcessMTNMOMOPayments($payment_id, $action = null)
    {
//        $this->checkLogin();
        $callback_url = $this->link . "PostMtnMoMoTransactionCallback/" . $payment_id;
        $momo = new MtnMomoHelper();
        $this->helper->log_to_system(json_encode($this->input->post()));
        $paymentRecord = $this->db->where(["id" => $payment_id])->get("orders")->row();


        if (!$paymentRecord) {
            echo json_encode(new Response(404, "Payment Record not found"));

        } elseif ($action == null && $paymentRecord) {
            echo json_encode(new Response(0, "success", $paymentRecord));

        } elseif ($action == "verifypaymentdetails") {
            $mode_of_payment = $this->input->post("mode_of_payment");
            $phone = $this->input->post("phone");

            $validatePhone = $this->validatePhoneNumber($phone);
            if ($validatePhone->getResultCode() <> 0) {
                echo json_encode($validatePhone);
            } else {
                $validateAccountHolder = $momo->VerifyAccountHolder($phone);
                echo json_encode($validateAccountHolder);
            }

        } elseif ($action == "triggerpayment") {
            $payment_channel = $this->db->where(['user' => "MTN_MOMO"])->get("payment_chanels")->row();

            $phone = $this->input->post("phone");
            $amount = $this->input->post("amount");

            $validatePhone = $this->validatePhoneNumber($phone);

            if ($validatePhone->getResultCode() != 0) {
                echo json_encode($validatePhone);
            } else {
                $transactionId = strtoupper(uniqid("PN-MOMO" . $this->userId));

                $response = new Response();
                $this->db->trans_start();

                $orderNumber = $paymentRecord->order_number;
                $orderDetails = $paymentRecord;

                if (!$orderDetails) {
                    echo json_encode(new Response(404, 404 . " - Order number not found", null));
                    return;
                }

                $client = $this->db->where("id", $orderDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $orderDetails->owner)->get("users")->row();
                $item = $this->db->where("id", $orderDetails->item)->get("items")->row();
                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();

                if (!$client):
                    echo json_encode(new Response(404, 404 . " Client no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$item):
                    echo json_encode(new Response(404, 404 . " Item no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$owner):
                    echo json_encode(new Response(404, 404 . " Owner no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$balance):
                    echo json_encode(new Response(0, 0 . " No Balance record found"));
                    return;
                endif;

                if ($balance->balance == 0 || $balance->balance < 1):
                    echo json_encode(new Response(404, " Payment for this order was cleared."));
                    return;
                endif;

                if ($orderDetails->approved == 0) {
                    $response->setData(null);
                    $response->setMessage("Dear $client->name Your Order has not yet been approved yet, Please be patient");
                    $response->setResultCode(501);

                    echo json_encode($response);
                    return;
                }

                $orderResponse['orderNumber'] = $orderNumber;
                $orderResponse['amountPayable'] = $balance->balance;
                $orderResponse['minAmount'] = $balance->balance > (0.5 * $balance->amount) ? 0.25 * $balance->balance : 0.5 * $balance->balance;
                $orderResponse['customerName'] = $client->name;
                $orderResponse['itemName'] = $item->name;
                $orderResponse['itemRegistrationNumber'] = $item->identification_number;
                $orderResponse['transactionId'] = $transactionId;
                $orderResponse['narration'] = "Dear $client->name, Please Complete Payment for $item->name Order Number $orderNumber. Amount Payable " . number_format($balance->balance);

                $paymentHistory = array();
                $paymentHistory['payment_channel'] = $payment_channel->id;
                $paymentHistory['transactionid'] = $transactionId;
                $paymentHistory['channel_name'] = $payment_channel->chanel_name;
                $paymentHistory['min_amount'] = $orderResponse['minAmount'];
                $paymentHistory['amountpayable'] = $balance->balance;
                $paymentHistory['client'] = $client->id;
                $paymentHistory['client_name'] = $client->name;
                $paymentHistory['item'] = $orderDetails->item;
                $paymentHistory['item_reg_number'] = $item->identification_number;
                $paymentHistory['owner'] = $orderDetails->owner;
                $paymentHistory['owner_name'] = $owner->name;
                $paymentHistory['order_number'] = $orderNumber;
                $paymentHistory['status'] = 0;
                $paymentHistory['narration'] = "Payment for $orderNumber off $payment_channel->chanel_name";
                $paymentHistory['customer_number'] = $client->phone;

                $this->db->insert("transaction_history", $paymentHistory);
                $transactionInsertId = $this->db->insert_id();

                $paymentResult = $momo->RequestToPay($phone, $amount, "Payment for " . $paymentRecord->order_number, $transactionId, $callback_url);

                echo json_encode($paymentResult);
                $resultCode = $paymentResult->getResultCode();
                $resultMessage = $paymentResult->getMessage();

                $this->db->where(["id" => $transactionInsertId])
                    ->set(["momo_request_status" => $resultCode, "momo_request_response" => $resultMessage])
                    ->update("transaction_history");

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                echo json_encode(new Response(200, 0 . " Successfully processed transaction", $orderResponse));
                return;
            }
        }
    }

    //process rave payments
    public function ProcessRavePayments($payment_id, $action = null)
    {
        $this->checkLogin();

        $paymentRecord = $this->db->select("pc.id,pc.amount,pc.owner, pc.item, pc.total_paid, pc.balance,pc.order_number as reference_number, pc.amount, concat('Payment for ',pc.order_number) as reason , pc.last_transaction_date")
            ->from("payment_balances pc")
            ->where(["id" => $payment_id, 'client' => $this->userId])
            ->get()
            ->row();


        if (!$paymentRecord) {
            echo json_encode($this->ca_h->getGenericResponse(404, "Payment Record not found"));

        } elseif ($action == null && $paymentRecord) {
            echo json_encode($this->ca_h->getGenericResponse(0, "success", $paymentRecord));

        } elseif ($action == "verifypaymentdetails") {
            $mode_of_payment = $this->input->post("mode_of_payment");
            $phone = $this->input->post("phone");

            $validatePhone = $this->validatePhoneNumber($phone);
            if ($validatePhone->getResultCode() <> 0) {
                echo json_encode($validatePhone);
            } else {
                echo json_encode($this->ca_h->getGenericResponse(0, "Validation successful"));
            }

        } elseif ($action == "triggerpayment") {
            $payment_channel = $this->db->where(['user' => "rave"])->get("payment_chanels")->row();

            $phone = $this->input->post("phone");
            $amount = $this->input->post("amount");
            $validatePhone = $this->validatePhoneNumber($phone)->getData();

            $transactionId = strtoupper(uniqid("PN-RV-" . $this->userId));
            $client = $this->db->where(['id' => $this->userId])->get("users")->row();
            $owner = $this->db->where(['id' => $paymentRecord->owner])->get("users")->row();

            $paymentHistory = array();
            $paymentHistory['payment_channel'] = $payment_channel->id;
            $paymentHistory['transactionid'] = $transactionId;
            $paymentHistory['channel_name'] = $payment_channel->chanel_name;
            $paymentHistory['min_amount'] = $paymentRecord->balance > (0.5 * $paymentRecord->amount) ? 0.25 * $paymentRecord->balance : 0.5 * $paymentRecord->balance;;
            $paymentHistory['amountpayable'] = $paymentRecord->amount;
            $paymentHistory['client'] = $this->userId;
            $paymentHistory['client_name'] = $client->name;
            $paymentHistory['item'] = $paymentRecord->item;
            $paymentHistory['item_reg_number'] = $paymentRecord->reference_number;
            $paymentHistory['owner'] = $owner->id;
            $paymentHistory['owner_name'] = $owner->name;
            $paymentHistory['order_number'] = $paymentRecord->reference_number;
            $paymentHistory['status'] = 0;
            $paymentHistory['narration'] = "Payment for $paymentRecord->reference_number off $payment_channel->chanel_name";
            $paymentHistory['customer_number'] = $phone;

            $this->db->insert("transaction_history", $paymentHistory);
            $transactionInsertId = $this->db->insert_id();

            //instantiate flutter wave
            $fl_w = $this->ca_h->getFlutterWavePayments();
            $ravePaymentDetails = [
                "customer_phone" => $phone,
                "customer_firstname" => $client->name,
                "customer_lastname" => null,
                "customer_email" => $client->email,
                "amount" => $amount,
                "txref" => $transactionId,
            ];

            $paymentResult = $fl_w->RequestRavePayment($ravePaymentDetails);
            $resultCode = $paymentResult->getResultCode();
            $resultMessage = $paymentResult->getMessage();

            $resultData = $paymentResult->getData();
            $raveRedirectLink = null;

            if ($resultCode == 200 || $resultCode == 0) {
                $raveRedirectLink = $resultData['data']['link'];
            }

            $this->db->where(["id" => $transactionInsertId])
                ->set(["http_request_status" => $resultCode, "http_request_response" => $resultMessage, "rave_payment_link" => $raveRedirectLink])
                ->update("transaction_history");

            $resultMessage = (200 || 0) ? $resultMessage = "You will be redirected to Complete Payment" : $resultMessage;

            echo json_encode($this->ca_h->getGenericResponse(($resultCode == (202 || 200) ? 0 : $resultCode), $resultMessage, $raveRedirectLink));
        }
    }

    private function validatePhoneNumber($phone)
    {
        if (strlen($phone) < 10) {
            return new Response(100, "Phone number must be 10 digits or 12 digits ");
        }

        $first3 = substr($phone, 0, 3);
        $first1 = substr($phone, 0, 1);

        $first1Array = ['07', '25'];
        $mtnnumbers = ["077", "078"];
        $first2 = substr($phone, 0, 2);

        if (!in_array($first2, $first1Array)) {
            return new Response(100, "Phone number must start with 07 or 256 " . $first1);
        }

        if ($first3 == "256" && !in_array(substr(2, 3), $mtnnumbers)) {
            return new Response(100, "Phone number must be MTN number ");
        }

        $phone = $first1 == 0 ? "256" . substr(1, 9) : $phone;
        return new Response(0, "valid", $phone);
    }
}
