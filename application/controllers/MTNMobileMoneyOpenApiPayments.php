<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 3/9/2019
 * Time: 10:59 AM
 */

require "Response.php";
class MTNMobileMoneyOpenApiPayments extends CI_Controller {

    private $uniqid=null;
    private $requestToPayUrl="https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay";
    private $CreateApiUserUrl="https://ericssonbasicapi2.azure-api.net/v1_0/apiuser";
    private $CreateAPiKeyUrl="https://ericssonbasicapi2.azure-api.net/v1_0/apiuser/67251c89-0e98-4843-b45f-60ec3caae7e6/apikey";
    private $accesstokenUrl="https://ericssonbasicapi2.azure-api.net/collection/token/";
    private $getaccountbalanceurlgetUrl="https://ericssonbasicapi2.azure-api.net/collection/v1_0/account/balance";
    private $verifyaccountholderUrl="https://ericssonbasicapi2.azure-api.net/collection/v1_0/accountholder/misidn/{number}/active";
    private $OcpApimSubscriptionKey='7a142fad542646edb7276845c18e1494';
    private $accessToken=null;
    private $apiKey="afc62804a31e4c148c2068a6dd36cc6a";
    private $apiuser="2773d68e-001e-4739-ac28-e9d0b344e93c";


    //mtnmomoapi widgets
    //Primary key 23979477d28643cbb26d5471d1b98161
    //Secondary key 254d6d7c04aa4a52bbdff8eda6be3a69

    //collections -- same as debit subscriber account request to pay
    //Primary key 7a142fad542646edb7276845c18e1494
    //Secondary key819d898335ca4a9e967be24141a90193

    //sptransfer -- move money from your account to another -- disbursements
    //Primary key53abb869144a4dc2a5d64385984bcc8d
    //Secondary key138a26c106fa461194f802fad37cc0e5

    //remittt money from out to local
    //Primary key92023f1900d24b69a5fec40720b06e04
    //Secondary keyaf656c70f1f541e5952fa15a0fdc7301

    public function __construct(){
        parent::__construct();
        $this->uniqid=$this->guidv4();

        //keep the access_token ALive for momo payment
        $this->ManageAccessToken();
    }

    public function Index(){
        echo json_encode(new Response(0,0,0));
    }

    public function getTransactionStatus($referenceId){
        $getTransactionStatusUrl="https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay/".$referenceId;
        $requestToPayHeaders = array(
            'Authorization: Bearer '.$this->accessToken,
            'X-Reference-Id: '.$this->uniqid,
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey,
            'X-Target-Environment: sandbox',
        );

        $response=$this->sendGetRequest($getTransactionStatusUrl,null,$requestToPayHeaders);
        return $response;

    }

