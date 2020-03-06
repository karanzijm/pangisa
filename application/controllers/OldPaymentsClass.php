<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 12/17/2018
 * Time: 10:43 AM
 */

require "Response.php";
//require "constants.php";
class OldPaymentsClass extends CI_Controller {
    private $queryPaymentParamasNumber=3;
    private $postPaymentParamasNumber=3;

    public function Index(){
        echo 'Pangisa';
    }

    public function Sasula($action){

        try {
            $post_information = file_get_contents("php://input");
            $log_information = "Request == " . $action . " == " .$post_information;
            $this->logsasulaactivity($log_information);

            //check type of method used
            if ($this->input->server('REQUEST_METHOD') <> 'POST'):
                echo json_encode(new Response(NOT_ALLOWED_METHOD, NOT_ALLOWED_METHOD . " HTTP -> " . $this->input->server('REQUEST_METHOD') . " method not allowed. use post"));
                return;
            endif;

            $post_information = json_decode($post_information, 1);

            $payment_channel = $this->db->where(["user" => $post_information['username'], "password" => sha1(base64_decode($post_information['password']))])->get("payment_chanels")->row();

            if (!$payment_channel) {
                echo json_encode(new Response(UNAUTHORISED, UNAUTHORISED . " Un known source. on second trial, you will be blocked", null));
                return;
            }



            if ($action == "queryPayment"){
                $this->db->trans_start();
                $response = new Response();
                $orderNumber = $post_information['orderNumber'];
                $orderDetails = $this->db->where("order_number", $orderNumber)->get("orders")->row();

                if (!$orderDetails) {
                    echo json_encode(new Response(ORDER_NUMBER_NOT_FOUND, ORDER_NUMBER_NOT_FOUND . " - Order number not found", null));
                    return;
                }

                $client = $this->db->where("id", $orderDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $orderDetails->owner)->get("users")->row();
                $item = $this->db->where("id", $orderDetails->item)->get("items")->row();
                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();

                if (!$client):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Client no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$item):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Item no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$owner):
                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Owner no longer exists, please contact 778693362 for more information"));
                    return;
                endif;

                if (!$balance):
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

                    echo json_encode($response);
                    return;
                }

                $transactionid = strtoupper(uniqid("PAN" . $client->id));

                $orderResponse['orderNumber'] = $orderNumber;
                $orderResponse['amountPayable'] = $balance->balance;
                $orderResponse['minAmount'] = $balance->balance > (0.5 * $balance->amount) ? 0.25 * $balance->balance : 0.5 * $balance->balance;
                $orderResponse['customerName'] = $client->name;
                $orderResponse['itemName'] = $item->name;
                $orderResponse['itemRegistrationNumber'] = $item->identification_number;
                $orderResponse['transactionId'] = $transactionid;
                $orderResponse['narration'] = "Dear $client->name, Please Complete Payment for $item->name Order Number $orderNumber. Amount Payable " . number_format($balance->balance);

//                $paymentHistory = array();
//                $paymentHistory['payment_channel'] = $payment_channel->id;
//                $paymentHistory['transactionid'] = $transactionid;
//                $paymentHistory['channel_name'] = $payment_channel->chanel_name;
//                $paymentHistory['min_amount'] = $orderResponse['minAmount'];
//                $paymentHistory['amountpayable'] = $balance->balance;
//                $paymentHistory['client'] = $client->id;
//                $paymentHistory['client_name'] = $client->name;
//                $paymentHistory['item'] = $orderDetails->item;
//                $paymentHistory['item_reg_number'] = $item->identification_number;
//                $paymentHistory['owner'] = $orderDetails->owner;
//                $paymentHistory['owner_name'] = $owner->name;
//                $paymentHistory['order_number'] = $orderNumber;
//                $paymentHistory['status'] = 0;
//                $paymentHistory['narration'] = "Payment for $orderNumber off $payment_channel->chanel_name";
//                $paymentHistory['customer_number'] = $client->phone;
//
//                $this->db->insert("transaction_history", $paymentHistory);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                echo json_encode(new Response(SUCCESS, SUCCESS . " Successfully processed transaction", $orderResponse));
                return;

            }

            //receive payment from sasula
            if ($action == "postSasulaPayment") {

                $orderNumber = $post_information['orderNumber'];
                $this->db->trans_start();

                $orderDetails = $this->db->where("order_number", $orderNumber)->get("orders")->row();

                $client = $this->db->where("id", $orderDetails->client)->get("users")->row();
                $owner = $this->db->where("id", $orderDetails->owner)->get("users")->row();
                $item = $this->db->where("id", $orderDetails->item)->get("items")->row();
                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();

                $transactionid = strtoupper(uniqid("PANGISA-SASULA-" . $client->id));

//                $paymentHistoryObject = $this->db->where(["transactionid" => $transactionid, "order_number" => $orderNumber])->get("transaction_history")->row();
//
//                if (!$paymentHistoryObject) {
//                    echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND . " Payment Reference not found", null));
//                    return;
//                }
//
//                if ($paymentHistoryObject->status <> 0) {
//                    echo json_encode(new Response(DUPLICATE_TRANSACTION, DUPLICATE_TRANSACTION . " Duplicate transaction Id"));
//                    return;
//                }

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
                $paymentHistoryUpDateObject['status'] = 0;
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
                //$this->db->set($paymentHistoryUpDateObject)->where("id", $paymentHistoryObject->id)->update("transaction_history");

                $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();
                $new_balance = $balance->balance - $post_information['amount'];
                $total_paid = $balance->total_paid + $post_information['amount'];

                $updateTotalPaidBalance = array(
                    "balance" => $new_balance,
                    "total_paid" => $total_paid
                );

                //update the balances
                $this->db->set($updateTotalPaidBalance)->where("id", $balance->id)->update("payment_balances");

                //save message to que for client
                $item_client=$this->db->where("id",$balance->client)->get("users")->row();
                $message = array(
                    "message" => "Dear $item_client->name , Your Payment of UGX ". number_format($post_information['amount'])." has been received for  Order Number " . $orderNumber . " Transaction id ".$transactionid.". ",
                    "client" => $this->session->user['id'],
                    "status" => 0,
                    "messageid" => null,
                    "phone_number" => $item_client->phone,
                );
                //inset into que for client
                $this->db->insert("message_outbox", $message);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(new Response(DATABASE_ERROR, DATABASE_ERROR . " A database error occurred."));
                    return;
                } else {
                    $this->db->trans_commit();
                    echo json_encode(new Response(SUCCESS, SUCCESS . " successfully processed transaction."));
                    return;
                }
            }

            echo json_encode(new Response(INFORMATION_NOT_FOUND, INFORMATION_NOT_FOUND, " Unknown Operation, please refer to your api guide"));
            return;

        }catch (Exception $exception){
            echo new Response(INTERNAL_SERVER_ERROR,INTERNAL_SERVER_ERROR." Internal Server error");
            return;
        }
    }

    private function testNullParameter($data){
        $result=false;

        foreach($data as $k=>$v){
            if(!$v): return true; endif;
        }
        return $result;

    }

    private function logsasulaactivity($info){
        file_put_contents(APPPATH."logs/sasulalogs.log", "[".date('Y-m-d H:i:s')."] - ".$info."\n", FILE_APPEND);
    }

}