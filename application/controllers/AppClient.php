<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "CentralAccess.php";
//include APPPATH . "/controllers/Ballistics.php";
//include "Response.php";
//include "FlutterWavePayments.php";

class AppClient extends CI_Controller
{
    private $ctrl_name = 'AppClient';
    private $error = null;
    private $link = null;
    private $title = null;
    private $form_header_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $limit = 30;
    private $helper = null;
    private $pagination_display = null;
    private $userIdGlobal = null;
    private $portal = "vendor_portal/";
    private $backLink = null;
    private $called_link = null;
    private $unique_trace_id = null;
    private $ca_h = null;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Africa/Kampala");
        $this->load->library("Lib");
        $this->load->library("image_lib");
        $this->load->library("EmailHelper");
        $this->load->model("ItemsModel");
        $this->load->model("BusinessModel");
        $this->load->model("UserModel");
        $this->load->model("CommonDatabaseOperations");
        $this->link = base_url() . $this->ctrl_name . '/';

        $this->ca_h = new CentralAccess();
        $this->helper = $this->ca_h->getBallisticHelper();

        $this->userIdGlobal = $this->session->user['id'];
        $this->load->library("user_agent");
        $this->backLink = $this->agent->referrer();

        $this->called_link = $this->uri->uri_string();
        $this->unique_trace_id = strtoupper(uniqid("PANGISA-ER"));

        $_SESSION['called_link'] = $this->called_link;
        $_SESSION['unique_trace_id'] = $this->unique_trace_id;

