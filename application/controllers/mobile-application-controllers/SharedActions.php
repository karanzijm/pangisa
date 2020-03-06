<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/1/2019
 * Time: 5:23 PM
 */
include APPPATH . "controllers/BaseController.php";
//include APPPATH . "controllers/CentralAccess.php";

class SharedActions extends BaseController
{

    public function index()
    {
        echo "hi mobile application";
    }

    public function getCategories($cat_id = null)
    {
        $categories = $this->db->get("categories")->result_array();
        $categoryReturn = [];
        foreach ($categories as $category):
            $category['icon'] = base_url() . "category_icons/" . $category['icon'];
            $categoryReturn[] = $category;
        endforeach;

        $this->responseData = $categoryReturn;
        $this->responseMessage = count($categoryReturn) . " categories found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    private function returnResponse()
    {
        echo json_encode($this->ca_h->getGenericResponse($this->responseCode, $this->responseMessage, $this->responseData));
        exit();
    }

    public function postClientOrder()
    {
        $order = $this->post_data;

        $this->helper->log_to_system($this->post_data, null, null);

        $number_of_days = $order['number_of_days'] < 1 ? (-1 * $order['number_of_days']) : $order['number_of_days'];
        $item = $this->db->where(['id' => $order['item_id']])->get("items")->row();
        $item_id = $order['item_id'];

        $order_dets = array(
            "item" => $item_id,
            "owner" => $item->added_by,
            "number_of_days" => $number_of_days,
            "place_of_use" => $order['intended_place_of_use'],
            "client" => $order['client'],
            "usage_description" => $order['usage_description'],
            "approved" => 0,
            "order_amount" => $item->price * $number_of_days,
            "item_price_at_order" => $item->price_without_markup,
            "markup_at_order" => (($item->price * $number_of_days) - ($item->price_without_markup * $number_of_days)),
            "markup_cleared" => 0,
            "item_negotiation_completed" => 'False',
            "is_negotiable" => $item->is_negotiable,
            "order_source" => 'app',
        );

        //insert order
        $this->db->insert("orders", $order_dets);
        $order_insert_id = $this->db->insert_id();

        //save logs
        $this->saveLog("orders", "Placed Order", false, $order_insert_id);
        $order_number = "PN" . str_pad($this->db->insert_id(), 8, 0, STR_PAD_LEFT);

        $this->db->set(["orders" => "orders+1"]);
        $this->db->where('id', $item->id);
        $this->db->update('items');

        //update order numbers
        $this->db->set("order_number", $order_number)->where("id", $order_insert_id)->update("orders");


        $item_client = $this->db->where("id", $this->session->user['id'])->select("id,phone, email, name")->get("users")->row();
        $item_owner = $this->db->where("id", $item->added_by)->select("id,phone, email, name")->get("users")->row();
        $item_ordered = $this->db->where("id", $item_id)->select("id,name, identification_number")->get("items")->row();


        if ($item->is_sales_default_account) {
            $vendor_phone_number = $item->vendor_phone_number;
            //save message to que for client
            $message = array(
                "message" => "Dear $item->vendor_name , An Order " . $order_number . " has been placed on your item (" . $item->name . ") ",
                "client" => $this->session->user['id'],
                "owner" => $item->added_by,
                "item" => $item_id,
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
            "item" => $item_id,
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

        //send mail to vendor
        $SUBJECT = "PANGISA - YOU HAVE A PENDING ORDER";
        $seller_mail_address = $item_owner->email;
        $seller_message_for_item_order = "Hello $item_owner->name, you have a pending order. <br><br> Order Number : $order_number <br>";
        $seller_message_for_item_order .= "Client $item_client->name  <br><br> Order Date :  <br>" . date("Y-m-d h:i:s");
        $seller_message_for_item_order .= "<br>Item $item_ordered->name ";
        $seller_message_for_item_order .= "<br>Item Identification Number $item_ordered->identification_number ";
        $seller_message_for_item_order .= "<br>Please Login and take action";

        $emailObjectForDatabaseSave['client'] = $item_client->id;
        $emailObjectForDatabaseSave['owner'] = $item_owner->id;
        $emailObjectForDatabaseSave['action'] = "PLACED_ORDER";
        $emailObjectForDatabaseSave['body'] = $seller_message_for_item_order;
        $emailObjectForDatabaseSave['email_address'] = $seller_mail_address;
        $emailObjectForDatabaseSave['status'] = 0;
        $emailObjectForDatabaseSave['subject'] = $SUBJECT;

        //save the email for the seller
        //$this->helper->sendMail($SUBJECT,$seller_mail_address, $seller_message_for_item_order);
        $this->db->insert("email_outbox", $emailObjectForDatabaseSave);

        //send mail to client
        $SUBJECT = "PANGISA - YOU HAVE PLACED AN ORDER ORDER";
        $emailResponseToUser = $this->emailhelper->composeEmailForApplicationUserPlaceOrder($order_dets['item'], $order_dets['client'], $order_insert_id);
        $emailObjectForDatabaseSave['email_address'] = $item_client->email;
        $emailObjectForDatabaseSave['body'] = $emailResponseToUser;
        $emailObjectForDatabaseSave['subject'] = $SUBJECT;

        //save the email for the client --  all these process that talk to external systems have been automated
        $this->db->insert("email_outbox", $emailObjectForDatabaseSave);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->responseCode = 1000;
            $this->responseData = null;
            $this->responseMessage = "A internal server error occurred.";
        } else {
            $this->db->trans_commit();
            $this->responseCode = 0;
            $this->responseData = null;
            $this->responseMessage = "Your order has been received successfully.";
        }

        $this->returnResponse();
    }

    public function getItemsFiltered()
    {
        $this->db
            ->select("i.id,i.price,i.is_negotiable,i.name,i.added_by as vendor_id, i.front_view,i.side_view,i.rear_view,i.item_number,i.verified,i.status, (select name from users where id=i.added_by) as owner, ca.name as  category,i.identification_number as 'Identification',i.year_of_make as 'Year of Make',
                ,(select count(*) from orders where item=i.id) as Orders,
                date(i.date_added) as date")
            ->from("items i");

        if ($this->post_data) {
            $filterData = $this->post_data;
            $this->helper->cleanArrayInput($filterData);

            if ($filterData['category_id'] <> null) {
                $this->db->where(['i.category' => $filterData['category_id']]);
            }

            if ($filterData['name'] <> null) {
                $this->db->where("i.name like '%" . $filterData['name'] . "%'");
            }
        }

        $this->db->join("categories ca", "ca.id=i.category");
        $items = $this->db->limit($this->limit, $this->offset)->order_by("i.date_added", "desc")->get()->result_array();

        $itemsReturn = [];
        foreach ($items as $item) {
            $item['front_view'] = base_url() . "items/" . $item['front_view'];
            $item['rear_view'] = base_url() . "items/" . $item['rear_view'];
            $item['side_view'] = base_url() . "items/" . $item['side_view'];
            $itemsReturn[] = $item;
        }

        $this->responseData = $itemsReturn;
        $this->responseMessage = count($items) . " items found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getItems($cat_id = null, $offset = null)
    {
        $this->db
            ->select("i.id,i.price,i.is_negotiable,i.name,i.added_by as vendor_id,i.front_view,i.side_view,i.rear_view,i.item_number,i.verified,i.status, (select name from users where id=i.added_by) as owner, ca.name as  category,i.identification_number as 'Identification',i.year_of_make as 'Year of Make',
                ,(select count(*) from orders where item=i.id) as Orders,
                date(i.date_added) as date")
            ->from("items i");

        if ($cat_id) {
            $this->db->where(['category' => $cat_id]);
        }

        $this->db->join("categories ca", "ca.id=i.category");
        $items = $this->db->limit($this->limit, $offset ?: $this->offset)->order_by("i.date_added", "desc")->get()->result_array();

        $itemsReturn = [];
        foreach ($items as $item) {
            $item['front_view'] = base_url() . "items/" . $item['front_view'];
            $item['rear_view'] = base_url() . "items/" . $item['rear_view'];
            $item['side_view'] = base_url() . "items/" . $item['side_view'];
            $itemsReturn[] = $item;
        }

        $this->responseData = $itemsReturn;
        $this->responseMessage = count($items) . " categories found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getVendors($cat_id = null, $offset = null)
    {
        $filterData = [];

        if ($this->post_data) {
            $filterData = $this->post_data;
            $this->helper->cleanArrayInput($filterData);
        }

        $clients = $this->db
            ->where(["registration_type" => $this->vendor_user])
            ->where($filterData)
            ->select("id,name,  email, phone , location,role,(case when status=1 then 'Active' else 'Suspended' end )as status, date(date) as date")
            ->from("users")
            ->get()
            ->result_array();


        $this->responseData = $clients;
        $this->responseMessage = count($clients) . " clients found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getUserInformation()
    {
        $filterData = [];

        if ($this->post_data) {
            $filterData = $this->post_data;
            $this->helper->cleanArrayInput($filterData);
        }

        $clients = $this->db
            ->where($filterData)
            ->select("id,name,  email, phone , location,role,(case when status=1 then 'Active' else 'Suspended' end )as status, date(date) as date")
            ->from("users")
            ->get()
            ->result_array();


        $this->responseData = $clients;
        $this->responseMessage = count($clients) . " clients found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getClients($cat_id = null, $offset = null)
    {
        $clients = $this->db
            ->where(["registration_type" => $this->client_user])
            ->select("id,name,  email, phone , location,role,(case when status=1 then 'Active' else 'Suspended' end )as status, date(date) as date")
            ->from("users")
            ->get()
            ->result_array();
        $this->responseData = $clients;
        $this->responseMessage = count($clients) . " clients found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getUsers($cat_id = null, $offset = null)
    {
        $clients = $this->db
            ->where(["registration_type" => $this->sys_user])
            ->select("id,name,  email, phone , location,role,(case when status=1 then 'Active' else 'Suspended' end )as status, date(date) as date")
            ->from("users")
            ->get()
            ->result_array();
        $this->responseData = $clients;
        $this->responseMessage = count($clients) . " clients found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getVendorCollectionsAccount($cat_id = null, $offset = null)
    {
        $vendor_accounts = $this->db->select("v.*, (select name from users where id=v.vendor_id) as vendor_name")->from("vendor_account_gl v")->limit($this->limit, $offset)->get()->result_array();
        $this->responseData = $vendor_accounts;
        $this->responseMessage = count($vendor_accounts) . " accounts found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getItemDetails($item_id = null)
    {
        $itemToView = $this->db->select("i.*, (select name from categories where id=i.category) as category_name,(select name from users where id=i.added_by) as owner")->from("items i")->where("id", $item_id)->get()->result_array();
        $itemToView = $this->ca_h->getBallisticHelper()->stripHtmlTagsFromStringOrAssociativeArray($itemToView);

        $itemsReturn = [];
        foreach ($itemToView as $item):
            $item['rear_view'] = base_url() . "items/" . $item['rear_view'];
            $item['front_view'] = base_url() . "items/" . $item['front_view'];
            $item['side_view'] = base_url() . "items/" . $item['side_view'];
            $itemsReturn[] = $item;
        endforeach;

        $this->responseData = $this->trimResponse($itemsReturn[0]);
        $this->responseCode = 0;
        $this->responseMessage = "Item " . $itemsReturn[0]['name'] . " Found";
        $this->returnResponse();
    }

    public function getOrderDetails($order_id)
    {
        $this->db->select("
                    o.id as orderId,o.number_of_days,o.approved,o.place_of_use,o.date as orderDate,o.usage_description,o.approved as orderStatus,o.comment as orderComments,o.approval_date as orderApprovalDate,o.user_cancel as orderUserCancel,o.pick_up_date as orderPickUpDate,o.admin_aproval as orderAdminApproval,o.admin_aproval_date as orderAdminApprovalDate,o.order_number as orderNumber,o.order_amount as orderAmount, o.return_date  as orderReturnDate,o.item_price_at_order as orderPricePerUnit,o.order_closed_successfully as orderClosedStatus,
                    i.id as itemId,i.name as itemName,i.category as itemCategory,i.color as itemColor,i.size as itemSize,i.rear_view,i.front_view,i.side_view,i.identification_number,i.brief_description,i.year_of_make,i.price,i.pick_up_location,i.price_point,i.features,i.views,i.orders,i.brand,i.item_number,
                    u.name as owner, u.id as ownerId,pb.amount,pb.order_number,pb.total_paid,pb.balance, pb.last_transaction_date");
        $this->db->from("orders o");
        $this->db->join("items i", "i.id=o.item");
        $this->db->join("users u", "u.id=o.owner");
        $this->db->join("payment_balances pb", "pb.client=o.client");
        $this->db->where("o.id", $order_id);

        $orderToView = $this->db->get()->row();

        if ($orderToView == null) {
            $this->responseCode = 1000;
            $this->responseData = $orderToView;
            $this->responseMessage = "Order with id " . $order_id . " not found";
        } else {
            $this->responseCode = 0;
            $this->responseData = $orderToView;
            $this->responseMessage = "Order with id " . $order_id . " found";
        }

        $this->returnResponse();

    }

    public function clientCancelOrder($client_id, $order_id)
    {
        $orderToCancel = $this->db->where(["client" => $client_id, "id" => $order_id])->get("orders")->row();

        if ($orderToCancel != null) {
            if ($orderToCancel->approved == 1) {
                $this->responseMessage = "Order is already approved and cannot be cancelled";
            } else {
                $this->db->where('id', $order_id)->set(["user_cancel" => 1, "approved" => 2])->update("orders");
                $this->saveLog("orders", "cancelled", false, $order_id);
                $this->responseMessage = "Order Cancelling was successful";
            }

        } else {
            $this->responseMessage = "Order with specified id not found";
        }

        $this->returnResponse();

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

    public function getClientOrders($client_id)
    {
        $orders = $this->db->select(
            "o.id,o.order_number, o.number_of_days,o.date,o.place_of_use, o.place_of_use,o.approved,o.user_cancel,o.comment, 
                    i.id as item_id, i.name,i.size,i.price,i.identification_number, p.name as price_point"
        )
            ->from("orders o")
            ->join("items i", "i.id=o.item")
            ->join("price_points p", "p.id=i.price_point")
            ->where(["o.client" => $client_id])
            ->order_by("o.date desc")
            ->get()
            ->result_array();

        $this->responseMessage = count($orders) . " orders found";
        $this->responseData = $orders;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getClientOrderPayments($offset = 0)
    {
        $filterdata = $this->post_data;
        if ($filterdata) {
            $this->db->where($filterdata);
        }

        $payments = $this->db->select("p.*, u.name as client, i.name as item, i.identification_number")
            ->from("payment_balances p")
            ->join("users u", "u.id=p.client")
            ->join("items i", "i.id=p.item")
            ->limit($this->limit, $offset)
            ->order_by("p.last_transaction_date", "desc")
            ->get()
            ->result_array();

        $this->responseMessage = count($payments) . "transactions found";
        $this->responseData = $payments;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    private function trimResponse($input)
    {
        $resp = [];
        foreach ($input as $key => $value) {
            $resp[$key] = str_replace('&nbsp;', '', trim($value));
        }
        return $resp;
    }

    //shared by all the users who are logged in
    public function updateProfile($user_id)
    {
        $updateInfo = $this->post_data;
        $this->helper->cleanArrayInput($updateInfo);

        if (isset($updateInfo['email'])) {
            //check valid email
            if (filter_var($updateInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $this->responseCode = 1000;
                $this->responseMessage = "profile Update Reject, Email is not a valid email";
                $this->returnResponse();
            }
        }

        if (isset($updateInfo['password'])) {
            $updateInfo['password'] = sha1($updateInfo['password']);
        }

        $this->db->where('id', $user_id)->update('users', $updateInfo);
        $this->responseMessage = "Profile Updated Successfully";
        $this->responseCode = 0;

        $this->returnResponse();
    }

    public function getClientTransactions($client_id)
    {
        $this->db->select("
        t.id,t.order_number,t.transactionid,t.ext_transaction_id,t.payee_number, t.amountpayable, t.amount,t.channel_name as channel,
         t.payment_date,
        date(t.date) as date");
        $this->db->from("transaction_history t");
        $this->db->where(["client" => $client_id, "status" => 1]);

        $transactions = $this->db->limit($this->limit)->order_by("t.date desc")->get()->result_array();

        $this->responseMessage = count($transactions) . "transactions found";
        $this->responseData = $transactions;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    //vendor specific
    /**
     * @param $vendor_id
     * @param null $cat_id
     * @param int $offset
     */
    public function getVendorItems($vendor_id, $cat_id = null, $offset = 0)
    {
        $this->db->where(['added_by' => $vendor_id])
            ->select("i.id,i.price,i.is_negotiable,i.price_without_markup,i.name,i.front_view,i.side_view,i.rear_view,i.item_number,i.verified,i.status, (select name from users where id=i.added_by) as owner, ca.name as  category,i.identification_number as 'Identification',i.year_of_make as 'Year of Make',
                ,(select count(*) from orders where item=i.id) as orders,
                date(i.date_added) as date")
            ->from("items i");

        if ($cat_id) {
            $this->db->where(['category' => $cat_id]);
        }

        $this->db->join("categories ca", "ca.id=i.category");
        $items = $this->db->limit($this->limit, $offset ?: $this->offset)->order_by("i.date_added", "desc")->get()->result_array();

        $itemsReturn = [];
        foreach ($items as $item) {
            $item['front_view'] = base_url() . "items/" . $item['front_view'];
            $item['rear_view'] = base_url() . "items/" . $item['rear_view'];
            $item['side_view'] = base_url() . "items/" . $item['side_view'];
            $itemsReturn[] = $item;
        }

        $this->responseData = $itemsReturn;
        $this->responseMessage = count($items) . " categories found";
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getVendorTransactions($vendor_id)
    {
        $this->db->select("
        t.id,t.order_number,t.transactionid,t.ext_transaction_id,t.payee_number, t.amountpayable, t.amount,t.channel_name as channel,
         t.payment_date,
        date(t.date) as date");
        $this->db->from("transaction_history t");
        $this->db->where(["owner" => $vendor_id, "status" => 1]);

        $transactions = $this->db->limit($this->limit)->order_by("t.date desc")->get()->result_array();

        $this->responseMessage = count($transactions) . "transactions found";
        $this->responseData = $transactions;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    public function getVendorPayments($vendor_id, $offset = 0)
    {
        $this->db->where(["owner" => $vendor_id]);
        $payments = $this->db
            ->Select("p.*, u.name as client, i.name as item, i.identification_number")
            ->from("payment_balances p")
            ->join("users u", "u.id=p.client")
            ->join("items i", "i.id=p.item")
            ->limit($this->limit, $offset)
            ->get()
            ->result_array();

        $this->responseMessage = count($payments) . " payments found";
        $this->responseData = $payments;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    //get vendor orders
    public function getVendorOrders($vendor_id)
    {
        $orders = $this->db->select(
            "o.id,o.order_number, o.number_of_days,o.date,o.place_of_use, o.place_of_use,o.approved,o.user_cancel,o.comment, 
                    i.id as item_id, i.name,i.size,i.price,i.identification_number, p.name as price_point"
        )
            ->from("orders o")
            ->join("items i", "i.id=o.item")
            ->join("price_points p", "p.id=i.price_point")
            ->where(["o.owner" => $vendor_id])
            ->order_by("o.date desc")
            ->get()
            ->result_array();

        $this->responseMessage = count($orders) . " orders found";
        $this->responseData = $orders;
        $this->responseCode = 0;
        $this->returnResponse();
    }

    //get vendor statement f account, list of transactions showing vendor account activity
    public function getVendorStatementOfAccount($vendor_id)
    {
        $this->db->select("id,vendor_id,amount,balance_before,balance_after,transaction_type,date,payments_trasaction_id,payment_reference_number");
        $table = "vendor_account_gl_transaction_history";
        $stofa = $this->db->where(["vendor_id" => $vendor_id])->limit($this->limit)->order_by("id", "desc")->get($table)->result_array();
        $this->responseMessage = count($stofa) . " results found";
        $this->responseData = $stofa;
        $this->returnResponse();
    }

    public function getVendorLiquidationRequest($vendor_id)
    {
        $this->db->select("*");
        $table = "liquidation_requests";
        $stofa = $this->db->where(["vendor_id" => $vendor_id])->limit($this->limit)->order_by("id", "desc")->get($table)->result_array();
        $this->responseMessage = count($stofa) . " results found";
        $this->responseData = $stofa;
        $this->returnResponse();
    }

    /**
     * @param $vendor_id
     * @param $order_id
     */
    public function updateOrderStatus($vendor_id, $order_id)
    {
    }

    public function getVendorOrdersExtensions($vendor_id)
    {
    }

    public function getVendorReports($vendor_id)
    {
    }

    public function vendorAddItem($vendor_id)
    {
        $vendor_details=$this->getUserAccount($vendor_id);

        $itemNumberSalt = 1000;
        $input_data = $this->post_data?:[];
        $this->helper->cleanArrayInput($input_data);

        $input_data['added_by'] = $vendor_id;
        $expected = ["price_point", "price", "brief_description", "features", "is_negotiable", "rear_view", "front_view", "side_view"];


        //get the price point
        $price_point = $input_data['price_point'];
        if ($price_point == 0 || empty($price_point) || !isset($price_point)) {
            $price_point = 1;
        }

        $input_data['price_point'] = $price_point;

        //test the year of make to make sure it is in range
        $year_of_make = (isset($input_data['year_of_make']) ? $input_data['year_of_make'] : null);

        if ($year_of_make <> null && is_numeric($year_of_make) && ($year_of_make < 1900 || $year_of_make > date('Y'))) {
            $this->responseMessage = "The Year of make (" . $input_data['year_of_make'] . ") is not in the accepted range 1950 ~ " . date('Y');
            $this->returnResponse();
        }

        //upload the images
        $rear = $this->uploadItemImages('rear', $input_data['rear_view']);
        $front = $this->uploadItemImages('front', $input_data['front_view']);
        $side = $this->uploadItemImages('side', $input_data['side_view']);

        if (!$rear || !$side | !$front) {
            $this->responseCode = 1000;
            $this->helper->log_to_system('adding item failed with error  ' . $this->responseMessage, $this->called_link, $this->unique_trace_id);
            $this->returnResponse();
        }

        $input_data['rear_view'] = $rear;
        $input_data['side_view'] = $side;
        $input_data['front_view'] = $front;

        $item_margin_percentage = $this->db->get("item_mark_up_percentage")->row();
        $input_data['item_margin_percentage'] = $item_margin_percentage <> null ? $item_margin_percentage->mark_up_percentage : (10 / 100);

        $item_price = !(empty($input_data['price']) && is_numeric($input_data['price'])) ? $input_data['price'] : 0;
        $input_data['price_without_markup'] = $item_price;
        $input_data['price'] = $item_price + ($item_price * ($item_margin_percentage->mark_up_percentage / 100) ?: (10 / 100));

        if ($vendor_details->email == "saccount@pangisa.co.ug") {
            $input_data['is_sales_default_account'] = 'True';
        }

        $this->db->insert("items", $input_data);
        $itemId = $this->db->insert_id();
        $itemNumber = $itemNumberSalt + $itemId;

        $this->db->where(["id" => $itemId])->update("items", ["item_number" => $itemNumber]);
        $this->saveLog("items", "added", false, $itemId);

        if ($itemId) {
            $this->helper->log_to_system("adding item propery", $this->called_link, $this->unique_trace_id);

            $this->responseMessage = "Item inserted successfully with id ${itemId}";
            $this->responseCode = 0;
            $this->returnResponse();
        }

    }

    //create a liquidation request
    //create liquidation request
    public function create_liquidation_request($vendor_id)
    {
        $data = $this->post_data;
        $this->helper->cleanArrayInput($data);

        //$vendor
        $vendor = $this->db->where(['id' => $vendor_id])->get("users")->row();
        $balance = $this->db->where(['vendor_id' => $vendor_id])->get("vendor_account_gl")->row();

        if ($balance->momo_collection_number == null && $balance->bank_account_number == null) {
            $this->responseMessage = "Your Request cannot be accepted because you have not set the number on which to deposit your money";
            $this->responseCode = 1000;
            $this->returnResponse();
        }

        //update the receiving account if it exists
        //to be implemented later
        if($balance->momo_collection_number==null && $data['receiving_account']){}

        //check whether the vendor has un cleared vendor requests.
        $liquidation_requests = $this->db->where(['status' => 0, 'vendor_id' => $vendor_id])->limit(1)->get("liquidation_requests")->row();

        if ($data['amount'] > $balance->balance) {
            $this->responseMessage = sprintf("Amount Cannot be more than available Balance - Ugx %s", number_format($balance->balance));
            $this->responseCode = 1000;
            $this->returnResponse();
        }

        if ($data['amount'] < 500) {
            $this->responseMessage = "Amount Cannot be less than Ugx 500";
            $this->responseCode = 1000;
            $this->returnResponse();
        }

        if ($liquidation_requests) {
            $this->responseMessage = "You already have a pending request for Ugx " . $liquidation_requests->amount . " logged on " . $liquidation_requests->date . ", You might not log multiple Liquidation requests, this is to allow easy tracking. Contact our Administrator for more information";
            $this->responseCode = 1000;
            $this->returnResponse();
        }

        $liquidationInsertObject = [];
        $liquidationInsertObject['vendor_id'] = $vendor_id;
        $liquidationInsertObject['vendor_account'] = $vendor->user_code;
        $liquidationInsertObject['amount'] = $data['amount'];
        $liquidationInsertObject['current_balance'] = $balance->balance;
        $liquidationInsertObject['balance_after'] = $balance->balance - $data['amount'];
        $liquidationInsertObject['mode_of_transaction'] = $data['mode_of_transaction'];
        $liquidationInsertObject['receiving_account'] = $data['mode_of_transaction'] == "mobile_money" ? $balance->momo_collection_number : $balance->bank_account_number;
        $liquidationInsertObject['comments'] = $data['liquidation_notes'];

        //insert into database
        $this->db->insert('liquidation_requests', $liquidationInsertObject);

        $inserId = $this->db->insert_id();
        $reference_number = "LQ" . (10000000 + $inserId);

        //update the database and set the reference number
        $this->db->where(['id' => $inserId])->set(['reference_number' => $reference_number])->update("liquidation_requests");

        $this->responseMessage = "Your Request to Withdraw money has been received successfully, Once approved, you will receive your money automatically. REF# = " . $reference_number;
        $this->returnResponse();
    }

    public function uploadItemImages($type_of_image, $data)
    {
        $filename = strtoupper($type_of_image . md5($type_of_image) . time());
        $upload_path = "./items/";

        $image_parts = explode(";base64,", $data);
        $image_type_definition = explode("image/", $image_parts[0]);

        $actual_image_type = $image_type_definition[1];
        $image_base_64_content = base64_decode($image_parts[1]);


        if ($image_base_64_content) {

            if (!in_array($actual_image_type, ['jpg', 'jpeg', 'gif', 'png'])) {
                $this->responseMessage =  $type_of_image." Unknown image type";
                return null;
            }


            if ($image_base_64_content === false) {
                $this->responseMessage = $type_of_image." base 64 decode failed";
                return null;
            } else {
                file_put_contents($upload_path ."/". $filename . ".{$actual_image_type}", $image_base_64_content);
                return  $filename . ".{$actual_image_type}";
            }

        } else {
            $this->responseMessage =  $type_of_image." Image URI string is incorrect or damaged";
            return null;
        }

    }

    public function vendorEditItem($vendor_id, $item_id)
    {
        try {
            $input_data = $this->post_data;
            $this->helper->cleanArrayInput($input_data);
            $itemWithSameIdentification = $this->db->where(["identification_number" => $input_data['identification_number']])->get("items");

            if (count($itemWithSameIdentification->result_array()) > 1) {
                $this->responseMessage = "Duplicate Item Identification Number,Please choose a different one";
                $this->responseCode = 1000;
                $this->returnResponse();
            }


//        if (isset($_FILES['rear_view']) && $_FILES['rear_view']['name'] <> null) {
//            $rear = $this->uploadItemImages('rear', 'rear_view');
//            $input_data['rear_view'] = $rear;
//            $this->deleteImage("items/" . $item->rear_view);
//        }
//
//        if (isset($_FILES['front_view']) && $_FILES['front_view']['name'] <> null) {
//            $front = $this->uploadItemImages('front', 'front_view');
//            $input_data['front_view'] = $front;
//            $this->deleteImage("items/" . $item->front_view);
//        }
//
//        if (isset($_FILES['side_view']) && $_FILES['side_view']['name'] <> null) {
//            $side = $this->uploadItemImages('side', 'side_view');
//            $input_data['side_view'] = $side;
//            $this->deleteImage("items/" . $item->side_view);
//        }


            $item_margin_percentage = $this->db->get("item_mark_up_percentage")->row();
            $input_data['item_margin_percentage'] = $item_margin_percentage <> null ? $item_margin_percentage->mark_up_percentage : (10 / 100);

            $input_data['price_without_markup'] = $input_data['price'];
            $markuppercentage = $item_margin_percentage->mark_up_percentage ? ($item_margin_percentage->mark_up_percentage / 100) : (10 / 100);
            $input_data['price'] = $input_data['price'] + ($input_data['price'] * $markuppercentage);

            $this->db->where(['id' => $item_id, "added_by" => $vendor_id])->set($input_data)->update("items");
            $this->saveLog("items", "updated", true, $item_id);

            $this->responseMessage = "Successfully updated";
            $this->responseCode = 0;
            $this->returnResponse();
        } catch (Exception $exception) {
            $this->responseCode = 1000;
            $this->responseMessage = $exception->getMessage();
            $this->returnResponse();
        }
    }

    public function vendorDeleteItem($vendor_id, $item_id)
    {
    }

    //sys admin specific actions


    //chat system
    public function saveChatMessages()
    {
        $chatData = $this->post_data;

        if (count($chatData) > 0) {
            $receiver = $chatData['receiver'];
            $sender = $chatData['sender'];
            $message = $chatData['message'];

            $saveMessageObject = [];
            $saveMessageObject['sender'] = $sender;
            $saveMessageObject['receiver'] = $receiver;
            $saveMessageObject['message'] = $message;
            $saveMessageObject['date'] = date('Y-m-d h:i:s');
            $saveMessageObject['seen'] = 0;
            $saveMessageObject['deleted'] = 0;

            $this->db->insert('chats', $saveMessageObject);

            $insertId = $this->db->insert_id();

            if ($insertId) {
                $this->responseData = $insertId;
                $this->responseCode = 0;
                $this->responseMessage = "Saved Successfully";
            } else {
                $this->responseCode = 1000;
                $this->responseMessage = 'An internal error has occured';
                $this->responseData = null;
            }

        } else {
            $this->responseMessage = "No data found";
            $this->responseCode = 1000;
        }

        $this->returnResponse();
    }

    public function retrieveChatMessages($user_id, $offset = 0)
    {
        $chats = $this->db->where("receiver=$user_id or sender=$user_id")->limit($this->limit, $offset)->order_by("id", "asc")->get('chats')->result_array();
        if ($chats) {
            $this->responseData = $chats;
            $this->responseCode = 0;
        } else {
            $this->responseMessage = "No chats found";
            $this->responseCode = 1000;
            $this->responseData = null;
        }
        $this->returnResponse();
    }

    public function updateMessageSeenStatus($user_id)
    {
        if (!$user_id) {
            $this->responseMessage = "no user id supplied";
            $this->responseCode = 1000;
        } else {
            if ($this->db->where(['receiver' => $user_id])->set(['seen' => 1])->update('chats')) {
                $this->responseData = null;
                $this->responseMessage = "Successfully updated";
                $this->responseCode = 0;
            } else {
                $this->responseMessage = "internal server error";
                $this->responseCode = 1000;
            }
        }

        $this->returnResponse();
    }
}
