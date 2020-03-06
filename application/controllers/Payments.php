<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 12/17/2018
 * Time: 10:43 AM
 */

require "Response.php";
require "constants.php";
require "Ballistics.php";
require "FlutterWavePayments.php";
require APPPATH . "libraries/MtnMomoHelper.php";

class Payments extends CI_Controller
{
    private $queryPaymentParamasNumber = 3;
    private $postPaymentParamasNumber = 3;
    private $called_link = null;
    private $unique_trace_id = null;
    private $ip_address = null;
    private $orderNumber = null;
    private $user_name = null;
    private $helper = null;
    private $post_information = null;

    public function __construct()
    {
        parent::__construct();

        $this->called_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; //$this->uri->uri_string();
        $this->unique_trace_id = uniqid("LM");
        $this->ip_address = $this->getUserIpAddr();

        $_SESSION['called_url'] = $this->called_link;
        $_SESSION['unique_trace_id'] = $this->unique_trace_id;

        $post_information = file_get_contents("php://input");
        $post_information = json_decode($post_information, 1);

        //class level access to post information
        $this->post_information = $post_information;

        $orderNumber = isset($post_information['orderNumber']) ? $post_information['orderNumber'] : null;
        $user_name = isset($post_information['username']) ? $post_information['username'] : null;

        $this->user_name = $user_name;
        $this->orderNumber = $orderNumber;

        $this->helper = new Ballistics();
//        set_error_handler(array(&$this->helper, "my_error_handler"));
        $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $orderNumber . "|" . json_encode($post_information), $this->called_link, $this->unique_trace_id);
    }

    public function Index()
    {
        echo 'Pangisa';
    }

    function getUserIpAddr()
    {
        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_HOST'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function Sasula($action)
    {
        try {
            $post_information = file_get_contents("php://input");

            //check type of method used
            if ($this->input->server('REQUEST_METHOD') <> 'POST'):
                $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "|Method not allowed");
                echo json_encode(new Response(NOT_ALLOWED_METHOD, NOT_ALLOWED_METHOD . " HTTP -> " . $this->input->server('REQUEST_METHOD') . " method not allowed. use post"));
                return;
            endif;

            $post_information = json_decode($post_information, 1);

            //check the payment channel exists
            $payment_channel = $this->db->where(["user" => $post_information['username'], "password" => sha1(base64_decode($post_information['password']))])->get("payment_chanels")->row();

            if (!$payment_channel) {
                $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| Un known source. on second trial, you will be blocked");
                echo json_encode(new Response(UNAUTHORISED, UNAUTHORISED . " Un known source. on second trial, you will be blocked", null));
                return;
            }

            if ($action == "queryPayment") {
                $this->db->trans_start();
                $response = new Response();
                $orderNumber = $post_information['orderNumber'];
                $orderDetails = $this->db->where("order_number", $orderNumber)->get("orders")->row();

                if (!$orderDetails) {
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| Order not found");
                    echo json_encode(new Response(ORDER_NUMBER_NOT_FOUND, ORDER_NUMBER_NOT_FOUND . " - Order number not found", null));
                    return;
                }

                $client = $this->db->where("id", $orderDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $orderDetails->owner)->get("users")->row();
                $item = $this->db->where("id", $orderDetails->item)->get("items")->row();
                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();

                if (!$client):
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| Client no longer exists");
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Client no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$item):
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| Items was deleted");
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Item no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$owner):
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| Item owner no longer exists");
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Owner no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$balance):
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $this->orderNumber . "|" . $this->orderNumber, $this->called_link, $this->unique_trace_id . "| No balance");
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " No Balance record found"));
                    return;
                endif;

                if ($balance->balance == 0 || $balance->balance < 1):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Payment for this order was cleared."));
                    return;
                endif;


                if ($orderDetails->approved == 0) {
                    $response->setData(null);
                    $response->setMessage("Dear $client->name Your Order has not yet been approved yet, Please be patient");
                    $response->setResultCode(ORDER_NOT_APPROVED);
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $response->getMessage() . "|", $this->called_link, $this->unique_trace_id);
                    echo json_encode($response);
                    return;
                } elseif ($orderDetails->approved == 2) {
                    $response->setData(null);
                    $response->setMessage("Dear $client->name Your Order was rejected. Reason : " . $orderDetails->comment);
                    $response->setResultCode(501);
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . $response->getMessage() . "|", $this->called_link, $this->unique_trace_id);
                    echo json_encode($response);
                    return;
                }

                //generate the transaction id
                $transactionid = strtoupper(uniqid("PAN" . $client->id));

                $orderResponse['orderNumber'] = $orderNumber;
                $orderResponse['amountPayable'] = $balance->balance;
                $orderResponse['minAmount'] = $balance->balance > (0.5 * $balance->amount) ? 0.25 * $balance->balance : 0.5 * $balance->balance;
                $orderResponse['customerName'] = $client->name;
                $orderResponse['itemName'] = $item->name;
                $orderResponse['itemRegistrationNumber'] = $item->identification_number;
                $orderResponse['transactionId'] = $transactionid;
                $orderResponse['narration'] = "Dear $client->name, Please Complete Payment for $item->name Order Number $orderNumber. Amount Payable " . number_format($balance->balance);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                echo json_encode(new Response(SUCCESS, SUCCESS . " Order Query was success", $orderResponse));
                return;

            }

            //receive payment from sasula
            if ($action == "postSasulaPayment") {

                $orderNumber = $post_information['orderNumber'];
                $this->db->trans_start();

                $orderDetails = @$this->db->where("order_number", $orderNumber)->get("orders")->row();

                if (!$orderDetails) {
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . " Order not found" . "|", $this->called_link, $this->unique_trace_id);
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Order with specified order number not found"));
                    return;
                }

                $client = $this->db->where("id", $orderDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $orderDetails->owner)->get("users")->row();
                $item = $this->db->where("id", $orderDetails->item)->get("items")->row();
                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();

                //normal check params
                if (!$client):
                    $this->logsasulaactivity($this->ip_address . "|REQUEST|" . " Client no longer exists, please contact administrator for more information" . "|", $this->called_link, $this->unique_trace_id);
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Client no longer exists, please contact administrator for more information"));
                    return;
                endif;

                if (!$item):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Item no longer exists, please contact administrator for more information"));
                    return;
                endif;

                if (!$owner):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Owner no longer exists, please contact administrator for more information"));
                    return;
                endif;

                if (!$balance):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " No Balance record found"));
                    return;
                endif;

                if ($balance->balance == 0 || $balance->balance < 1):
                    echo json_encode(new Response(ORDER_CLEARED, "Payment for this order was cleared."));
                    return;
                endif;


                $transactionid = strtoupper(uniqid("P" . $client->id));

                $paymentHistoryUpDateObject = array();
                $paymentHistoryUpDateObject['status'] = 1;

                if ($post_information['transactionStatus'] <> 0) {
                    $paymentHistoryUpDateObject['status'] = 2; //2 failed, 1 success
                }

                $paymentHistoryUpDateObject['payment_channel'] = $payment_channel->id;
                $paymentHistoryUpDateObject['transactionid'] = $transactionid;
                $paymentHistoryUpDateObject['channel_name'] = $payment_channel->chanel_name;
                $paymentHistoryUpDateObject['min_amount'] = $balance->balance > (0.5 * $balance->amount) ? 0.25 * $balance->balance : 0.5 * $balance->balance;
                $paymentHistoryUpDateObject['amountpayable'] = $balance->balance;
                $paymentHistoryUpDateObject['client'] = $client->id;
                $paymentHistoryUpDateObject['client_name'] = $client->name;
                $paymentHistoryUpDateObject['item'] = $orderDetails->item;
                $paymentHistoryUpDateObject['item_reg_number'] = $item->identification_number;
                $paymentHistoryUpDateObject['owner'] = $orderDetails->owner;
                $paymentHistoryUpDateObject['owner_name'] = $owner->name;
                $paymentHistoryUpDateObject['order_number'] = $orderNumber;
                $paymentHistoryUpDateObject['status'] = 1;
                $paymentHistoryUpDateObject['narration'] = "Payment for $orderNumber off $payment_channel->chanel_name";
                $paymentHistoryUpDateObject['customer_number'] = $client->phone;
                $paymentHistoryUpDateObject['amount'] = $post_information['transactionStatus'] == 0 ? $post_information['amount'] : null;
                $paymentHistoryUpDateObject['payment_date'] = date("Y-m-d h:i:s");
                $paymentHistoryUpDateObject['payee_number'] = $post_information['payerNumber'];
                $paymentHistoryUpDateObject['payee_name'] = $post_information['payerName'];
                $paymentHistoryUpDateObject['ext_transaction_id'] = $post_information['extTransactionId'];
                $paymentHistoryUpDateObject['external_response_message'] = $post_information['extTransactionMessage'];

                //update the transaction history
                $this->db->insert("transaction_history", $paymentHistoryUpDateObject);

                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();
                $new_balance = $balance->balance - $post_information['amount'];
                $total_paid = $balance->total_paid + $post_information['amount'];

                //update balance for user
                $updateTotalPaidBalance = array(
                    "balance" => $new_balance,
                    "total_paid" => $total_paid
                );

                //update the balances
                $this->db->set($updateTotalPaidBalance)->where("id", $balance->id)->update("payment_balances");

                //save message to que for client
                $item_client = $this->db->where("id", $balance->client)->get("users")->row();
                $message = array(
                    "message" => "Dear $item_client->name , Your Payment of UGX " . number_format($post_information['amount']) . " has been received for  Order Number " . $orderNumber . " Transaction id " . $transactionid . ". ",
                    "client" => $this->session->user['id'],
                    "status" => 0,
                    "messageid" => null,
                    "phone_number" => $item_client->phone,
                );
                //inset into que for client
                $this->db->insert("message_outbox", $message);

                //get the reference for payment
                $payment_reference = $this->db->where(['transactionId' => $transactionid])->limit(1)->get("transaction_history")->row();
                $payment_record = new paymentRecord();
                $payment_record->order_number = $orderNumber;
                $payment_record->amount = $post_information['amount'];
                $payment_record->ext_transaction_id = $post_information['extTransactionId'];
                $payment_record->mode_of_transaction = "SASULA";
                $payment_record->pangisa_transaction_id = $transactionid;
                $payment_record->payee_name = @$post_information['payerName'] ?: null;
                $payment_record->payment_reference_number = $payment_reference->id;
                $payment_record->source_destination_bank_account = null;
                $payment_record->source_destination_momo_number = $post_information['payerNumber'];
                $payment_record->payments_trasaction_id = $post_information['extTransactionId'];
                $payment_record->client_id = $client->id;
                $payment_record->item_id = $item->id;

                $this->manageVendorAccountBalances($owner->id, $payment_record);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(new Response(DATABASE_ERROR, DATABASE_ERROR . " A database error occurred."));
                    return;
                } else {
                    $this->db->trans_commit();
                    echo json_encode(new Response(SUCCESS, SUCCESS . " successfully processed transaction.", ['transaction_id' => $transactionid]));
                    return;
                }
            }

            echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND, " Unknown Operation, please refer to your api guide"));
            return;

        } catch (Exception $exception) {
            echo new Response(INTERNAL_SERVER_ERROR, INTERNAL_SERVER_ERROR . " Internal Server error");
            return;
        }
    }

    //this will used to handle only flutter wave response callbacks
    public function FlutterWave($action)
    {

        $post_information = file_get_contents("php://input");
        $post_information = json_decode($post_information, 1);
        $this->logsasulaactivity($this->ip_address . "|REQUEST|" . json_encode($post_information) . "|", $this->called_link, $this->unique_trace_id);

        $action ?: "receive_payment";
        $post_information = $this->post_information ?: null;

        try {
            if (empty($post_information) || $post_information == null):
                echo json_encode(new Response(501, " Bad Request, no post data found"));

            elseif ($action == 'receive_payment'):
                $fl_w_tx_status = $post_information['status'];
                $fl_w_tx_message = $post_information['message'];

                $fl_w_tx = $post_information['data'];
                $referenceNumber = $fl_w_tx['txref'];
                $paymentHistoryDetails = $this->db->where("transactionid", $referenceNumber)->limit(1)->get("transaction_history")->row();

                //avoid over payment due to web hook retries
                if (($paymentHistoryDetails->status == 1) || ($paymentHistoryDetails->ext_transaction_id == $fl_w_tx['flwref'])) {
                    echo json_encode(new Response(1000, " Transaction Already Completed or Duplicate transaction Id"));
                    exit();

                } elseif ($fl_w_tx_status == 'success' && $fl_w_tx['status'] == 'successful') {
                    $this->db->trans_start();

                    $client = $this->db->where("id", $paymentHistoryDetails->client)->get("users")->row();
                    $owner = $this->db->where("id", $paymentHistoryDetails->owner)->get("users")->row();
                    $balance = $this->db->where(["client" => $client->id, "order_number" => $paymentHistoryDetails->order_number])->get("payment_balances")->row();

                    $paymentHistoryUpDateObject = array();
                    $paymentHistoryUpDateObject['status'] = 1; //2 failed, 1 success
                    $paymentHistoryUpDateObject['payment_date'] = date("Y-m-d h:i:s");
                    $paymentHistoryUpDateObject['amount'] = $fl_w_tx['chargedamount'];
                    $paymentHistoryUpDateObject['payee_number'] = $fl_w_tx['custphone'];
                    $paymentHistoryUpDateObject['payee_name'] = $fl_w_tx['custname'];
                    $paymentHistoryUpDateObject['ext_transaction_id'] = $fl_w_tx['flwref'];
                    $paymentHistoryUpDateObject['external_response_message'] = $fl_w_tx['acctmessage'];
                    $paymentHistoryUpDateObject['rave_app_charge'] = $fl_w_tx['appfee'];
                    $paymentHistoryUpDateObject['rave_pay_type'] = $fl_w_tx['paymenttype'];
                    $paymentHistoryUpDateObject['rave_payment_response_object'] = json_encode($post_information);

                    //update the transaction history
                    $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");

                    $new_balance = $balance->balance - $fl_w_tx['chargedamount'];
                    $total_paid = $balance->total_paid + $fl_w_tx['chargedamount'];

                    $updateTotalPaidBalance = array(
                        "balance" => $new_balance,
                        "total_paid" => $total_paid
                    );

                    //update the balances
                    $this->db->set($updateTotalPaidBalance)->where("id", $balance->id)->update("payment_balances");

                    //save message to que for client
                    $message = array(
                        "message" => "Dear $client->name , Your Payment of UGX " . number_format($fl_w_tx['chargedamount']) . " has been received for  Order Number " . $paymentHistoryDetails->reference_number . " Transaction id " . $referenceNumber . ". ",
                        "client" => $this->session->user['id'],
                        "status" => 0,
                        "messageid" => null,
                        "phone_number" => $client->phone,
                    );

                    //inset into que for client
                    $this->db->insert("message_outbox", $message);

                    //send client an email
                    $order_id_for_sending_notification = null;
                    if (!$paymentHistoryDetails->order_id) {
                        $order_id_for_sending_notification = $this->db->where(['order_number' => $paymentHistoryDetails->order_number])->get('orders')->row()->id;
                    } else {
                        $order_id_for_sending_notification = $paymentHistoryDetails->order_id;
                    }

                    $this->saveClientPaymentNotificationEmail($order_id_for_sending_notification, $fl_w_tx['chargedamount']);

                    $payment_record = new paymentRecord();
                    $payment_record->order_number = $paymentHistoryDetails->order_number;
                    $payment_record->amount = $fl_w_tx['chargedamount'];
                    $payment_record->ext_transaction_id = $referenceNumber;
                    $payment_record->mode_of_transaction = "Flutter Wave";
                    $payment_record->pangisa_transaction_id = $referenceNumber;
                    $payment_record->payee_name = $fl_w_tx['custname'];
                    $payment_record->payment_reference_number = $paymentHistoryDetails->id;
                    $payment_record->source_destination_bank_account = null;
                    $payment_record->source_destination_momo_number = $fl_w_tx['custphone'];
                    $payment_record->payments_trasaction_id = $paymentHistoryDetails->id;
                    $payment_record->client_id = $client->id;
                    $payment_record->payment_channel = "SASULA";

                    $this->manageVendorAccountBalances($owner->id, $payment_record);
                    echo json_encode(new Response(SUCCESS, SUCCESS . " Transaction was successful.", null));
                } else {
                    $paymentHistoryUpDateObject['status'] = 2;
                    $paymentHistoryUpDateObject['external_response_message'] = 'Transaction failed -' . $fl_w_tx['vbvmessage'];
                    $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");
                    echo json_encode(new Response(1000, " Transaction was un successful.", null));
                }


                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(new Response(DATABASE_ERROR, DATABASE_ERROR . " A database error occurred."));
                    return;
                } else {
                    $this->db->trans_commit();
                    return;
                }

                echo json_encode(new Response(INFORMATION_NOT_FOUND, " Unknown Operation, please refer to your api guide"));
                return;
            else:
                echo json_encode(new Response(INFORMATION_NOT_FOUND, $action . " is Unknown, please refer to your api guide"));
            endif;

        } catch (Exception $exception) {
            echo new Response(INTERNAL_SERVER_ERROR, INTERNAL_SERVER_ERROR . " Internal Server error");
            return;
        }

    }

    public function verifyFlutterWaveTransaction()
    {
        $pending_transactions = $this->db->where(['payment_channel' => 2, 'status' => 0])->get("transaction_history")->result_array();
        $fl_w = new FlutterWavePayments();

        foreach ($pending_transactions as $transaction) {
            $transactionRefNumber = $transaction['transactionid'];
            $response = $fl_w->VerifyRavePayment($transactionRefNumber);
            echo json_encode($response);
            $this->updateVerifiedTransaction($transactionRefNumber, $response->getData());
        }
    }

    public function checkMTNPendingTransactions()
    {
        $pending_transactions = $this->db->where(['payment_channel' => 0, 'status' => 0, 'http_request_response' => 'Accepted'])->order_by("id", "desc")->limit(4)->get("transaction_history")->result_array();
        $momo = new MtnMomoHelper();

        foreach ($pending_transactions as $transaction) {
            $transactionRefNumber = $transaction['momo_x_reference_id'];
            $response = $momo->getTransactionStatus($transactionRefNumber);
            $this->updateVerifiedMTNTransaction($transactionRefNumber, $response->getData(), $response->getResultCode());
        }
    }

    public function checkMTNPendingTransactionsCopy()
    {
        $r = file_get_contents("php://input");
        var_dump($r);

        $r = json_decode($r, 1);

        var_dump($r);
        $pending_transactions = $this->db->where(['momo_x_reference_id' => '9b290121-365b-4e10-8db9-177d1b5d321c', 'payment_channel' => 0, 'http_request_response' => 'Accepted'])->order_by("id", "desc")->limit(1)->get("transaction_history")->result_array();

        foreach ($pending_transactions as $transaction) {
            $transactionRefNumber = $transaction['momo_x_reference_id'];
            $response = new Response(0, 'success', (json_encode($r)));
            var_dump($response);
            $this->updateVerifiedMTNTransaction($transactionRefNumber, $response->getData(), $response->getResultCode());
        }

    }

    private function saveClientPaymentNotificationEmail($orderId, $transactionAmount)
    {
        $order = $this->db->where(['id' => $orderId])->get('orders')->row();
        $item = $this->db->where(['id' => $order->item])->get('items')->row();
        $client = $this->db->where('id', $order->client)->get('users')->row();
        $vendor = $this->db->where('id', $order->owner)->get('users')->row();
        $paymentBalance = $this->db->where(['order_number' => $order->order_number, 'client' => $client->id])->get('payment_balances')->row();

        $pickupItemNotice = null;
        if ($paymentBalance->balance == 0) {
            $pickupItemNotice = "<p>Payment for the above order has been completed, You will receive a phone call from one of our administrators who will link you to the vendor to pick your item. For any more information, please respond to this email</p>";
        }

        $clientNotification = "
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #607D8B; color: #ffffff; font-weight: 700'>
                    Dear " . $client->name . ", Your Payment for " . $item->name . " has been Received
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
                        <td>" . number_format($order->item_price_at_order) . "</td>
                    </tr>
                    <tr>
                        <td>Total Amount </td>
                        <td>" . number_format($order->order_amount) . "</td>
                    </tr>
                    <tr>
                        <td>Amount Paid </td>
                        <td>UGX " . number_format($paymentBalance->total_paid) . "</td>
                    </tr>
                    <tr>
                        <td>Balance </td>
                        <td> UGX " . number_format($paymentBalance->balance) . "</td>
                    </tr>
                    <tr>
                        <td>Last Transaction Date</td>
                        <td>" . $paymentBalance->last_transaction_date . "</td>
                    </tr>
                    
                    <tr>
                        <td>Last Transaction Amount</td>
                        <td>" . number_format($transactionAmount) . "</td>
                    </tr>
                    
                </table>
                      
                      <br>
                      " . $pickupItemNotice . "                        
                         
                <hr/>
            
                You can view the balances by clicking the following link <a href='" . base_url() . "Customer/payments_balances'>
                " . base_url() . "Customer/Orders/payments_balances/</a>                 
            
            </div>
        ";

        //save for client
        $clientEmailNotification = [];
        $clientEmailNotification['client'] = $client->id;
        $clientEmailNotification['owner'] = $vendor->id;
        $clientEmailNotification['action'] = 'PAYMENT RECEIVED';
        $clientEmailNotification['body'] = $clientNotification;
        $clientEmailNotification['email_address'] = $client->email;
        $clientEmailNotification['status'] = 0;
        $clientEmailNotification['subject'] = 'Payment of ' . $transactionAmount . ' for ' . $order->order_number . " has been Received";
        $this->db->insert("email_outbox", $clientEmailNotification);

        $vendorNotification = "
            <style>
                table td td{border-bottom: 1px solid #ccc;}
            </style>
            <div style='padding:0px; font-family: Calibri;'>
                <section style='padding:15px; background: #607D8B; color: #fff; font-weight: 700'>
                    Dear " . $vendor->name . ", You have received a payment for " . $item->name . " has been Received
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
                        <td>" . number_format($order->item_price_at_order) . "</td>
                    </tr>
                    <tr>
                        <td>Total Amount </td>
                        <td>" . number_format($order->order_amount - $order->markup_at_order) . "</td>
                    </tr>
                    <tr>
                        <td>Amount Paid </td>
                        <td>UGX " . number_format($paymentBalance->balance == 0 ? ($transactionAmount - $paymentBalance->order_markup) : $transactionAmount) . "</td>
                    </tr>
                    <tr>
                        <td>Balance </td>
                        <td> UGX " . number_format($paymentBalance->balance) . "</td>
                    </tr>                     
                </table>                            
            </div>
        ";
        $vendorEmailNotification = [];
        $vendorEmailNotification['client'] = $client->id;
        $vendorEmailNotification['owner'] = $vendor->id;
        $vendorEmailNotification['action'] = 'PAYMENT RECEIVED';
        $vendorEmailNotification['body'] = $vendorNotification;
        $vendorEmailNotification['email_address'] = $vendor->email;
        $vendorEmailNotification['status'] = 0;
        $vendorEmailNotification['subject'] = 'You have received a payment of ' . $transactionAmount . ' for ' . $order->order_number;

        $this->db->insert("email_outbox", $clientEmailNotification);
    }

    private function updateVerifiedTransaction($txid, $requestBody)
    {
        try {
            $this->logsasulaactivity($this->ip_address . "|REQUEST|verify-payment'" . json_encode($requestBody) . "|", $this->called_link, $this->unique_trace_id);
            $fl_w_tx_status = $requestBody['status'];
            $fl_w_tx_message = $requestBody['message'];

            $fl_w_tx = $requestBody['data'];
            $referenceNumber = $fl_w_tx['txref'];
            $paymentHistoryDetails = $this->db->where(["transactionid" => $txid])->limit(1)->get("transaction_history")->row();

            if ($fl_w_tx_status == 'success' && $fl_w_tx['status'] == 'successful') {
                $this->db->trans_start();

                $client = $this->db->where("id", $paymentHistoryDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $paymentHistoryDetails->owner)->get("users")->row();
                $balance = $this->db->where(["client" => $client->id, "order_number" => $paymentHistoryDetails->order_number])->get("payment_balances")->row();

                $paymentHistoryUpDateObject = array();
                $paymentHistoryUpDateObject['status'] = 1; //2 failed, 1 success
                $paymentHistoryUpDateObject['payment_date'] = date("Y-m-d h:i:s");
                $paymentHistoryUpDateObject['amount'] = $fl_w_tx['chargedamount'];
                $paymentHistoryUpDateObject['payee_number'] = $fl_w_tx['custphone'];
                $paymentHistoryUpDateObject['payee_name'] = $fl_w_tx['custname'];
                $paymentHistoryUpDateObject['ext_transaction_id'] = $fl_w_tx['flwref'];
                $paymentHistoryUpDateObject['external_response_message'] = $fl_w_tx['acctmessage'];
                $paymentHistoryUpDateObject['rave_app_charge'] = $fl_w_tx['appfee'];
                $paymentHistoryUpDateObject['rave_pay_type'] = $fl_w_tx['paymenttype'];
                $paymentHistoryUpDateObject['rave_payment_response_object'] = json_encode($requestBody);

                //update the transaction history
                $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");

                $new_balance = $balance->balance - $fl_w_tx['chargedamount'];
                $total_paid = $balance->total_paid + $fl_w_tx['chargedamount'];

                $updateTotalPaidBalance = array(
                    "balance" => $new_balance,
                    "total_paid" => $total_paid
                );

                //update the balances
                $this->db->set($updateTotalPaidBalance)->where("id", $balance->id)->update("payment_balances");

                //save message to que for client
                $message = array(
                    "message" => "Dear $client->name , Your Payment of UGX " . number_format($fl_w_tx['chargedamount']) . " has been received for  Order Number " . $paymentHistoryDetails->reference_number . " Transaction id " . $referenceNumber . ". ",
                    "client" => $this->session->user['id'],
                    "status" => 0,
                    "messageid" => null,
                    "phone_number" => $client->phone,
                );

                //inset into que for client
                $this->db->insert("message_outbox", $message);

                //send client an email
                $order_id_for_sending_notification = null;
                if (!$paymentHistoryDetails->order_id) {
                    $order_id_for_sending_notification = $this->db->where(['order_number' => $paymentHistoryDetails->order_number])->get('orders')->row()->id;
                } else {
                    $order_id_for_sending_notification = $paymentHistoryDetails->order_id;
                }

                $this->saveClientPaymentNotificationEmail($order_id_for_sending_notification, $fl_w_tx['chargedamount']);

                $payment_record = new paymentRecord();
                $payment_record->order_number = $paymentHistoryDetails->order_number;
                $payment_record->amount = $fl_w_tx['chargedamount'];
                $payment_record->ext_transaction_id = $referenceNumber;
                $payment_record->mode_of_transaction = $fl_w_tx['paymenttype'];
                $payment_record->pangisa_transaction_id = $referenceNumber;
                $payment_record->payee_name = $fl_w_tx['custname'];
                $payment_record->payment_reference_number = $paymentHistoryDetails->id;
                $payment_record->source_destination_bank_account = null;
                $payment_record->source_destination_momo_number = $fl_w_tx['custphone'];
                $payment_record->payments_trasaction_id = $paymentHistoryDetails->id;
                $payment_record->client_id = $client->id;
                $payment_record->payment_channel = "Flutter Wave";

                $this->manageVendorAccountBalances($owner->id, $payment_record);
                echo json_encode(new Response(SUCCESS, SUCCESS . " Transaction was successful.", null));
            } else {
                $paymentHistoryUpDateObject['status'] = 2;
                $paymentHistoryUpDateObject['external_response_message'] = 'Transaction failed -' . $fl_w_tx['vbvmessage'];
                $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");
                echo json_encode(new Response(SUCCESS, SUCCESS . " Transaction was un successful.", null));
            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(new Response(DATABASE_ERROR, DATABASE_ERROR . " A database error occurred."));
                return;
            } else {
                $this->db->trans_commit();
                return;
            }
        } catch (Exception $exception) {
            echo json_encode(new Response(INTERNAL_SERVER_ERROR, INTERNAL_SERVER_ERROR . " Internal Server error"));
            return;
        }
    }

    //mtn momo api veirified transaction
    private function updateVerifiedMTNTransaction($txid, $requestBody, $http_code)
    {
        $requestBody = (array)json_decode($requestBody);

        try {
            //transaction history record
            $paymentHistoryDetails = $this->db->where(["momo_x_reference_id" => $txid])->limit(1)->get("transaction_history")->row();
            $this->logsasulaactivity($this->ip_address . "|RESPONSE|verify-payment-mtn|TXNID|" . $txid . "|" . json_encode($requestBody) . "|", $this->called_link, $this->unique_trace_id);

            //check if the transaction already handled
            if ($paymentHistoryDetails->status <> 0) {
                echo json_encode(new Response(1000, "This transaction is already completed.", null));
                return;
            }

            if (isset($http_code) && !in_array($http_code, [200, 202, 0])) {
                $paymentHistoryUpDateObject = [];
                $paymentHistoryUpDateObject['status'] = 2;
                $paymentHistoryUpDateObject['external_response_message'] = 'Transaction failed # ' . $requestBody['message'];
                $this->db->set($paymentHistoryUpDateObject)->where(["id" => $paymentHistoryDetails->id])->update("transaction_history");
                echo json_encode(new Response(1000, $requestBody['message'], null));
                return;
            }

            $mtn_momo_status = $requestBody['status']; //SUCCESSFUL,FAILED
            $failed_reason = null;
            $momo_transaction = (array)$requestBody;

            if ($mtn_momo_status == 'FAILED') {
                $failed_reason = $momo_transaction['reason'];
            }


            if ($mtn_momo_status == 'SUCCESSFUL') {
                $this->db->trans_start();

                $client = $this->db->where("id", $paymentHistoryDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $paymentHistoryDetails->owner)->get("users")->row();
                $balance = $this->db->where(["client" => $client->id, "order_number" => $paymentHistoryDetails->order_number])->get("payment_balances")->row();

                $new_balance = $balance->balance - $momo_transaction['amount'];
                $total_paid = $balance->total_paid + $momo_transaction['amount'];

                //update the transaction history
                $paymentHistoryUpDateObject = array();
                $paymentHistoryUpDateObject['status'] = 1; //2 failed, 1 success if balance is zero, clear bill
                $paymentHistoryUpDateObject['payment_date'] = date("Y-m-d h:i:s");
                $paymentHistoryUpDateObject['amount'] = $momo_transaction['amount'];
                $paymentHistoryUpDateObject['ext_transaction_id'] = $momo_transaction['financialTransactionId'];
                $paymentHistoryUpDateObject['rave_payment_response_object'] = json_encode($requestBody);
                $paymentHistoryUpDateObject['external_response_message'] = $mtn_momo_status;
                $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");

                $updateTotalPaidBalance = array(
                    "balance" => $new_balance,
                    "total_paid" => $total_paid
                );

                //update the balances
                $this->db->set($updateTotalPaidBalance)->where("id", $balance->id)->update("payment_balances");

                //save message to que for client
                $message = array(
                    "message" => "Dear $client->name , Your Payment of UGX " . number_format($momo_transaction['amount']) . " has been received for  Order Number " . $paymentHistoryDetails->order_number . " Transaction id " . $momo_transaction['financialTransactionId'] . ". ",
                    "client" => $this->session->user['id'],
                    "status" => 0,
                    "messageid" => null,
                    "phone_number" => $client->phone,
                );

                //inset into que for client
                $this->db->insert("message_outbox", $message);

                //send client an email
                $order_id_for_sending_notification = null;
                if (!$paymentHistoryDetails->order_id) {
                    $order_id_for_sending_notification = $this->db->where(['order_number' => $paymentHistoryDetails->order_number])->get('orders')->row()->id;
                } else {
                    $order_id_for_sending_notification = $paymentHistoryDetails->order_id;
                }

                $this->saveClientPaymentNotificationEmail($order_id_for_sending_notification, $momo_transaction['amount']);

                $payment_record = new paymentRecord();
                $payment_record->order_number = $paymentHistoryDetails->order_number;
                $payment_record->amount = $momo_transaction['amount'];
                $payment_record->ext_transaction_id = $momo_transaction['financialTransactionId'];
                $payment_record->mode_of_transaction = "momo";
                $payment_record->pangisa_transaction_id = $paymentHistoryDetails->transactionid;
                $payment_record->payee_name = null;
                $payment_record->payment_reference_number = $paymentHistoryDetails->id;
                $payment_record->source_destination_bank_account = null;
                $payment_record->source_destination_momo_number = $momo_transaction['payer']->partyId;
                $payment_record->payments_trasaction_id = $paymentHistoryDetails->id;
                $payment_record->client_id = $client->id;
                $payment_record->payment_channel = $paymentHistoryDetails->channel_name;

                $this->manageVendorAccountBalances($owner->id, $payment_record);
                echo json_encode(new Response(SUCCESS, SUCCESS . " Transaction was successful.", null));
            } elseif ($mtn_momo_status == 'FAILED') {
                $paymentHistoryUpDateObject['status'] = 2;
                $paymentHistoryUpDateObject['external_response_message'] = 'Transaction failed -' . $failed_reason->message;
                $this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryDetails->id)->update("transaction_history");
                echo json_encode(new Response(1000, 1000 . " Transaction was un successful." . $failed_reason->message, null));
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(new Response(DATABASE_ERROR, DATABASE_ERROR . " A database error occurred."));
                return;
            } else {
                $this->db->trans_commit();
                return;
            }


        } catch (Exception $exception) {
            echo json_encode(new Response(INTERNAL_SERVER_ERROR, INTERNAL_SERVER_ERROR . " Internal Server error"));
            return;
        }
    }

    private function testNullParameter($data)
    {
        $result = false;

        foreach ($data as $k => $v) {
            if (!$v): return true; endif;
        }
        return $result;

    }

    private function logsasulaactivity($info, $uri = null, $traceid = null)
    {
        $uri_segment = null;

        if ($uri) {
            $uri_segment = str_replace("/", "->", $uri);
        }
        file_put_contents(APPPATH . "logs/sasulalogs.log", date('Y-m-d H:i:s') . "|" . $uri_segment . "|" . $traceid . "|" . $info . "\n", FILE_APPEND);
    }

    private function manageVendorAccountBalances($vendor_id, $payment_object)
    {
        //query the vendor account details
        $vendor_account_gl = $this->db->where(['vendor_id' => $vendor_id])->get("vendor_account_gl")->row();

        //query for item
        $item = $this->db->where(['id' => $payment_object->item_id])->get("items")->row();

        //get the order markup and deduct if not completed
        $orderPlaced = $this->db->where(['owner' => $vendor_id, 'order_number' => $payment_object->order_number])->get("orders")->row();
        $markupToPayByVendor = 0;
        $markupPaidByVendor = 0;

        if ($orderPlaced) {
            //check if markup is cleared
            if ($orderPlaced->markup_cleared <> 1) {
                $markup = $orderPlaced->markup_at_order;

                if ($payment_object->amount >= $markup) {
                    //clear markup and continue
                    $markupPaidByVendor = $markup;
                    $this->db->where(['id' => $orderPlaced->id])->set(['markup_paid' => $markup, "markup_cleared" => 1])->update("orders");
                } else {
                    $markupPaidByVendor = ($markup - $payment_object->amount);
                    $this->db->where(['id' => $orderPlaced->id])->set(['markup_paid' => ($markup - $payment_object->amount)])->update("orders");
                }

                $markupToPayByVendor = $markup;
            }
        }

        //update the vendor account transaction history
        $vendor_transaction_history_object = array();
        $vendor_balance_after = $vendor_account_gl->balance + ($payment_object->amount - $markupPaidByVendor);

        $vendor_transaction_history_object['vendor_id'] = $vendor_id;
        $vendor_transaction_history_object['amount'] = ($payment_object->amount - $markupPaidByVendor);
        $vendor_transaction_history_object['balance_before'] = $vendor_account_gl->balance;
        $vendor_transaction_history_object['balance_after'] = $vendor_balance_after;
        $vendor_transaction_history_object['transaction_type'] = 'CREDIT';
        $vendor_transaction_history_object['mode_of_transaction'] = $payment_object->mode_of_transaction;
        $vendor_transaction_history_object['source_destination_momo_number'] = $payment_object->source_destination_momo_number ?: null;
        $vendor_transaction_history_object['source_destination_bank_account'] = $payment_object->source_destination_bank_account ?: null;
        $vendor_transaction_history_object['vendor_account'] = $vendor_account_gl->account_number;
        $vendor_transaction_history_object['payments_trasaction_id'] = $payment_object->payments_trasaction_id;
        $vendor_transaction_history_object['payment_channel'] = $payment_object->payment_channel;
        $vendor_transaction_history_object['order_number'] = $payment_object->order_number;
        $vendor_transaction_history_object['payment_reference_number'] = $payment_object->payment_reference_number;
        $vendor_transaction_history_object['ext_transaction_id'] = $payment_object->ext_transaction_id;
        $vendor_transaction_history_object['pangisa_transaction_id'] = $payment_object->pangisa_transaction_id;

        //save the transaction history
        $this->db->insert("vendor_account_gl_transaction_history", $vendor_transaction_history_object);

        //update the vendor account gl
        $vendor_account_gl_update_record = [];

        //add original amount_collected to the new amount
        $total_amount_collected = $vendor_account_gl->total_amount_collected + ($payment_object->amount - $markupPaidByVendor);

        $vendor_account_gl_update_record['total_amount_collected'] = $total_amount_collected;
        $vendor_account_gl_update_record['last_collection_date'] = date('Y-m-d h:i:s');
        $vendor_account_gl_update_record['balance'] = $vendor_account_gl->balance + ($payment_object->amount - $markupPaidByVendor);
        $this->db->where(['id' => $vendor_account_gl->id, "vendor_id" => $vendor_id])->set($vendor_account_gl_update_record)->update("vendor_account_gl");

        //save message to que for client
        $vendor = $this->db->where(['id' => $vendor_id])->limit(1)->get("users")->row();
        $message = array(
            "message" => "You have received a Payment of UGX " . number_format($payment_object->amount - $markupPaidByVendor) . "  for  Order Number " . $payment_object->order_number . " Transaction id " . $payment_object->pangisa_transaction_id . ". ",
            "client" => null,
            "status" => 0,
            "messageid" => null,
            "phone_number" => $vendor->phone,
        );

        //inset into que for vendor
        $this->db->insert("message_outbox", $message);

        //update the pangisa account_gl
        $pangisa_account_gl = $this->db->limit(1)->get("pangisa_account_gl")->row();

        //save the pangisa_account_gl_transaction history
        $pangisa_account_gl_transaction_history = [];
        $pangisa_account_gl_transaction_history['amount'] = $payment_object->amount;
        $pangisa_account_gl_transaction_history['balance_before'] = $pangisa_account_gl->balance;
        $pangisa_account_gl_transaction_history['balance_after'] = ($pangisa_account_gl->balance + $payment_object->amount);
        $pangisa_account_gl_transaction_history['transaction_type'] = 'CREDIT';
        $pangisa_account_gl_transaction_history['vendor_id'] = $vendor_id;
        $pangisa_account_gl_transaction_history['client_id'] = $payment_object->client_id;
        $pangisa_account_gl_transaction_history['payment_channel'] = $payment_object->payment_channel;
        $pangisa_account_gl_transaction_history['source_destination_bank_account'] = ($payment_object->source_destination_bank_account ?: null);
        $pangisa_account_gl_transaction_history['source_destination_momo_number'] = ($payment_object->source_destination_momo_number ?: null);
        $pangisa_account_gl_transaction_history['payee_names'] = $payment_object->payee_name;
        $pangisa_account_gl_transaction_history['order_number'] = $payment_object->order_number;
        $pangisa_account_gl_transaction_history['ext_transaction_id'] = $payment_object->ext_transaction_id;
        $pangisa_account_gl_transaction_history['pangisa_transaction_id'] = $payment_object->pangisa_transaction_id;
        $pangisa_account_gl_transaction_history['payment_reference_number'] = $payment_object->payment_reference_number;

        //save record
        $this->db->insert("pangisa_account_transaction_history", $pangisa_account_gl_transaction_history);

        $pangisa_account_gl_update_record = [];
        $pangisa_amount_collected = ($pangisa_account_gl->amount_collected + $payment_object->amount);
        $pangisa_account_gl_update_record['amount_collected'] = ($pangisa_account_gl->amount_collected + $payment_object->amount);
        $pangisa_account_gl_update_record['balance'] = $pangisa_amount_collected - $pangisa_account_gl->amount_paid_out;
        $pangisa_account_gl_update_record['last_transaction_date'] = date("Y-m-d h:i:s");
        $pangisa_account_gl_update_record['balance'] = ($pangisa_amount_collected - $pangisa_account_gl->amount_paid_out);

        //save record
        $this->db->where(['id' => $pangisa_account_gl->id])->set($pangisa_account_gl_update_record)->update("pangisa_account_gl");

    }

    public function mtn_momo_api_payment_completed()
    {
        $post_information = file_get_contents("php://input");
        $this->logsasulaactivity(json_encode($post_information), $this->unique_trace_id, $this->called_link);
    }

    public function mtn_momo_api_disbursement_completed()
    {
        $post_information = file_get_contents("php://input");
        $this->logsasulaactivity(json_encode($post_information), $this->unique_trace_id, $this->called_link);
    }
}

class paymentRecord
{
    public $amount = null;
    public $mode_of_transaction;
    public $source_destination_momo_number;
    public $source_destination_bank_account;
    public $payments_trasaction_id;
    public $payment_channel;
    public $order_number;
    public $payment_reference_number;
    public $ext_transaction_id;
    public $pangisa_transaction_id;
    public $payee_name;
    public $client_id;
    public $item_id;


    public function __construct()
    {

    }
}