    public function CreateApiKey(){
        $CreateAPiKeyUrl="https://ericssonbasicapi2.azure-api.net/v1_0/apiuser/".$this->apiuser."/apikey";
        $ApiUSerBody=array("providerCallbackHost"=>"www.pangisa.co.ug");
        $ApiKeyHeader=array('Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey);
        $result=$this->sendPostRequest($CreateAPiKeyUrl,$ApiUSerBody,$ApiKeyHeader);
        return $result;
    }

    public function CreateApiUser(){

        $ApiUserHeader=array(
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey,
            'X-Reference-Id : '.$this->uniqid
        );

        $ApiUserData=array("providerCallbackHost"=> "pangisa.co.ug");

        $response=$this->sendPostRequest($this->CreateApiUserUrl,$ApiUserData,$ApiUserHeader);
        return $response;
    }

    public function CreateAccessToken(){}

    //balance
    public function GetAccountHolderBalance(){

        $etBalanceHeaders = array(
            'Authorization: Bearer '.$this->accessToken,
            'X-Reference-Id: '.$this->uniqid,
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey,
            'X-Target-Environment: sandbox',
        );

        $result=$this->sendPostRequest($this->getaccountbalanceurlgetUrl,null,$etBalanceHeaders);

        return $result;
    }

    //account holder
    public function VerifyAccountHolder($number){
        $url="https://ericssonbasicapi2.azure-api.net/collection/v1_0/accountholder/msisdn/$number/active";

        $headers = array(
            'X-Target-Environment : sandbox',
            'Ocp-Apim-Subscription-Key : '.$this->OcpApimSubscriptionKey,
        );

        $result=$this->sendGetRequest($url,null,$headers);
        return $result;
    }

    public function RequestToPay($number,$amount, $narration){

        $requestToPayHeaders = array(
            'Authorization: Bearer '.$this->accessToken,
            'X-Reference-Id: '.$this->uniqid,
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey,
            'X-Target-Environment: sandbox',
        );

        $requestToPayPayerDetails=array(
            "partyIdType"=>"MSISDN",
            "partyId"=> $number
        );

        $requestToPayBody=array(
            "amount"=> $amount,
            "currency"=>"EUR",
            "externalId"=>"123456789UG",
            "payer"=>$requestToPayPayerDetails,
            "payerMessage"=> "Pangisa : ".$narration,
            "payeeNote"=> "Pangisa : ".$narration,
        );

        $result=$this->sendPostRequest($this->requestToPayUrl,$requestToPayBody,$requestToPayHeaders);

        return $result;
    }

    public function DepositToUserAccount(){}

    private function ManageAccessToken(){

        $access_token_record=$this->db->get("mtn_momo_access_token")->row();
        $access_token=$access_token_record?$access_token_record->access_token:null;

        if($access_token==null){
            $this->getInitialAccessToken();
        }else{
            $timegap=$this->dateDiffTimeStamp($access_token_record->create_time, $access_token_record->expire_time);

            if($timegap<500){
                $this->UpdateAccessToken();
            }else{
                $this->accessToken=$access_token_record->access_token;
            }
        }
    }

    private function dateDiffTimeStamp($d1,$d2){
        $date = new DateTime( $d1 );
        $date2 = new DateTime( $d2 );
        $diff = $date2->getTimestamp() - $date->getTimestamp();
        return $diff;
    }

    private  function UpdateAccessToken(){
        $accessTokenHeaders=array(
            'Authorization: Basic '.base64_encode($this->apiuser.":".$this->apiKey),
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey
        );

        $result = $this->sendPostRequest($this->accesstokenUrl,null,$accessTokenHeaders);

        if($result->getResultCode()==0){
            $tokeData=$result->getData();
            $currentDate=date('Y-m-d h:i:s');

            $date = new DateTime($currentDate);
            $date->add(new DateInterval('PT0H3600S'));
            $datePlusTwoHours=$date->format('Y-m-d H:i:s');

            $tokenObject=array(
                "access_token"=>$tokeData->access_token,
                "create_time"=>$currentDate,
                "expire_time"=>$datePlusTwoHours,
                "status"=>1,
            );

            $this->db->set($tokenObject)->update("mtn_momo_access_token");
        }

    }

    private  function getInitialAccessToken(){
        $accessTokenHeaders=array(
            'Authorization: Basic '.base64_encode($this->apiuser.":".$this->apiKey),
            'Ocp-Apim-Subscription-Key: '.$this->OcpApimSubscriptionKey
        );

        $result = $this->sendPostRequest($this->accesstokenUrl,null,$accessTokenHeaders);

        if($result->getResultCode()==0){
            $tokeData=$result->getData();
            $currentDate=date('Y-m-d h:i:s');

            $date = new DateTime($currentDate);
            $date->add(new DateInterval('PT0H3600S'));
            $datePlusTwoHours=$date->format('Y-m-d H:i:s');

            $tokenObject=array(
                "access_token"=>$tokeData->access_token,
                "create_time"=>$currentDate,
                "expire_time"=>$datePlusTwoHours,
                "status"=>1,
            );

            $this->db->insert('mtn_momo_access_token',$tokenObject);
        }else{
            echo $result->getMessage();
        }

    }

    private function sendPostRequest($url,$data,$headers){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER=>true,
            CURLOPT_CUSTOMREQUEST=> "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER=> $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        $headers = explode("\r\n", $headers);
        $headers = array_filter($headers);
        $splitHttpHeaderResponse=explode(" ",$headers[0],20);

        $httpResponseHeader=new stdClass();
        $httpResponseHeader->code=$splitHttpHeaderResponse[1];
        $httpResponseHeader->remark=$splitHttpHeaderResponse[2];

        echo json_encode($httpResponseHeader); return;

        if ($err) {
            return new Response(1000,$err);
        } else {
//            $respData= json_decode($body);
            $code=0;
            $msg=null;

            if($httpResponseHeader->code<>0 ||$httpResponseHeader->code<>200 || $httpResponseHeader->code<>202 ){
                $code=$httpResponseHeader->code;
                $msg=$httpResponseHeader->remark;
            }
            return new Response($code,$msg,$body);
        }

    }

    function sendGetRequest($url,$data=[],$headers=[]){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER=>false,
            CURLOPT_CUSTOMREQUEST=> "GET",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER=> $headers?:[],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return new Response(100,$err);
        } else {
            $respData= json_decode($response);
            return new Response(0,"successful",$respData );
        }
    }

    private function guidv4(){
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}