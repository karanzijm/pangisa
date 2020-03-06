<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 10/7/2019
 * Time: 7:27 PM
 */
include "CentralAccess.php";

class PaymentsManagement extends CI_Controller
{
    private $mtn_numbers = ['77', '78'];
    private $airtel_numbers = ['75', '70'];
    private $africel_numbers = ['79'];
    private $utl_numbers = ['71'];
    private $_env = ['production', 'testbed'];
    private $ctrl_name = 'PaymentsManagement';
    private $error = null;
    private $link = null;
    private $title = null;
    private $form_header_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $limit = 100;
    private $helper = null;
    private $pagination_display = null;
    private $userIdGlobal = null;
    private $portal = "vendor_portal/";
    private $backLink = null;

    private $called_link = null;
    private $unique_trace_id = null;
    private $ca_h = null;

    //response object
    private $responseCode = 0;
    private $responseMessage = null;
    private $responseData = null;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Africa/Kampala");

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

        $this->helper->log_to_system(json_encode($this->input->post()));
        set_error_handler(array(&$this->helper, "my_error_handler"));
    }

    public function index()
    {
        $momo = $this->ca_h->getMtnMomoOpenApi();
        $momo->ManageAccessToken();
    }

    public function manageDepositToken()
    {
        $momo = $this->ca_h->getMtnMomoOpenApi();
    }

    /**
     * @param $payment_id
     * validate payment id and return payment details
     */
    public function getPaymentDetails($payment_id)
    {
        $this->checkLogin();

        //get the payment details
        $paymentRecord = $this->db->select("pb.id,pb.amount, pb.item, pb.client,pb.owner, 
                date(pb.date) as date, pb.total_paid as paid, pb.balance,pb.order_number as reference_number, pb.last_transaction_date, pb.reason")
            ->from("payment_balances pb")
            ->where(["pb.id" => $payment_id, 'pb.client' => $this->userIdGlobal])
            ->get()
            ->row();

        if (!$paymentRecord) {
            $this->responseCode = 404;
            $this->responseMessage = "Payment Record  with specified ID not found";

        } elseif ($paymentRecord) {
            $this->responseMessage = "Payment Details found " . json_encode($paymentRecord);
            $this->responseData = $paymentRecord;
        } else {

        }

        $this->returnResponse();
    }

    //return generic response
    public function checkLogin()
    {
        if (!$this->session->user['id']) {
            echo json_encode($this->ca_h->getGenericResponse(501, "Authentication Failed"));
            exit();
        }
    }

    /**
     * just return response based on the global response details
     */
    private function returnResponse()
    {
        echo json_encode($this->ca_h->getGenericResponse($this->responseCode, $this->responseMessage, $this->responseData));
        exit();
    }

    /**
     * @param $paymentId , start real payment based on the payment type
     */
    public function triggerPayment($paymentId)
    {
        $this->resetDefaultResponse();

        //check login
        $this->checkLogin();

        //get post data
        $post_data = $this->input->post();

        if ($post_data['mode_of_payment'] == 'CENTE_AGENT') {
            $this->responseMessage = "To use Cente Agent, Go to any agent with your order number and ask the agent to use sasula. Your order number doubles as the payment reference number";
            $this->returnResponse();
        }

        //validate the phone number
        $validate_phone_number = $this->validatePhoneNumber($post_data['phone']);

        if ($validate_phone_number === null) {
            //get phone number
            $phone_number = $this->checkPhoneNumberInternationalStandard($post_data['phone']);

            //check network
            $network = $this->checkPhoneNumberNetworkOperator($phone_number);

            if ($network == null) {
                $this->responseCode = '404';
                $this->responseMessage = "Un supported Network Operator";
            } else {

                //get the payment details
                $paymentRecord = $this->db->select("pb.id,pb.amount, pb.item, pb.client,pb.owner, pb.item,pb.order_number,
                date(pb.date) as date, pb.total_paid as paid, pb.balance,pb.order_number as reference_number, pb.last_transaction_date, pb.reason")
                    ->from("payment_balances pb")
                    ->where(["id" => $paymentId, 'client' => $this->userIdGlobal])
                    ->get()
                    ->row();

                $paymentRecord->phone = $phone_number;

                //submit request to network
                if (!$paymentRecord) {
                    $this->responseCode = 404;
                    $this->responseMessage = "Payment Record with such id $paymentId, $this->userIdGlobal not found";
                } elseif ($network == "MTN") {
                    $this->triggerMTNOpenAPiPayment($paymentRecord);
                } else {
                    $this->triggerRavePayment($paymentRecord);
                }
            }

        } else {
            $this->responseMessage = $validate_phone_number;
            $this->responseCode = 500;
        }

        $this->returnResponse();
    }

    private function resetDefaultResponse()
    {
        $this->responseMessage = null;
        $this->responseCode = 0;
        $this->responseData = null;
    }

    //check whether the phone number is valid
    private function validatePhoneNumber($phone)
    {
        $resp = null;

        if (strlen($phone) < 10 || strlen($phone) > 12) {
            $resp = "Phone number must be 10 digits or 12 digits";

        } else {
            $first1 = substr($phone, 0, 1);

            $first1Array = ['07', '25'];
            $first2 = substr($phone, 0, 2);

            if (!in_array($first2, $first1Array)) {
                $resp = "Phone number must start with 07 or 256 " . $first1;
            } else {
                $resp = null;
            }
        }

        return $resp;
    }

    public function checkPhoneNumberInternationalStandard($number)
    {
        $contains_plus = substr($number, 0, 1) == "+" ? true : false;

        if ($contains_plus) {
            $number = substr($number, 1, 12); //256778693362
        }

        $isInternationalStandard = substr($number, 0, 3) == "256" ? true : false;;
        if (!$isInternationalStandard) {
            $number = '256' . substr($number, 1, 10);
        }

        return $number;
    }

    private function checkPhoneNumberNetworkOperator($phone_number)
    {
        $first_2_character = substr($phone_number, 3, 2);
        $network_operator = null;

        if (in_array($first_2_character, $this->mtn_numbers)) {
            $network_operator = 'MTN';
        } elseif (in_array($first_2_character, $this->airtel_numbers)) {
            $network_operator = 'AIRTEL';
        }
        return $network_operator;
    }

    //handle which channel to trigger payments from
    //trigger this especially of number is airtel or payment choice is visa
    private function triggerRavePayment($paymentRecord)
    {
        $payment_channel = $this->db->where(['user' => "rave"])->get("payment_chanels")->row();

        $phone = $this->input->post("phone");
        $amount = $this->input->post("amount");

        $client = $this->db->where(['id' => $this->userIdGlobal])->get("users")->row();
        $owner = $this->db->where(['id' => $paymentRecord->owner])->get("users")->row();
        $order = $this->db->where(['order_number' => $paymentRecord->order_number])->get("orders")->row();
        $transactionId = strtoupper("PFLV" . ($client->phone . time()) . $this->userIdGlobal);

        $paymentHistory = array();
        $paymentHistory['payment_channel'] = $payment_channel->id;
        $paymentHistory['transactionid'] = $transactionId;
        $paymentHistory['channel_name'] = $payment_channel->chanel_name;
        $paymentHistory['min_amount'] = $paymentRecord->balance > (0.5 * $paymentRecord->amount) ? 0.25 * $paymentRecord->balance : 0.5 * $paymentRecord->balance;;
        $paymentHistory['amountpayable'] = $paymentRecord->amount;
        $paymentHistory['client'] = $this->userIdGlobal;
        $paymentHistory['client_name'] = $client->name;
        $paymentHistory['item'] = $paymentRecord->item;
        $paymentHistory['item_reg_number'] = $paymentRecord->reference_number;
        $paymentHistory['owner'] = $paymentRecord->owner;
        $paymentHistory['owner_name'] = $owner->name;
        $paymentHistory['order_number'] = $paymentRecord->reference_number;
        $paymentHistory['status'] = 0;
        $paymentHistory['narration'] = "Payment for $paymentRecord->reference_number off $payment_channel->chanel_name";
        $paymentHistory['customer_number'] = $phone;
        $paymentHistory['order_id'] = $order->id;

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

        $resultMessage = (200 || 0) ? $resultMessage = "You will be redirected to a new page to Complete Payment" : $resultMessage;

        return $this->ca_h->getGenericResponse(($resultCode == (202 || 200) ? 0 : $resultCode), $resultMessage, $raveRedirectLink);
    }

    //receive callbacks from momo open api
    public function PostMtnMoMoTransactionCallback($paymentID)
    {

    }

    //trigger mtn momo open api if mtn number
    private function triggerMTNOpenAPiPayment($paymentRecord)
    {
        $callback_url = $this->link . "PostMtnMoMoTransactionCallback/" . $paymentRecord->id;
        $momo = $this->ca_h->getMtnMomoOpenApi();

        $phone = $this->input->post("phone");
        $phone_number = $this->checkPhoneNumberInternationalStandard($phone);
        $amount = $this->input->post("amount");

        try {
            $transactionId = strtoupper(uniqid("PNMTNMOA" . $this->userIdGlobal));
            $payment_channel = $this->db->where(['user' => "MTN_MOMO"])->get("payment_chanels")->row();

            $orderNumber = $paymentRecord->reference_number;

            if (!$paymentRecord) {
                $this->responseCode = 404;
                $this->responseMessage = "Order details not found";
                $this->returnResponse();
                return;
            }

            $client = $this->db->where("id", $paymentRecord->client)->get("users")->row();
            $owner = $this->db->where("id", $paymentRecord->owner)->get("users")->row();
            $item = $this->db->where("id", $paymentRecord->item)->get("items")->row();
            $balance = $this->db->where("order_number", $orderNumber)->get("payment_balances")->row();
            $order = $this->db->where(['order_number' => $paymentRecord->order_number])->get("orders")->row();

            $orderResponse['orderNumber'] = $orderNumber;
            $orderResponse['amountPayable'] = $paymentRecord->balance;
            $orderResponse['minAmount'] = $paymentRecord->balance > (0.5 * $paymentRecord->amount) ? 0.25 * $paymentRecord->balance : 0.5 * $paymentRecord->balance;
            $orderResponse['customerName'] = $client->name;
            $orderResponse['itemName'] = $item->name;
            $orderResponse['itemRegistrationNumber'] = $item->identification_number;
            $orderResponse['transactionId'] = $transactionId;
            $orderResponse['narration'] = "Dear $client->name, Please Complete Payment for $item->name Order Number $orderNumber. Amount Payable " . number_format($paymentRecord->balance);

            $paymentHistory = array();
            $paymentHistory['payment_channel'] = $payment_channel->id;
            $paymentHistory['transactionid'] = $transactionId;
            $paymentHistory['channel_name'] = $payment_channel->chanel_name;
            $paymentHistory['min_amount'] = $orderResponse['minAmount'];
            $paymentHistory['amount'] = $amount;
            $paymentHistory['amountpayable'] = $balance->balance;
            $paymentHistory['client'] = $client->id;
            $paymentHistory['client_name'] = $client->name;
            $paymentHistory['item'] = $paymentRecord->item;
            $paymentHistory['item_reg_number'] = $item->identification_number;
            $paymentHistory['owner'] = $paymentRecord->owner;
            $paymentHistory['owner_name'] = $owner->name;
            $paymentHistory['order_number'] = $orderNumber;
            $paymentHistory['status'] = 0;
            $paymentHistory['narration'] = "Payment for $orderNumber off $payment_channel->chanel_name";
            $paymentHistory['customer_number'] = $client->phone;
            $paymentHistory['order_id'] = $order->id;
            $paymentHistory['payee_number'] = $phone_number;

            $this->db->insert("transaction_history", $paymentHistory);
            $transactionInsertId = $this->db->insert_id();

            $paymentResult = $momo->RequestToPay($phone_number, $amount, $paymentRecord->reason, $transactionId, $transactionInsertId);

            $resultCode = $paymentResult->getResultCode();
            $resultMessage = $paymentResult->getMessage();

            $this->db->where(["id" => $transactionInsertId])
                ->set(
                    [
                        "rave_payment_response_object" => json_encode($paymentResult->getData()),
                        "http_request_status" => $resultCode,
                        "http_request_response" => $resultMessage,
                        "http_request_response" => $resultMessage
                    ])
                ->update("transaction_history");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->responseCode = 501;
                $this->responseMessage = " An error stopped the transaction from completing";
                return;

            } else {
                $this->db->trans_commit();
            }

            $this->responseCode = $resultCode;
            $this->responseMessage = $resultMessage;
            return;

        } catch (Exception $exception) {
            $this->db->trans_rollback();
            $this->responseCode = 501;
            $this->responseMessage = "Internal Server error";
        }

    }

}