        set_error_handler(array(&$this->helper, "my_error_handler"));
    }

    public function index()
    {
        try {

            $this->checkLogin();
            $this->title = "Pangisa - Dashboard";

            $total_orders = count($this->db->where(['owner' => $this->userIdGlobal])->get("orders")->result_array());
            $total_items = count($this->db->where(['added_by' => $this->userIdGlobal, 'status' => 1])->get("items")->result_array());

            $data['total_orders'] = $total_orders;
            $data['total_items'] = $total_items;
            $data['orders'] = $orders = $this->db
                ->select("o.id, u.name as client, i.name,  i.identification_number as Number,i.price, concat( o.number_of_days,' Days') as 'Period', (o.number_of_days*i.price) as Amount, o.place_of_use as 'Place of use', date(o.date) as Date ")
                ->from("orders o")
                ->join("items i", "i.id=o.item")
                ->join("users u", "u.id=o.client")
                ->where(["o.owner" => $this->session->user['id']])
                ->order_by("o.date desc")
                ->get()->result_array();

            $this->form_header_link = $this->link . "index";
            $this->form_header_title = "Go some where";
            $this->page_title = "Dashboard";
            $this->loadView("dashboard", $data);
        } catch (Exception $exception) {
            print json_encode($exception);
        }

    }

    public function checkLogin()
    {
        $usertype = $this->session->user['user_type'];

        if (!$this->session->user['id'] || $usertype <> "vendor") {
            Redirect(base_url() . "/Index/login");
        }
    }

    private function loadView($view = null, $information = null)
    {
        $view_folder = "vendor_portal/";
        $data['title'] = $this->title ? $this->title : "Pangisa";
        $data['view'] = $view ? $view_folder . $view : $view_folder . "dashboard";
        $data['form_header_title'] = $this->form_header_title;
        $data['form_header_link'] = $this->form_header_link;
        $data['data'] = $information;
        $data['pagination'] = $this->pagination_display;
        $data['error'] = $this->error;
        $portal = "admin_portal/";

        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($portal . "home", array("menu" => $this->buildSideMenu()));
    }

    private function buildSideMenu()
    {
        $link = $this->link;
        $menu = array(
            ["icon_color" => "#fff", "name" => "Dashboard", "icon" => "fa fa-tachometer", "link" => $link . 'index'],
            ["icon_color" => "#fff", "name" => "Items", "icon" => "fa fa-edit", "link" => $link . 'items'],
            ["icon_color" => "#fff", "name" => "Orders", "icon" => "fa fa-edit", "link" => $link . 'orders'],
            ["icon_color" => "#fff", "name" => "Order Extensions", "icon" => "fa fa-edit", "link" => $link . 'ExtendItemOrderRequest'],
            ["icon_color" => "#fff", "name" => "Account Statement", "icon" => "fa fa-money", "link" => $link . 'account_statement'],
            ["icon_color" => "#fff", "name" => "Self Liquidation", "icon" => "fa fa-money", "link" => $link . 'liquidation_requests'],
            ["icon_color" => "#fff", "name" => "Payments", "icon" => "fa fa-money", "link" => $link . 'payments'],
            ["icon_color" => "#fff", "name" => "Transaction History", "icon" => "fa fa-edit", "link" => $link . 'transaction_history'],
            ["icon_color" => "#fff", "name" => "Report", "icon" => "fa fa-file-pdf-o", "link" => $link . 'Reports/General'],
            ["icon_color" => "#fff", "name" => "My Account", "icon" => "fa fa-user-o", "link" => $link . 'account'],
            ["icon_color" => "red", "name" => "Logout", "icon" => "fa fa-power-off", "link" => $link . 'logout']
        );

        return $menu;
    }

    public function items($action = null, $id = null, $offset = 0, $limit = 100)
    {
        $this->checkLogin();
        $this->userCan();

        if ($action == "view") {
            //get the item from the database
            $itemToView = $this->db->select("*, (select name from categories where id=category) as category,(select name from users where id=added_by) as owner")->from("items")->where(["id" => $id, 'added_by' => $this->userIdGlobal])->get()->row();

            //if item is not there, then return error
            if (!$itemToView) {
                $this->error = "The item with the given Id does not exist or is not owned by you. Please retry";
            } else {
                $this->loadView("view_item_details", $itemToView);
                return;
            }
        } elseif ($action == "delete") {
            $item = $this->db->where(["id" => $id, "added_by" => $this->userIdGlobal])->get("items")->row();
            $orders_on_item = $this->db->where(["item" => $item->id])->get("orders")->result_array();

            unlink(APPPATH . "../items/" . $item->rear_view);
            unlink(APPPATH . "../items/" . $item->front_view);
            unlink(APPPATH . "../items/" . $item->side_view);

            if (count($orders_on_item) > 0) {
                $this->db->where(["id" => $id, "added_by" => $this->userIdGlobal])->set(['status' => 0])->update("items");
                $this->saveLog("items", "deleted", true, $id);
            } else {
                $this->db->where(["id" => $id, "added_by" => $this->userIdGlobal])->delete("items");
                $this->saveLog("items", "deleted", true, $id);
            }

            Redirect($this->ctrl_name . '/items');

        } elseif ($action == "addExpense") {
            $post_information = $this->input->post();
            $this->helper->cleanArrayInput($post_information);

            //save expense object
            $expenseObject = array(
                "owner" => $this->userIdGlobal,
                "item_id" => $id,
                "amount" => $post_information['amount'],
                "reason" => $post_information['reason'],
            );
            $this->db->insert("item_expenses", $expenseObject);
            Redirect($this->backLink);

        } elseif ($action == "promote_item") {
            $post_information = $this->input->post();
            $this->helper->cleanArrayInput($post_information);

            //save expense object
            $promoteItemObject = array(
                "owner" => $this->userIdGlobal,
                "item" => $id,
                "number_of_days" => $this->helper->subtractDateFromDate($post_information['end_date'], $post_information['start_date']),
                "amount" => $post_information['amount'],
                "rate" => null,
                "paid" => 0,
                "start_date" => $post_information['start_date'],
                "end_date" => $post_information['end_date'],
                "expired" => 0,
                "enabled" => 0,
                "target_group" => $post_information['target_group'],

            );

            $this->db->insert("promote_items", $promoteItemObject);

            //add to pangisa collections account

            $collectionsObject = array(
                "item" => $id,
                "owner" => $this->userIdGlobal,
                "reason" => PROMOTE_ITEM,
                "amount" => $post_information['amount'],
                "mark_up" => 0,
                "net_amount" => $post_information['amount'],
                "paid" => 0,
                "balance" => $post_information['amount'],
            );

            $this->db->insert("pangisa_collections", $collectionsObject);

            $collectionsInsertId = $this->db->insert_id();
            $this->db->where("id", $collectionsInsertId)->set("reference_number", "PROM" . str_pad($collectionsInsertId, 6, 0, STR_PAD_LEFT))->update("pangisa_collections");

            Redirect($this->backLink);
        }

        $this->db
            ->select("i.id,i.name, ca.name as  category,i.color,i.size,i.identification_number as 'Identification number',i.year_of_make as 'Year of Make',
                date(i.date_added) as date")
            ->from("items i")
            ->join("categories ca", "ca.id=i.category")
            ->where(['i.added_by' => $this->session->user['id'], "status" => 1]);

        $items = $this->db->limit($limit, $offset)->get()->result_array();
        $actions = array("<i class='fa fa-eye'></i>" => $this->link . "items/view", "<i class='fa fa-trash-o'></i>" => $this->link . "items/delete", "<i class='fa fa-edit'></i>" => $this->link . "edit_item");

        $table = $this->lib->DrawTable($items, $actions);

        $this->title = "Pangisa - Items";
        $this->form_header_link = $this->link . "addItems";
        $this->form_header_title = "Add Items";
        $this->page_title = "View Items";
        $this->loadSimpleView($table);
    }

    private function userCan()
    {
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

    private function loadSimpleView($information)
    {
        $data['title'] = $this->title ? $this->title : "Pangisa";
        $data['view'] = "render_view";
        $data['page_title'] = $this->page_title;
        $data['information'] = $information;
        $data['form_header_title'] = $this->form_header_title;
        $data['form_header_link'] = $this->form_header_link;
        $data['error'] = $this->error;
        $data['filter'] = $this->current_page_filter;
        $data['pagination'] = null;
        $portal = "admin_portal/";

        $this->load->view("admin_portal/admin_header", $data);
        $this->load->view($portal . "home", array("menu" => $this->buildSideMenu()));
    }

    public function edit_item($itemId, $action = null)
    {
        $this->checkLogin();

        $item = $this->db->where(["id" => $itemId, "added_by" => $this->userIdGlobal])->get("items")->row();
        if (!$item) {
            Redirect($this->link . "items");
        }

        if ($action == "save") {
            $input_data = $this->input->post();
            $this->helper->cleanArrayInput($input_data);
            $itemWithSameIdentification = $this->db->where(["identification_number" => $input_data['identification_number']])->get("items");

            if (count($itemWithSameIdentification->result_array()) > 1) {
                $this->error = "Duplicate Item Identification Number,Please choose a different one";
                $this->loadView('edit_item', $item);
                return;
            }


            if (isset($_FILES['rear_view']) && $_FILES['rear_view']['name'] <> null) {
                $rear = $this->uploadItemImages('rear', 'rear_view');
                $input_data['rear_view'] = $rear;
                $this->deleteImage("items/" . $item->rear_view);
            }

            if (isset($_FILES['front_view']) && $_FILES['front_view']['name'] <> null) {
                $front = $this->uploadItemImages('front', 'front_view');
                $input_data['front_view'] = $front;
                $this->deleteImage("items/" . $item->front_view);
            }

            if (isset($_FILES['side_view']) && $_FILES['side_view']['name'] <> null) {
                $side = $this->uploadItemImages('side', 'side_view');
                $input_data['side_view'] = $side;
                $this->deleteImage("items/" . $item->side_view);
            }


            $item_margin_percentage = $this->db->get("item_mark_up_percentage")->row();
            $input_data['item_margin_percentage'] = $item_margin_percentage <> null ? $item_margin_percentage->mark_up_percentage : (10 / 100);

            $input_data['price_without_markup'] = $input_data['price'];
            $markuppercentage = $item_margin_percentage->mark_up_percentage ? ($item_margin_percentage->mark_up_percentage / 100) : (10 / 100);
            $input_data['price'] = $input_data['price'] + ($input_data['price'] * $markuppercentage);

            $this->db->where(['id' => $itemId])->set($input_data)->update("items");
            $this->saveLog("items", "updated", true, $itemId);
            Redirect($this->link . "items/view/" . $itemId);
        } else {
            $this->loadView('edit_item', $item);
        }

    }

    private function uploadItemImages($type, $image)
    {
        $filename = strtoupper($type . md5($type) . time());
        $upload_path = "./items/";
        try {
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
                $this->error = "Saving Item Failed due to Error Uploading $type image. Description =  " . $this->upload->display_errors();
                return null;
            }

            //get the file upload data
            $file_data = $this->upload->data();

            //get the full path of the uploaded image
            $image_upload_full_path = $file_data['full_path'];
            $image_upload_file_ext = $file_data['file_ext'];
            $image_upload_file_size = $file_data['file_size'];
            $image_upload_file_is_image = $file_data['is_image'];

            //apply water mark on  image
            $this->watermarkImages($image_upload_full_path);

            //if file size is bigger than recommended,  then resize it
            if ($image_upload_file_size > 5) {
                $this->compressImage($image_upload_full_path, 100);
            }

            return $file_data['file_name'];
        } catch (Exception $exception) {
            $this->error = "File upload failed with the following Error. Description =  " . $this->upload->display_errors();
            return null;
        }
    }

    private function compressImage($image_path, $quality)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $image_path;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 200;
        $config['height'] = 200;

        $this->load->library('image_lib', $config);
        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
        }
    }

    private function watermarkImages($source_path)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source_path;
        $config['wm_text'] = 'https://www.pangisa.co.ug';
        $config['wm_overlay_path'] = APPPATH . '../resources/images/icon.png';
        $config['wm_opacity'] = '50';
        $config['wm_font_path'] = BASEPATH . '../system/fonts/Righteous-Regular.ttf';
        $config['wm_type'] = 'text';
        $config['wm_font_size'] = '20';
        $config['wm_font_color'] = '#dc314c';
        $config['wm_vrt_alignment'] = 'middle';
        $config['wm_hor_alignment'] = 'center';
        $config['wm_padding'] = '20';

        $this->image_lib->initialize($config);
        if (!$this->image_lib->watermark()) echo $this->image_lib->display_errors();
    }

    private function deleteImage($imagePath)
    {
        $image_local_patch = APPPATH . '../' . $imagePath;
        if (file_exists($image_local_patch)) {
            @unlink(APPPATH . '../' . $imagePath);
        }
    }

    public function SearchAssociativeArrayByIndex($table, $index)
    {
        $tableResults = $this->db->get($table)->result_array();

        $res = [];
        foreach ($tableResults as $category) {
            if ($category['id'] == $index) {
                $res = $category;
            }
        }
        return $res;
    }

    public function addItems($action = null, $id = null)
    {
        $this->checkLogin();
        $this->userCan();
        $itemNumberSalt = 1000;
        $this->title = "Pangisa - Add New Item";
        $this->form_header_link = $this->link . "items";
        $this->form_header_title = "View Items";
        $this->page_title = "Add New Item";

        if ($action == "save") {
            $input_data = $this->input->post();
            $this->helper->cleanArrayInput($input_data);
            $input_data['added_by'] = $this->session->user['id'];

            //get the price point
            $price_point = $input_data['price_point'];
            if ($price_point == 0 || empty($price_point) || !isset($price_point)) {
                $price_point = 1;
            }
            $input_data['price_point'] = $price_point;

            $itemUniqueIdentification = isset($input_data['identification_number']) ? $input_data['identification_number'] : null;

            if ($itemUniqueIdentification != null) {
                $itemExistsByIdentificationNumber = $this->db->where(['identification_number' => $itemUniqueIdentification])->get("items")->result_array();

                if (count($itemExistsByIdentificationNumber) > 0) {
                    $this->error = "An Item with a similar Identification number exists, Please retry";
                    $this->loadView("vendor_add_items", null);
                    return;
                }
            }

            //test the year of make to make sure it is in range
            $year_of_make = (isset($input_data['year_of_make']) ? $input_data['year_of_make'] : null);

            if ($year_of_make <> null && is_numeric($year_of_make) && ($year_of_make < 1900 || $year_of_make > date('Y'))) {
                $this->error = "The Year of make (" . $input_data['year_of_make'] . ") is not in the accepted range 1950 ~ " . date('Y');
                $this->loadView("vendor_add_items", null);
                return;
            }

            //upload the images
            $rear = $this->uploadItemImages('rear', 'rear_view');
            $front = $this->uploadItemImages('front', 'front_view');
            $side = $this->uploadItemImages('side', 'side_view');

            //if the upload failed
            if ($rear == null || $front == null || $side == null) {
                $this->loadView("vendor_add_items", null);
                return;
            }

            $input_data['rear_view'] = $rear;
            $input_data['side_view'] = $side;
            $input_data['front_view'] = $front;

            $item_margin_percentage = $this->db->get("item_mark_up_percentage")->row();
            $input_data['item_margin_percentage'] = $item_margin_percentage <> null ? $item_margin_percentage->mark_up_percentage : (10 / 100);

            $item_price = !(empty($input_data['price']) && is_numeric($input_data['price'])) ? $input_data['price'] : 0;
            $input_data['price_without_markup'] = $item_price;
            $input_data['price'] = $item_price + ($item_price * ($item_margin_percentage->mark_up_percentage / 100) ?: (10 / 100));

            if ($this->session->user['email'] == "saccount@pangisa.co.ug") {
                $input_data['is_sales_default_account'] = 'True';
            }

            $this->db->insert("items", $input_data);
            $itemId = $this->db->insert_id();
            $itemNumber = $itemNumberSalt + $itemId;

            $this->db->where(["id" => $itemId])->update("items", ["item_number" => $itemNumber]);
            $this->saveLog("items", "added", false, $itemId);

            Redirect($this->ctrl_name . '/items');
        }

        $this->loadView("vendor_add_items", null);
    }

    public function orders($action = null, $id = null, $offset = 0, $limit = 100)
    {
        $this->checkLogin();
        $client_id = null;
        try {
            if ($action == "approve") {

                //before an order is approved, check that the item exits
                $order_to_approve = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();
                if (!$order_to_approve) {
                    $this->error = "Order Does not Exist or was deleted";
                } elseif ($order_to_approve->approved <> 0) {
                    //check the current status of the order
                    $this->error = "Order " . $order_to_approve->order_number . " is already " . ($order_to_approve->approved == 1 ? 'Approved' : 'Rejected');
                } else {
                    //get the order item
                    $order_item = $this->db->where(['id' => $order_to_approve->item])->get('items')->row();
                    if (!$order_item) {
                        $this->error = "The Order item was deleted or is non existent";
                    } else {
                        $this->db->trans_start();
                        $update = array(
                            "pick_up_date" => $this->input->post("pick_up_date"),
                            "comment" => $this->input->post("comment"),
                            "approved" => 1,
                            "approval_date" => date("y-m-d h:i:s"),
                        );
                        $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->update("orders", $update);
                        $this->saveLog("orders", "approved", false, $id);

                        //update item availability
                        $item_id = $this->db->where("id", $id)->get("orders")->row()->item;
                        $this->db->where("id", $item_id)->set(array("availability" => 0))->update("items");
                        $item = $this->db->where("id", $item_id)->get("items")->row();

                        //register a collection from the vendor when order is approved
                        $orderToUpdate = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();

                        //get the vendor name
                        $vendor = $this->CommonDatabaseOperations->getVendorById($orderToUpdate->owner);

                        //record initial payment for the client account
                        $payment_balance = array(
                            "amount" => $orderToUpdate->order_amount,
                            "owner" => $this->userIdGlobal,
                            "client" => $orderToUpdate->client,
                            "reason" => 'Order ' . $orderToUpdate->order_number . ' for ' . $item->name,
                            "owner_name" => $vendor->name,
                            "item" => $orderToUpdate->item,
                            "order_number" => $orderToUpdate->order_number,
                            "total_paid" => 0,
                            "balance" => $orderToUpdate->order_amount,
                            "order_markup" => $orderToUpdate->markup_at_order,
                        );
                        $this->db->insert("payment_balances", $payment_balance);

                        //register collections from vendor
                        $collectionsObject = array(
                            "item" => $id,
                            "client" => $orderToUpdate->client,
                            "owner" => $this->userIdGlobal,
                            "reason" => 'ORDER_MARKUP',
                            "amount" => ($orderToUpdate->markup_at_order),
                            "mark_up" => (($orderToUpdate->markup_at_order / $orderToUpdate->order_amount) * 100),
                            "net_amount" => $orderToUpdate->order_amount - $orderToUpdate->markup_at_order,
                            "order_number" => $orderToUpdate->order_number,
                            "paid" => 0,
                            "balance" => ($orderToUpdate->markup_at_order),
                        );
                        $this->db->insert("pangisa_collections", $collectionsObject);
                        $collectionsInsertId = $this->db->insert_id();
                        $this->db->where("id", $collectionsInsertId)->set("reference_number", "MARKUP" . str_pad($collectionsInsertId, 6, 0, STR_PAD_LEFT))->update("pangisa_collections");

                        //send notification
                        $item_client = $this->db->where("id", $orderToUpdate->client)->get("users")->row();
                        $clientSmsMessageNotification = array(
                            "message" => "Dear $item_client->name , Your order for " . $orderToUpdate->order_number . " has been Approved, Please Proceed and make payments ",
                            "client" => $item_client->id,
                            "status" => 0,
                            "phone_number" => $item_client->phone,
                        );

                        //inset into que for client
                        $this->db->insert("message_outbox", $clientSmsMessageNotification);

                        //send email to client
                        $this->saveClientOrderApprovalNotificationEmail($id);

                        //commit transaction
                        $this->db->trans_complete();
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                        } else {
                            $this->db->trans_commit();
                        }

                        Redirect($this->link . "orders/view/" . $id);
                    }
                }
            } elseif ($action == "reject") {
                $orderToUpdate = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();
                if ($orderToUpdate && count($orderToUpdate) > 0) {
                    $update = array(
                        "comment" => $this->input->post("comment"),
                        "approved" => 2,
                        "approval_date" => date("y-m-d h:i:s")
                    );

                    $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->update("orders", $update);
                    $this->saveLog("orders", "rejected", false, $id);

                    //send notification

                    $item_client = $this->db->where("id", $orderToUpdate->client)->get("users")->row();
                    $message = array(
                        "message" => "Dear $item_client->name , Your order " . $orderToUpdate->order_number . " has been Rejected. Please login and try a another order",
                        "client" => $item_client->id,
                        "status" => 0,
                        "phone_number" => $item_client->phone,
                    );

                    //notify the client that the order was rejected
                    $this->saveClientOrderRejectNotificationEmail($id);

                    //inset into que for client
                    $this->db->insert("message_outbox", $message);

                    //redirect to orders
                    Redirect($this->link . "orders/");
                } else {
                    $this->error = "Order not found, please make sure order id is correct";
                }
            } elseif ($action == "view") {
                $order = $this->db->where(["o.id" => $id, "o.owner" => $this->userIdGlobal])
                    ->select("
                    o.number_of_days, o.place_of_use, o.usage_description, o.date,o.order_amount as amount,o.approved,o.id,o.approved,o.comment,
                    u.name,u.location,o.number_of_days,o.quantity,
                    pr.name as price_point,o.item_negotiation_completed,o.is_negotiable,
                    i.name as item_name,i.identification_number,i.brief_description,i.pick_up_location, rear_view, front_view , side_view,o.item_price_at_order as price
                ")
                    ->from("orders o")
                    ->join("items i", "i.id=o.item")
                    ->join("users u", "u.id=o.client")
                    ->join("price_points pr", "pr.id=i.price_point")
                    ->get()
                    ->result_Array();

                if (count($order) > 0) {
                    $this->loadView("order_details", $order[0]);
                    return;
                } else {
                    $this->error = "Order with given id not found.";
                }

            }

            $orders = $this->db
                ->select("o.id, o.order_number as 'order number', u.name as client, i.name,  i.identification_number as Number,i.price, 
                concat( o.number_of_days,' Days') as 'Period', o.quantity as quantity, o.order_amount as Amount, o.place_of_use as 'Place of use', 
                date(o.date) as Date,
                (select case when o.approved=1 then 'Approved'  when o.approved=0 then 'Pending' else 'Rejected' end) as status 
                ")
                ->from("orders o")
                ->join("items i", "i.id=o.item")
                ->join("users u", "u.id=o.client")
                ->where(["o.owner" => $this->userIdGlobal])
                ->order_by("o.date desc")
                ->get()->result_array();

            $actions = array(
                "<i class='fa fa-eye'></i>" => $this->link . "orders/view",
                "<i class='fa fa-trash-o'></i>" => $this->link . "orders/delete",
            );

            $table = $this->lib->DrawTable($orders, $actions);

            $this->title = "Pangisa - Orders";
            $this->form_header_link = null;
            $this->form_header_title = null;
            $this->page_title = "View Orders";
            $this->loadSimpleView($table);

        } catch (Exception $exception) {
            $this->helper->log_to_system($exception, $this->called_link, $this->unique_trace_id);
            $this->db->trans_complete();
            $this->db->trans_rollback();
        }
    }

    //used only for negotiable orders
    public function updateOrderPricing($id)
    {
        //before an order is approved, check that the item exits
        $order_to_approve = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();
        if (!$order_to_approve) {
            $this->error = "Order Does not Exist or was deleted";
        } elseif ($order_to_approve->approved <> 0) {
            //check the current status of the order
            $this->error = "Order " . $order_to_approve->order_number . " is already " . ($order_to_approve->approved == 1 ? 'Approved' : 'Rejected');
        } else {
            //get the order item
            $order_item = $this->db->where(['id' => $order_to_approve->item])->get('items')->row();
            if (!$order_item) {
                $this->error = "The Order item was deleted or is non existent";
            } else {
                $this->db->trans_start();

                $newOrderDetails = $this->input->post();
                $quantity = $newOrderDetails['quantity'];
                $price = $newOrderDetails['price'];
                $number_of_days = $newOrderDetails['number_of_days'];

                $order_item = $this->db->where(['id' => $order_to_approve->item])->get("items")->row();
                $mark_up_percentage = $order_item->item_margin_percentage;

                $markup_at_order = ($mark_up_percentage / 100) * $price;
                $price_with_margin = $markup_at_order + $price;
                $order_amount = $price_with_margin * $number_of_days * $quantity;

                $updateObject = [];
                $updateObject['quantity'] = $quantity;
                $updateObject['markup_at_order'] = $markup_at_order;
                $updateObject['order_amount'] = $order_amount;
                $updateObject['item_price_at_order'] = $price;
                $updateObject['item_negotiation_completed'] = 1;
                $updateObject['number_of_days'] = $number_of_days;

                $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->set($updateObject)->update('orders');

                //commit transaction
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                Redirect($this->link . "orders/view/" . $id);
            }
        }
    }

    //approve order reserve
    public function approveOrder($id)
    {
        //before an order is approved, check that the item exits
        $order_to_approve = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();
        if (!$order_to_approve) {
            $this->error = "Order Does not Exist or was deleted";
        } else {
            //get the order item
            $order_item = $this->db->where(['id' => $order_to_approve->item])->get('items')->row();
            if (!$order_item) {
                $this->error = "The Order item was deleted or is non existent";
            } else {
                $this->db->trans_start();
                $update = array(
                    "pick_up_date" => $this->input->post("pick_up_date"),
                    "comment" => $this->input->post("comment"),
                    "approved" => 1,
                    "approval_date" => date("y-m-d h:i:s"),
                );
                $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->update("orders", $update);
                $this->saveLog("orders", "approved", false, $id);

                //update item availability
                $item_id = $this->db->where("id", $id)->get("orders")->row()->item;
                $this->db->where("id", $item_id)->set(array("availability" => 0))->update("items");

                //register a collection from the vendor when order is approved
                $orderToUpdate = $this->db->where(["id" => $id, 'owner' => $this->userIdGlobal])->get("orders")->row();

                //get the vendor name
                $vendor = $this->CommonDatabaseOperations->getVendorById($orderToUpdate->owner);

                //record initial payment for the client account
                $payment_balance = array(
                    "amount" => $orderToUpdate->order_amount,
                    "client" => $this->userIdGlobal,
                    "owner" => $orderToUpdate->owner,
                    "owner_name" => $vendor->name,
                    "item" => $orderToUpdate->item,
                    "order_number" => $orderToUpdate->order_number,
                    "total_paid" => 0,
                    "balance" => $orderToUpdate->order_amount,
                );
                $this->db->insert("payment_balances", $payment_balance);

                //register collections from vendor
                $collectionsObject = array(
                    "item" => $id,
                    "client" => $orderToUpdate->client,
                    "owner" => $this->userIdGlobal,
                    "reason" => 'ORDER_MARKUP',
                    "amount" => ($orderToUpdate->markup_at_order),
                    "mark_up" => (($orderToUpdate->markup_at_order / $orderToUpdate->order_amount) * 100),
                    "net_amount" => $orderToUpdate->order_amount - $orderToUpdate->markup_at_order,
                    "order_number" => $orderToUpdate->order_number,
                    "paid" => 0,
                    "balance" => ($orderToUpdate->markup_at_order),
                );
                $this->db->insert("pangisa_collections", $collectionsObject);
                $collectionsInsertId = $this->db->insert_id();
                $this->db->where("id", $collectionsInsertId)->set("reference_number", "MARKUP" . str_pad($collectionsInsertId, 6, 0, STR_PAD_LEFT))->update("pangisa_collections");

                //send notification
                $item_client = $this->db->where("id", $orderToUpdate->client)->get("users")->row();
                $clientSmsMessageNotification = array(
                    "message" => "Dear $item_client->name , Your order " . $orderToUpdate->order_number . " has been Approved, Please Proceed and make payments ",
                    "client" => $item_client->id,
                    "status" => 0,
                    "phone_number" => $item_client->phone,
                );

                echo json_encode($clientSmsMessageNotification);
                //inset into que for client
                $this->db->insert("message_outbox", $clientSmsMessageNotification);

                //send mail to client
                $SUBJECT = $orderToUpdate->order_number . " - YOUR ORDER HAS BEEN APPROVED";
                $seller_message_for_item_order = "Hello $item_client->name, your order ( Order Number : $orderToUpdate->order_number ) has been approved. <br>";
                $seller_message_for_item_order .= "<br><br>Please Proceed with the payments by visiting any Centenary  Bank Agent to use Sasula or  choose Agency Banking or pay using Mobile Money off the portal ";
                $seller_message_for_item_order .= "<br>Pick up Date : " . $update['pick_up_date'] . "<br>";
                $seller_message_for_item_order .= "<br>Remarks : " . $update['comment'];
                $seller_message_for_item_order .= "<br>Approval Date : " . $update['approval_date'];

                $clientEmailNotification = [];
                $clientEmailNotification['client'] = $item_client->id;
                $clientEmailNotification['owner'] = $vendor->id;
                $clientEmailNotification['action'] = "APPROVED_ORDER";
                $clientEmailNotification['body'] = $seller_message_for_item_order;
                $clientEmailNotification['email_address'] = $item_client->email;
                $clientEmailNotification['status'] = 0;
                $clientEmailNotification['subject'] = $SUBJECT;
                echo json_encode($clientEmailNotification);
                $this->db->insert("email_outbox", $clientEmailNotification);

                //commit transaction
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                Redirect($this->link . "orders/view/" . $id);
            }
        }
    }

    public function users($action = null, $id = null)
    {
        $this->checkLogin();
        $this->userCan();

        $form = $this->UserModel->getForm();
        $submit_link = $this->link . 'users/save';
        $table = $this->lib->DrawFormTabular($submit_link, $form, null);

        if ($action == "add") {
            $this->title = "Pangisa - Add New User";
            $this->form_header_link = $this->link . "users";
            $this->form_header_title = "View Users";
            $this->page_title = "Addd New User";
            $this->loadSimpleView($table);
        } elseif ($action == "save") {
            $input_information = $this->input->post();

            if ($input_information['password'] <> $input_information['confirm_password']) {
                $this->error == "Passwords are not the same";
                $this->title = "Pangisa - Add New User";
                $this->form_header_link = $this->link . "users";
                $this->form_header_title = "View Users";
                $this->page_title = "Addd New User";
                $this->loadSimpleView($table);
                return;
            } else {
                $input_information['password'] = sha1($input_information['password']);
                $this->UserModel->save($input_information);
                Redirect($this->link . 'users');
            }

        } else {
            $users = $this->UserModel->retrieve([], 100, 0);
            $actions = array("<i class='fa fa-lock'></i>" => $this->link . "users/block", "<i class='fa fa-trash-o'></i>" => $this->link . "users/delete");

            $table = $this->lib->DrawTable($users, $actions);

            $this->title = "Pangisa - Users";
            $this->form_header_link = $this->link . "users/add";
            $this->form_header_title = "Add New User";
            $this->page_title = "View Users";
            $this->loadSimpleView($table);
        }

    }

    public function ConvertNumberToMonth($number)
    {
        $res = null;
        switch ($number) {
            case 1:
                $res = "Jan";
                break;
            case 2:
                $res = "Feb";
                break;
            case 3:
                $res = "Mar";
                break;
            case 4:
                $res = "April";
                break;
            case 5:
                $res = "May";
                break;
            case 6:
                $res = "June";
                break;
            case 7:
                $res = "July";
                break;
            case 8:
                $res = "Aug";
                break;
            case 9:
                $res = "Sept";
                break;
            case 10:
                $res = "Oct";
                break;
            case 11:
                $res = "Nov";
                break;
            case 12:
                $res = "Dec";
                break;
        }
        return $res;
    }

    public function account($action = null, $id = null)
    {
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
                    "date_modified" => date("y-m-d h:i:s"),
                    "phone" => $input_data['phone'],
                    "location" => $input_data['location'],
                    "alternative_phone" => $input_data['alternative_phone']
                );

                //check if the password has been updated
                if ($input_data['password'] <> null && $input_data['password'] != '' && !empty($input_data['password'])) {
                    $update_user["password"] = sha1($input_data['password']);
                    $update_user["password_last_modified"] = date("y-m-d h:i:s");
                }

                $this->db->where(["id" => $this->session->user['id']])->update("users", $update_user);
                Redirect($this->link . "account");
            }
        }

        $this->loadView("myaccount", $data);
    }

    public function logout()
    {
        $this->saveLog("users", "logged out", false, null);

        $this->session->sess_destroy();
        Redirect(base_url());
    }

    public function transaction_history($offset = null, $order_number = null)
    {
        $this->checkLogin();

        $filter = $this->input->post();
        $filter_information = $this->helper->load_params($filter);

        $this->db->select("
        t.id,t.order_number as order,t.client_name as client,t.transactionid,t.ext_transaction_id,t.payee_number, t.amountpayable,
         t.amount,t.channel_name as channel, 
        t.payment_date as 'pay date', (select case when t.status=1 then 'paid' else 'pending' end) as status,
        date(t.date) as date");
        $this->db->from("transaction_history t");
        $this->db->where(array_merge(["owner" => $this->session->user['id']], $filter_information));

        $transactions = $this->db->limit($this->limit, $offset)->order_by("t.date desc")->get()->result_array();

        $table = $this->lib->DrawTable($transactions, null);
        $total = $this->db->count_all("transactions");
        $pagination = $this->pagination("transactions/", $total, $this->limit);

        $this->title = "Pangisa - Transactions";
        $this->form_header_link = $this->link . "addItems";
        $this->form_header_title = null;
        $this->page_title = "My Transactions History";
        $this->loadSimpleView($table, $pagination);
    }

    public function pagination($link, $total, $per_page)
    {
        $config['base_url'] = $this->link . $link;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    public function payments($offset = 0)
    {
        $this->checkLogin();
        $this->page_title = "PAYMENTS";
        $filtet_string = $this->input->post();

        if (count($filtet_string) > 0) {
            $this->db->where(array_merge(["owner" => $this->session->user['id']], $filtet_string));
        } else {
            $this->db->where(["owner" => $this->session->user['id']]);
        }

        $payments = $this->db
            ->Select("p.*, u.name as client, i.name as item, i.identification_number")
            ->from("payment_balances p")
            ->join("users u", "u.id=p.client")
            ->join("items i", "i.id=p.item")
            ->limit($this->limit, $offset)
            ->get()
            ->result_array();

        $this->helper->RemoveElementFromAssociativeArray($payments, "Owner");
        $this->filter_submit_link = $this->link . "payments";
        $this->loadView("payments", $payments);
    }

    //view liquidation requests
    public function liquidation_requests($action = null, $offset = 0)
    {
        $this->checkLogin();
        $responseData = [];

        try {
            //get a summary of the account transactions
            $vendorAccountGl = $this->db->where(['vendor_id' => $this->userIdGlobal])->get("vendor_account_gl")->row();
            $responseData['vendor_account_gl'] = $vendorAccountGl;

            //get a summary of the liquidation requests
            $liquidation_requests = $this->db->where(['vendor_id' => $this->userIdGlobal])->order_by("id", "desc")->limit($this->limit, $offset)->get("liquidation_requests")->result_array();
            $responseData['liquidation_requests'] = $liquidation_requests;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        $this->loadView("liquidation_requests", $responseData);
    }

    //create liquidation request
    public function create_liquidation_request()
    {
        $data = $this->input->post();
        $this->helper->cleanArrayInput($data);

        //$vendor
        $vendor = $this->db->where(['id' => $this->userIdGlobal])->get("users")->row();
        $balance = $this->db->where(['vendor_id' => $this->userIdGlobal])->get("vendor_account_gl")->row();

        if ($balance->momo_collection_number == null || $balance->bank_account_number == null) {
            echo "Your Request cannot be accepted because you have not set the number on which to deposit your money";
            return;
        }

        //check whether the vendor has un cleared vendor requests.
        $liquidation_requests = $this->db->where(['status' => 0, 'vendor_id' => $this->userIdGlobal])->limit(1)->get("liquidation_requests")->row();

        if ($data['amount'] > $balance->balance) {
            echo sprintf("Amount Cannot be more than available Balance - Ugx %s", number_format($balance->balance));
            return;
        }

        if ($data['amount'] < 500) {
            echo "Amount Cannot be less than Ugx 500";
            return;
        }

        if ($liquidation_requests) {
            echo "You already have a pending request for Ugx " . $liquidation_requests->amount . " logged on " . $liquidation_requests->date . ", You might not log multiple Liquidation requests, this is to allow easy tracking. Contact our Administrator for more information";
            return;
        }

        $liquidationInsertObject = [];
        $liquidationInsertObject['vendor_id'] = $this->userIdGlobal;
        $liquidationInsertObject['vendor_account'] = $vendor->user_code;
        $liquidationInsertObject['amount'] = $data['amount'];
        $liquidationInsertObject['current_balance'] = $balance->balance;
        $liquidationInsertObject['balance_after'] = $balance->balance - $data['amount'];
        $liquidationInsertObject['mode_of_transaction'] = $data['mode_of_transaction'];
        $liquidationInsertObject['receiving_account'] = $data['mode_of_transaction'] == "mobile_money" ? $balance->momo_collection_number : $balance->bank_account_number;
        $liquidationInsertObject['comments'] = $data['comments'];

        //insert into database
        $this->db->insert('liquidation_requests', $liquidationInsertObject);

        $inserId = $this->db->insert_id();
        $reference_number = "LQ" . (10000000 + $inserId);

        //update the database and set the reference number
        $this->db->where(['id' => $inserId])->set(['reference_number' => $reference_number])->update("liquidation_requests");

        echo "Your Request to Withdraw money has been received successfully, Once approved, you will receive your money automatically. REF# = " . $reference_number;
    }

    //account statement, view how your vendor account has been transacting
    public function account_statement($offset = 0)
    {
        $this->checkLogin();
        $responseData = [];

        try {
            //get a summary of the account transactions
            $vendorAccountGl = $this->db->where(['vendor_id' => $this->userIdGlobal])->get("vendor_account_gl")->row();
            $responseData['vendor_account_gl'] = $vendorAccountGl;

            //get the last {n} number of transactions
            $vendorAccountTransactionsGl = $this->db->where(['vendor_id' => $this->userIdGlobal])->order_by('id', 'desc')->limit($this->limit, $offset)->get("vendor_account_gl_transaction_history")->result_array();
            $responseData['vendor_account_gl_transaction_history'] = $vendorAccountTransactionsGl;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        $this->loadView("vendor_account_gl", $responseData);

    }

    //accept order extension
    public function ExtendItemOrderRequest($action = null, $id = null)
    {
        $this->checkLogin();
        $orderToExtend = null;
        $order_id = $id ? $id : null;
        $customer = null;

        if ($order_id) {
            $orderToExtend = $this->db->select(
                "ext.id, ext.client,o.order_number, o.number_of_days, ext.number_of_days as ext_number_of_days, date(ext.start_date) as start_date, 
                date(ext.stop_date) as stop_date, ext.reason, ext.approved, ext.status_reason,
             i.id as item_id, i.name,i.size,i.price, p.name as price_point, date(ext.date) as date"
            )
                ->from("extended_orders ext")
                ->join("orders o", "o.order_number=ext.order_number")
                ->join("items i", "i.id=o.item")
                ->join("price_points p", "p.id=i.price_point")
                ->where(["ext.id" => $order_id, "o.owner" => $this->userIdGlobal])
                ->order_by("ext.date desc")
                ->limit(50)
                ->get()
                ->row();
            $customer = $this->db->where("id", $orderToExtend->client)->get("users")->row();

            if ($orderToExtend->approved <> VENDOR_PENDING_OPERATION) {
                $action = null;
                $id = null;
            }
        }


        if ($action == "accept") {
            $balance = $this->db->where("order_number", $orderToExtend->order_number)->get("payment_balances")->row();

            if ($balance):
                $new_balance = $balance->balance + ($orderToExtend->price * $orderToExtend->ext_number_of_days);
                $updateTotalPaidBalance = array(
                    "balance" => $new_balance,
                    "amount" => ($orderToExtend->price * $orderToExtend->ext_number_of_days)
                );
                //update the balances
                $this->db->set($updateTotalPaidBalance)->where(["id" => $balance->id, "owner" => $this->userIdGlobal])->update("payment_balances");
                $this->db->where("id", $orderToExtend->id)->set(["approved" => VENDOR_ACCEPT_OPERATION])->update("extended_orders");

                //send sms
                $message = array(
                    "message" => "Dear $customer->name , Your order extension requests for " . $orderToExtend->order_number . " has been accepted. Please deposit full amount to complete payment. Thank you.",
                    "owner" => $this->session->user['id'],
                    "client" => $customer->id,
                    "item" => $orderToExtend->item_id,
                    "status" => 0,
                    "messageid" => null,
                    "phone_number" => $customer->phone,
                );

                $this->db->insert("message_outbox", $message);
            endif;
        } elseif ($action == "reject") {

            $this->db->where(["id" => $orderToExtend->id, "owner" => $this->userIdGlobal])->set(["approved" => VENDOR_REJECT_OPERATION])->update("extended_orders");

            $message = array(
                "message" => "Dear $customer->name , Your order extension requests for " . $orderToExtend->order_numebr . " has been rejected by the vendor. Please Continue as return item back to vendor. Thank you",
                "owner" => $this->session->user['id'],
                "client" => $customer->id,
                "item" => $orderToExtend->item_id,
                "status" => 0,
                "messageid" => null,
                "phone_number" => $customer->phone,
            );
            $this->db->insert("message_outbox", $message);

        }

        $extendedOrders = $this->db->select(
            "ext.id,o.order_number, o.number_of_days, ext.number_of_days as ext_number_of_days, date(ext.start_date) as start_date, 
            date(ext.stop_date) as stop_date, ext.reason, ext.approved, ext.status_reason,
             i.id as item_id, i.name,i.size,i.price, p.name as price_point, date(ext.date) as date"
        )
            ->from("extended_orders ext")
            ->join("orders o", "o.order_number=ext.order_number")
            ->join("items i", "i.id=o.item")
            ->join("price_points p", "p.id=i.price_point")
            ->where("ext.owner=" . $this->userIdGlobal)
            ->order_by("ext.date desc")
            ->limit(50)
            ->get()
            ->result_array();

        $data['extendedOrders'] = $extendedOrders;

        $this->loadView("extendOrder", $data);
    }

    public function AjaxRetrieveSubCategories()
    {
        $this->checkLogin();

        $subcategories = $this->db->where("category", $this->helper->cleanInput($this->input->post("category")))->get("sub_categories")->result_array();
        $res = "";
        $res .= '<select class="form-control sub_categories"   name="sub_category">';
        $res .= "<option>Please Choose Sub Category</option>";

        foreach ($subcategories as $subcategory) {
            $res .= "<option value='" . $subcategory['id'] . "'>" . $subcategory['name'] . "</option>";
        }

        $res .= "</select>";
        echo $res;
    }

    public function payments_to_pangisa($action = null, $id = null)
    {
        $this->checkLogin();
        $paymentsInOwing = null;

        if (!$action):
            $paymentsInOwing = $this->db->select("pc.id,pc.amount, net_amount, date(pc.date) as date, pc.paid, pc.balance,pc.reference_number, pc.order_amount,  pc.last_transaction_date, pc.reason")
                ->from("pangisa_collections pc")
                ->where(['owner' => $this->userIdGlobal])
                ->limit($this->limit)
                ->get()
                ->result_array();

            $this->loadView("view_pangisa_collections", $paymentsInOwing);
        endif;

    }

    public function Reports($type = null, $event = "view")
    {
        $this->checkLogin();

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

            $this->db->where(['owner' => $this->userIdGlobal])->select("
                 count(*) as total, date(date) as transaction_date,    
                (select count(*) from  transaction_history where status=1 and date(date)=transaction_date) as successful,
                (select count(*) from  transaction_history where status=0 and date(date)=transaction_date) as pending
            ")->from("transaction_history");

            $transactionsResult = $this->db->order_by("date desc, owner desc, total desc, successful desc")->group_by("transaction_date, owner")->get()->result_array();
            $responseObject['transactionResultsGeneral'] = $transactionsResult;


            $this->db->where(['owner' => $this->userIdGlobal])->select("channel_name as channel, count(*) as total, date(date) as transaction_date,    (select count(*) from  transaction_history where status=1 and date(date)=transaction_date) as successful,(select count(*) from  transaction_history where status=0 and date(date)=transaction_date) as pending ")
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

            $this->db->where(['added_by' => $this->userIdGlobal])->select("
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

            $this->db->where(['owner' => $this->userIdGlobal])->select("
                 p.*,   
                (select name from users where id=p.client) as   client,
                (select name from items where id=p.item) as   item_name,
                (select  item_number from items where id=p.item) as   item_number,               
                ")->from("payment_balances p");

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

            $this->db->where(['owner' => $this->userIdGlobal])->select("
                 o.order_number,o.date, 
                (select name  from  items where id=o.item ) as item,  
                (select count(*) from  orders where item=o.item   $dbDateString ) as orders,
                (select count(*) from  orders where item=o.item and  approved=1  $dbDateString ) as successful,
                (select count(*) from  orders where item=o.item and  approved=0  $dbDateString) as pending,
                (select count(*) from  orders where item=o.item and  user_cancel=1  $dbDateString) as user_cancelled,
                (select sum(order_amount) from  orders where item=o.item     $dbDateString) as order_amount
            ")->from("orders o");

            $ordersResult = $this->db->order_by("o.date", "desc")->order_by("successful", "desc")->group_by("o.date, o.item")->get()->result_array();
            $responseObject['ordersResultsGeneral'] = $ordersResult;


        } else if ($type == "general") {
            $ReportHeading = "General Perfomance Report";
            $cur_date = date('Y-m-d');
            $cur_date_minu_7 = new DateTime($cur_date);
            $cur_date_minu_7 = $cur_date_minu_7->modify('-100 Days')->format('Y-m-d');
            $cur_date_minu_7 = (array)$cur_date_minu_7;
            $cur_date_minu_7 = $cur_date_minu_7[0];

            $transactions = $this->db->select("Count(*) as total, date(date) as date, sum(amount) as amount")->from("transaction_history")->where("owner='$this->userIdGlobal' and date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();
            $orders = $this->db->select("Count(*) as total, date(date) as date, sum(order_amount) as amount")->from("orders")->where("owner='$this->userIdGlobal' and date between '$cur_date_minu_7' and '$cur_date'")->order_by("date desc,  total desc")->group_by("date")->get()->result_array();


            $responseObject['orders'] = $orders;

            $responseObject['transactions'] = $transactions;
            $responseObject['date_range'] = $cur_date_minu_7 . " and " . $cur_date;
        } elseif ($type == "payments") {
            $ReportHeading = "Payments Report";
        }

        $data['submit_link'] = $this->link . "Reports/" . $type;
        $data['reportInformation'] = $responseObject;
        $data['reportType'] = strtolower($type);
        $data['reportHeading'] = $ReportHeading;

        $this->loadView("report_index", $data);
    }

    //momo payments
//    public function ProcessMTNMOMOPayments($payment_id, $action = null)
//    {
//        $this->checkLogin();
//        $momo = new MtnMomoHelper();
//
//        $paymentRecord = $this->db->select("pc.id,pc.amount, net_amount, date(pc.date) as date, pc.paid, pc.balance,pc.reference_number, pc.order_amount,  pc.last_transaction_date, pc.reason")
//            ->from("pangisa_collections pc")
//            ->where(["id" => $payment_id, 'owner' => $this->userIdGlobal])
//            ->get()
//            ->row();
//
//
//        if (!$paymentRecord) {
//            echo json_encode($this->>$this->ca_h->$this->getGenericResponse(404, "Payment Record not found"));
//
//        } elseif ($action == null && $paymentRecord) {
//            echo json_encode($this->>$this->ca_h->$this->getGenericResponse(0, "success", $paymentRecord));
//
//        } elseif ($action == "verifypaymentdetails") {
//            $mode_of_payment = $this->input->post("mode_of_payment");
//            $phone = $this->input->post("phone");
//
//            $validatePhone = $this->validatePhoneNumber($phone);
//            if ($validatePhone->getResultCode() <> 0) {
//                echo json_encode($validatePhone);
//            } else {
//                $validateAccountHolder = $momo->VerifyAccountHolder($phone);
//                echo json_encode($validateAccountHolder);
//            }
//
//        } elseif ($action == "triggerpayment") {
//            $payment_channel = $this->db->where(['user' => "MTN_MOMO"])->get("payment_chanels")->row();
//
//            $phone = $this->input->post("phone");
//            $amount = $this->input->post("amount");
//
//            $validatePhone = $this->validatePhoneNumber($phone);
//
//            if ($validatePhone->getResultCode() <> 0) {
//                echo json_encode($validatePhone);
//            } else {
//
//                $transactionId = strtoupper(uniqid("PNV" . $this->userIdGlobal));
//                $client = $this->db->where(['id' => $this->userIdGlobal])->get("users")->row();
//
//                $paymentHistory = array();
//                $paymentHistory['payment_channel'] = $payment_channel->id;
//                $paymentHistory['transactionid'] = $transactionId;
//                $paymentHistory['channel_name'] = $payment_channel->chanel_name;
//                $paymentHistory['min_amount'] = $paymentRecord->balance > (0.5 * $paymentRecord->amount) ? 0.25 * $paymentRecord->balance : 0.5 * $paymentRecord->balance;;
//                $paymentHistory['amountpayable'] = $paymentRecord->amount;
//                $paymentHistory['client'] = $this->userIdGlobal;
//                $paymentHistory['client_name'] = $client->name;
//                $paymentHistory['item'] = null;
//                $paymentHistory['item_reg_number'] = $paymentRecord->reference_number;
//                $paymentHistory['owner'] = null;
//                $paymentHistory['owner_name'] = "Pangisa Applications Limited";
//                $paymentHistory['order_number'] = $paymentRecord->reference_number;
//                $paymentHistory['status'] = 0;
//                $paymentHistory['narration'] = "Payment for $paymentRecord->reference_number off $payment_channel->chanel_name";
//                $paymentHistory['customer_number'] = $phone;
//
//                $this->db->insert("transaction_history", $paymentHistory);
//                $transactionInserId = $this->db->insert_id();
//
//                $paymentResult = $momo->RequestToPay($phone, $amount, "Payment for " . $paymentRecord->reference_number);
//                $resultCode = $paymentResult->getResultCode();
//                $resultMessage = $paymentResult->getMessage();
//
//                $this->db->where(["id" => $transactionInserId])
//                    ->set(["momo_request_status" => $resultCode, "momo_request_response" => $resultMessage])
//                    ->update("transaction_history");
//
//                echo json_encode($this->>$this->ca_h->$this->getGenericResponse(($resultCode == 202 ? 0 : $resultCode), $resultMessage));
//            }
//        }
//    }

    //process rave payment
    public function ProcessRavePayments($payment_id, $action = null)
    {
        $this->checkLogin();
//        $momo = new MtnMomoHelper();

        $paymentRecord = $this->db->select("pc.id,pc.amount, net_amount, date(pc.date) as date, pc.paid, pc.balance,pc.reference_number, pc.order_amount,  pc.last_transaction_date, pc.reason")
            ->from("pangisa_collections pc")
            ->where(["id" => $payment_id, 'owner' => $this->userIdGlobal])
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

            $transactionId = strtoupper(uniqid("PN-RV-" . $this->userIdGlobal));
            $client = $this->db->where(['id' => $this->userIdGlobal])->get("users")->row();

            $paymentHistory = array();
            $paymentHistory['payment_channel'] = $payment_channel->id;
            $paymentHistory['transactionid'] = $transactionId;
            $paymentHistory['channel_name'] = $payment_channel->chanel_name;
            $paymentHistory['min_amount'] = $paymentRecord->balance > (0.5 * $paymentRecord->amount) ? 0.25 * $paymentRecord->balance : 0.5 * $paymentRecord->balance;;
            $paymentHistory['amountpayable'] = $paymentRecord->amount;
            $paymentHistory['client'] = $this->userIdGlobal;
            $paymentHistory['client_name'] = $client->name;
            $paymentHistory['item'] = null;
            $paymentHistory['item_reg_number'] = $paymentRecord->reference_number;
            $paymentHistory['owner'] = null;
            $paymentHistory['owner_name'] = "Pangisa Applications Limited";
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
            return $this->ca_h->getGenericResponse(100, "Phone number must be 10 digits or 12 digits");
        }

        $first3 = substr($phone, 0, 3);
        $first1 = substr($phone, 0, 1);

        $first1Array = ['07', '25'];
        $mtnnumbers = ["077", "078"];
        $first2 = substr($phone, 0, 2);

        if (!in_array($first2, $first1Array)) {
            return $this->ca_h->getGenericResponse(100, "Phone number must start with 07 or 256 " . $first1);
        }

        if ($first3 == "256" && !in_array(substr(2, 3), $mtnnumbers)) {
            return $this->ca_h->getGenericResponse(100, "Phone number must be MTN number ");
        }

        $phone = $first1 == 0 ? "256" . substr(1, 9) : $phone;
        return $this->ca_h->getGenericResponse(0, "valid", $phone);
    }

    private function saveClientOrderApprovalNotificationEmail($orderId)
    {
        $order = $this->db->where('id', $orderId)->get('orders')->row();
        $item = $this->db->where('id', $order->item)->get('items')->row();
        $client = $this->db->where('id', $order->client)->get('users')->row();
        $vendor = $this->db->where('id', $order->owner)->get('users')->row();

        $clientNotification = "
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #2196F3; color: #fff; font-weight: 700'>
                    Dear " . $client->name . ", Your Order for " . $item->name . " has been Approved
                </section>
                <br>
                <table cellpadding='3' cellspacing='0'>
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
                    <tr>
                        <td>Approval Date</td>
                        <td>" . $order->approval_date . "</td>
                    </tr>
                    <tr>
                        <td>Approval Remarks</td>
                        <td>" . $order->comment . "</td>
                    </tr>
                </table>
                                               
                <br>
                <p>To pay for this order, follow the steps below </p>
                <p><b>Mobile Money (Airtel Or MTN) </b></p>
                <ul>
                    <li>Login into the portal and go to payments.</li>
                    <li>Click on the pay button on the right most column </li>
                    <li>Choose a preferred mode of payment from the pop up menu </li>
                    <li>Fill in your phone number</li>
                    <li>click Proceed</li>
                    <li>You will receive a pop up asking you to authorise on the payment on the phone number supplied</li>
                </ul>
                
                <p><b>Centenary Bank Agents </b></p>
                <ul>
                    <li>Visit any Cente Agent of Choice.</li>
                    <li>Ask them to pay for an item using Sasula </li>
                    <li>Provide " . $order->order_number . " as your payment reference number</li>
                    <li>Confirm the Payment Narration that is displayed on their POS devices</li>
                    <li>If Correct, Proceed to pay</li>
                </ul>
                
                <hr/>
            
                You can view the status of your order by clicking the following link <a href='" . base_url() . "Customer/Orders/view/" . $order->id . "'>
                " . base_url() . "Customer/Orders/view/" . $order->id . "</a>                 
            
            </div>
        ";

        echo $clientNotification;

        //save for client
        $clientEmailNotification = [];
        $clientEmailNotification['client'] = $client->id;
        $clientEmailNotification['owner'] = $vendor->id;
        $clientEmailNotification['action'] = 'PLACED_ORDER';
        $clientEmailNotification['body'] = $clientNotification;
        $clientEmailNotification['email_address'] = $client->email;
        $clientEmailNotification['status'] = 0;
        $clientEmailNotification['subject'] = $order->order_number . " has been Approved";
        $this->db->insert("email_outbox", $clientEmailNotification);
    }

    private function saveClientOrderRejectNotificationEmail($orderId)
    {
        $order = $this->db->where('id', $orderId)->get('orders')->row();
        $item = $this->db->where('id', $order->item)->get('items')->row();
        $client = $this->db->where('id', $order->client)->get('users')->row();
        $vendor = $this->db->where('id', $order->owner)->get('users')->row();

        $clientNotification = "
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #2196F3; color: #fff; font-weight: 700'>
                    Dear " . $client->name . ", Your Order for " . $item->name . " has been Reject
                </section>
                <br>
                <table cellpadding='3' cellspacing='0'>
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
                    <tr>
                        <td>Rejected Date</td>
                        <td>" . $order->approval_date . "</td>
                    </tr>
                    <tr>
                        <td>Approval Remarks</td>
                        <td>" . $order->comment . "</td>
                    </tr>
                </table>
                                               
                <p><b>You can visit the portal to place another order</b></p>
                
                <hr/>
            
                You can view the status of your order by clicking the following link <a href='" . base_url() . "Customer/Orders/view/" . $order->id . "'>
                " . base_url() . "Customer/Orders/view/" . $order->id . "</a>                 
            
            </div>
        ";

        echo $clientNotification;

        //save for client
        $clientEmailNotification = [];
        $clientEmailNotification['client'] = $client->id;
        $clientEmailNotification['owner'] = $vendor->id;
        $clientEmailNotification['action'] = 'PLACED_ORDER';
        $clientEmailNotification['body'] = $clientNotification;
        $clientEmailNotification['email_address'] = $client->email;
        $clientEmailNotification['status'] = 0;
        $clientEmailNotification['subject'] = $order->order_number . " has been Rejected";
        $this->db->insert("email_outbox", $clientEmailNotification);
    }
}
