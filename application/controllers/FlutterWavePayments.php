<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 6/21/2019
 * Time: 12:54 PM
 */

class FlutterWavePayments
{
    private $rave_standard_payment_url = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay";
    private $rave_standard_verifypayment_url = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify";
    private $publickey = "FLWPUBK-e81b9f5aeb497db6d957dfa8624b6312-X";
    private $secret_key = "FLWSECK-556183ed586d486f7695e77206f96f7a-X";
    private $encryptionkey = "556183ed586d24943e3ab653";
    private $unique_trace_id = null;
    private $ip_address = null;
    private $helper = null;
    private $raveHeaders = null;

    public function __construct()
    {
        $this->unique_trace_id = uniqid("PN-RV-");
        $this->helper = new Ballistics();
        $this->ip_address = $this->helper->getUserIpAddr();

        $this->raveHeaders = [
            "content-type: application/json",
            "cache-control: no-cache"
        ];

    }

    //send the standard rave payment
    public function RequestRavePayment($data)
    {
        $ravePaymentDetails = [
            "country" => "UG",
            "custom_title" => "Pangisa Applications",
            "custom_description" => "Please Pay your outstanding balance of " . $data['amount'],
            "pay_button_text" => "Pay to Pangisa",
            "PBFPubKey" => $this->publickey,
            "currency" => "UGX",
            "redirect_url" => base_url() . "Payments/FlutterWave/receive_payment/"
        ];
        $post_data = array_merge($data, $ravePaymentDetails);
        return $this->helper->sendPostRequest($this->rave_standard_payment_url, $post_data, $this->raveHeaders);
    }

    //receive payment callback from rave
    public function ReceiveRavePayment()
    {
    }

    //verify Rave payment
    public function VerifyRavePayment($transaction_number)
    {
        $data = [
            "txref" => $transaction_number,
            "SECKEY" => $this->secret_key
        ];

        return $this->helper->sendPostRequest($this->rave_standard_verifypayment_url, $data, $this->raveHeaders);
    }
}