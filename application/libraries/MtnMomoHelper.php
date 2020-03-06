<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 4/22/2019
 * Time: 8:01 PM
 */

//include APPPATH . "/controllers/Response.php";

class MtnMomoHelper
{
    protected $CI;

    private $uniqid = null;
    private $requestToPayUrl = "https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay";
    private $requestToPayUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/v1_0/requesttopay";

    //api user
    private $CreateApiUserUrl = "https://ericssonbasicapi2.azure-api.net/v1_0/apiuser";
    private $CreateApiUserUrlProduction = "https://ericssonbasicapi1.azure-api.net/v1_0/apiuser";

    //api key
    private $CreateAPiKeyUrl = "https://ericssonbasicapi2.azure-api.net/v1_0/apiuser/67251c89-0e98-4843-b45f-60ec3caae7e6/apikey";
    private $CreateAPiKeyUrlProduction = "https://ericssonbasicapi1.azure-api.net/v1_0/apiuser/67251c89-0e98-4843-b45f-60ec3caae7e6/apikey";

    private $accesstokenUrl = "https://ericssonbasicapi2.azure-api.net/collection/token/";
    private $accesstokenUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/token/";

    //to handle disbursements
    private $disbursementTokenUrlProduction = "https://ericssonbasicapi1.azure-api.net/disbursement/token/";
    private $disbursementUrlProduction = "https://ericssonbasicapi1.azure-api.net/disbursement/v1_0/transfer";
    private $disbursementApiKeyProduction = "a4e4d462fb3b422dafb9f5b6aeb0a286";
    private $disbursementApiUserProduction = "0f7e42ca-04e9-4d20-a85b-0ddfc0d48aab";

    //get the transaction status
    private $getTransactionstatusProductionUrl = "https://ericssonbasicapi1.azure-api.net/collection/v1_0/requesttopay/";


    private $getaccountbalanceurlgetUrl = "https://ericssonbasicapi2.azure-api.net/collection/v1_0/account/balance";
    private $getaccountbalanceurlgetUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/v1_0/account/balance";

    private $OcpApimSubscriptionKey = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyProduction = 'e7ac1adae673408a9aa38fe9d92be9df';

    private $OcpApimSubscriptionKeyCollections = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyCollectionsProduction = '5f46ed89029d4eab94e163d10cab17cc';

    private $OcpApimSubscriptionKeyDisbursements = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyDisbursementsProduction = 'e7ac1adae673408a9aa38fe9d92be9df';

    private $accessToken = null;
    private $apiKey = "afc62804a31e4c148c2068a6dd36cc6a";

    //production
    private $apiKeyCollectionProduction = "1053a74512cd473f93975f984e73c217";
    private $apiKeyDisbursementsProduction = "afc62804a31e4c148c2068a6dd36cc6a";

    private $apiuserProduction = "2773d68e-001e-4739-ac28-e9d0b344e93c";
    private $apiuser = "2773d68e-001e-4739-ac28-e9d0b344e93c";

    //use these only in production
    private $collections_user_name = 'PANGI2.sp5';
    private $apiuserForCollectionProduction = '43bb6534-72d5-4fc4-9658-7143eab6d60c';
    private $apiuserForDisbursementsProduction = 'PANGI3.sp5';
    private $apiuserForLiquidationProduction = 'Pangisa';

    private $is_live_service = true;
    private $target_environment = 'mtnuganda';
    private $currency = 'EUR';

    //production details collections
    //Primary key aaa985675d5247bdb8ce12e9487b27ce
    //Secondary key 0b1516f90ff844eba05055c684dcacb2

    //deposits/disbursements
    //Primary key e7ac1adae673408a9aa38fe9d92be9df
    //Secondary key 4eb90ee7deee489dab273696523055e9

    //Balllistics helper
    private $helper = null;


    //collections -- same as debit subscriber account request to pay
    //Primary key 5f46ed89029d4eab94e163d10cab17cc
    //Secondary key 819d898335ca4a9e967be24141a90193

    //sptransfer -- move money from your account to another -- disbursements
    //Primary key53abb869144a4dc2a5d64385984bcc8d
    //Secondary key138a26c106fa461194f802fad37cc0e5

    //remittt money from out to local
    //Primary key92023f1900d24b69a5fec40720b06e04
    //Secondary keyaf656c70f1f541e5952fa15a0fdc7301

    public function __construct()
    {
        date_default_timezone_set("Africa/Kampala");
        $this->uniqid = $this->guidv4();
        $this->CI =& get_instance();
        $this->CI->load->library("session", "upload", "pagination", "user_agent", "database");

        $this->target_environment = $this->is_live_service ? 'mtnuganda' : 'sandbox';

        //$this->ManageAccessToken();
        if ($this->target_environment == 'mtnuganda') {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyProduction;
            $this->currency = 'UGX';
            $this->apiKey = $this->apiKeyCollectionProduction;
            $this->apiuser = $this->apiuserProduction;
            $this->requestToPayUrl = $this->requestToPayUrlProduction;
            $this->CreateAPiKeyUrl = $this->CreateAPiKeyUrlProduction;
            $this->CreateApiUserUrl = $this->CreateApiUserUrlProduction;
            $this->accesstokenUrl = $this->accesstokenUrlProduction;
            $this->getaccountbalanceurlgetUrl = $this->getaccountbalanceurlgetUrlProduction;
        }
    }

    public function Index()
    {
        echo json_encode(new Response(0, 0, 0));
    }

    public function getTransactionStatus($referenceId)
    {
        $getTransactionStatusUrl = $this->getTransactionstatusProductionUrl . $referenceId;
        //if it is a live service, then use live key

        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyCollectionsProduction;
            $this->apiuser = $this->apiuserForCollectionProduction;
            $this->apiKey = $this->apiKeyCollectionProduction;
        }

        $access_token_record = $this->CI->db->where(['status' => 1, "type" => 'collections'])->get("mtn_momo_access_token")->row();
        $access_token = $access_token_record->access_token;

        if ($access_token == null) {
            return new Response(1000, "initial access Token found");
        }

        $requestToPayHeaders = array(
            'Authorization: Bearer ' . trim($access_token),
            'X-Reference-Id: ' . $this->uniqid,
            'Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKey,
            'X-Target-Environment: ' . $this->target_environment,
            'Content-Type: application/json',
        );

        $helper = new Ballistics();
        $helper->log_payment_to_system($this->ip_address . "|RESPONSE|verify-payment-mtn|REFID" . $referenceId . "|", $this->called_link, $this->unique_trace_id);
        return $this->sendGetRequest($getTransactionStatusUrl, null, $requestToPayHeaders);
    }
    //this method is called first while working with this integration  -- only used in test bed

    /**
     * for production no need to creat the api key adn the api user, these can be picked off the sales portal
     * * @return Response
     */
    public function CreateApiUser()
    {
        $ApiUserHeader = array(
            'Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKey,
            'X-Reference-Id : ' . $this->uniqid
        );

        $response = $this->sendPostRequest($this->CreateApiUserUrl, null, $ApiUserHeader);
        return $response;
    }

    //this is called second, after creating the api user -- only used in test bed

    /**
     * no need to create this for production, these can be picked off the sales portal
     * @return Response
     */
    public function CreateApiKey()
    {
        $ApiUSerBody = array("providerCallbackHost" => "https://pangisa.co.ug/");
        $ApiKeyHeader = array('Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKeyCollectionsProduction);
        $CreateAPiKeyUrlProduction = "https://ericssonbasicapi1.azure-api.net/v1_0/apiuser/" . $this->apiuserForCollectionProduction . "/apikey";
        return $this->sendPostRequest($CreateAPiKeyUrlProduction, $ApiUSerBody, $ApiKeyHeader);
    }

    //balance
    public function GetAccountHolderBalance()
    {

        $etBalanceHeaders = array(
            'Authorization: Bearer ' . $this->accessToken,
            'X-Reference-Id: ' . $this->uniqid,
            'Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKey,
            'X-Target-Environment: sandbox',
        );

        return $this->sendPostRequest($this->getaccountbalanceurlgetUrl, null, $etBalanceHeaders);
    }

    //account holder
    public function VerifyAccountHolder($number)
    {
        $url = "https://ericssonbasicapi2.azure-api.net/collection/v1_0/accountholder/msisdn/$number/active";

        $headers = array(
            'X-Target-Environment : sandbox',
            'Ocp-Apim-Subscription-Key : ' . $this->OcpApimSubscriptionKey,
        );

        return $this->sendGetRequest($url, null, $headers);
    }

    public function RequestToPay($number, $amount, $narration, $transaction_number = null, $transaction_insert_id)
    {
        $access_token_record = $this->CI->db->where(['status' => 1, "type" => 'collections'])->get("mtn_momo_access_token")->row();
        $access_token = $access_token_record->access_token;

        if ($access_token == null) {
            return new Response(1000, "initial access Token found");
        }

        //if it is a live service, then use live key
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyCollectionsProduction;
            $this->apiuser = $this->apiuserForCollectionProduction;
            $this->apiKey = $this->apiKeyCollectionProduction;
            $this->requestToPayUrl = $this->requestToPayUrlProduction;
        }

        $x_reference_id = $this->uniqid;

        $requestToPayHeaders = array(
            'Authorization: Bearer ' . trim($access_token),
            'X-Reference-Id: ' . $x_reference_id,
            'Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKey,
            'X-Target-Environment: ' . $this->target_environment,
            'Content-Type: application/json',
        );
        $requestToPayPayerDetails = array(
            "partyIdType" => "MSISDN",
            "partyId" => $number
        );
        $requestToPayBody = array(
            "amount" => $amount,
            "currency" => $this->currency,
            "externalId" => $transaction_number ?: self::guidv4(),
            "payer" => $requestToPayPayerDetails,
            "payerMessage" => $narration,
            "payeeNote" => $narration,
        );

        //update the database with the reference id
        $this->CI->db->where(['id' => $transaction_insert_id])->set(['momo_x_reference_id' => $x_reference_id])->update('transaction_history');

        //sent the http request
        $response = $this->sendPostRequest($this->requestToPayUrl, $requestToPayBody, $requestToPayHeaders);

        //log the request
        $helper = new Ballistics();
        $helper->log_payment_to_system("Request-to-pay-momo|XREF|" . $x_reference_id . "|PHONE|" . $number . "|TXNID-PANGISA|" . $transaction_number . "|BODY|" . json_encode($requestToPayBody) . "|RESPONSE|" . json_encode($response) . "\n\n");

        return $response;
    }

    public function DepositToUserAccount($number, $amount, $narration, $transaction_number = null, $transaction_insert_id)
    {
//        $this->ManageAccessToken("disbursements");
        $access_token_record = $this->CI->db->where(['status' => 1, "type" => 'disbursements'])->get("mtn_momo_access_token")->row();
        $access_token = $access_token_record ? $access_token_record->access_token : null;

        if ($access_token == null) {
            return new Response(1000, "initial access Token found");
        }

        //if it is a live service, then use live key
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyDisbursementsProduction;
            $this->apiuser = $this->disbursementApiUserProduction;
            $this->apiKey = $this->disbursementApiKeyProduction;
            $this->accesstokenUrl = $this->disbursementUrlProduction;
        }

        $x_reference_id = $this->uniqid;

        $requestToPayHeaders = array(
            'Authorization: Bearer ' . trim($access_token),
            'X-Reference-Id: ' . $x_reference_id,
            'Ocp-Apim-Subscription-Key: ' . $this->OcpApimSubscriptionKey,
            'X-Target-Environment: ' . $this->target_environment,
            'Content-Type: application/json',
        );
        $requestToPayPayerDetails = array(
            "partyIdType" => "MSISDN",
            "partyId" => $number
        );
        $requestToPayBody = array(
            "amount" => $amount,
            "currency" => $this->currency,
            "externalId" => $transaction_number ?: self::guidv4(),
            "payee" => $requestToPayPayerDetails,
            "payerMessage" => $narration,
            "payeeNote" => $narration,
        );

        //update the database with the reference id
        $this->CI->db->where(['id' => $transaction_insert_id])->set(['momo_x_reference_id' => $x_reference_id])->update('transaction_history');

        //sent the http request
        $response = $this->sendPostRequest($this->disbursementUrlProduction, $requestToPayBody, $requestToPayHeaders);

        //log the request
        $helper = new Ballistics();
        $helper->log_payment_to_system("disbursement-momo|XREF|" . $x_reference_id . "|PHONE|" . $number . "|TXNID-PANGISA|" . $transaction_number . "|BODY|" . json_encode($requestToPayBody) . "|RESPONSE|" . json_encode($response) . "\n\n");

        return $response;
    }

    //manage the validity of the access token
    public function ManageAccessToken($type = 'collections')
    {
        $access_token_record = $this->CI->db->where(['status' => 1, "type" => $type])->get("mtn_momo_access_token")->row();
        $access_token = $access_token_record ? $access_token_record->access_token : null;

        if ($access_token == null) {
            if ($type == "collections") {
                $this->getInitialAccessToken();
            } elseif ($type = "disbursements") {
                $this->getDisbursementInitialAccessToken();
            }
        } else {
            $timegap = $this->dateDiffTimeStamp(date('Y-m-d H:i:s'), $access_token_record->expire_time);

            if ($timegap > 0) {
                if ($type == "collections") {
                    $this->UpdateAccessToken();
                } elseif ($type = "disbursements") {
                    $this->UpdateDisbursementAccessToken();
                }
            } else {
                $this->accessToken = $access_token;
            }
        }
    }

    private function dateDiffTimeStamp($d1, $d2)
    {
        $date = new DateTime($d1);
        $date2 = new DateTime($d2);
        $diff = $date->getTimestamp() - $date2->getTimestamp();
        return $diff;
    }

    //update existing accesstoken for collections
    private function UpdateAccessToken()
    {
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyCollectionsProduction;
            $this->apiuser = $this->apiuserForCollectionProduction;
            $this->apiKey = $this->apiKeyCollectionProduction;
            $this->accesstokenUrl = $this->accesstokenUrlProduction;
        }

        $accessTokenHeaders = array(
            'Authorization:Basic ' . base64_encode($this->apiuser . ":" . $this->apiKey),
            'Ocp-Apim-Subscription-Key:' . $this->OcpApimSubscriptionKey,
        );

        $result = $this->sendPostRequest($this->accesstokenUrl, [], $accessTokenHeaders);

        if ($result->getResultCode() == 200) {
            $tokeData = $result->getData();
            $currentDate = date('Y-m-d H:i:s');

            if ($tokeData && $tokeData->access_token <> null) {
                $date = new DateTime($currentDate);
                $date->add(new DateInterval('PT0H3600S'));
                $datePlusTwoHours = $date->format('Y-m-d H:i:s');
                $tokenObject = array(
                    "access_token" => $tokeData->access_token,
                    "create_time" => $currentDate,
                    "expire_time" => $datePlusTwoHours,
                    "status" => 1,
                );
                $this->CI->db->set($tokenObject)->update("mtn_momo_access_token");
                $this->accessToken = $tokeData->access_token;

            } else {
                exit(json_encode(new Response(1000, 'update access token not found in response')));
            }
        } else {
            echo json_encode(new Response(1000, $result->getMessage()));
        }

    }

    //update existing accesstoken for disbursements
    private function UpdateDisbursementAccessToken()
    {
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyDisbursementsProduction;
            $this->apiuser = $this->disbursementApiUserProduction;
            $this->apiKey = $this->disbursementApiKeyProduction;
            $this->accesstokenUrl = $this->disbursementTokenUrlProduction;
        }

        $accessTokenHeaders = array(
            'Authorization:Basic ' . base64_encode($this->apiuser . ":" . $this->apiKey),
            'Ocp-Apim-Subscription-Key:' . $this->OcpApimSubscriptionKey,
        );

        $result = $this->sendPostRequest($this->accesstokenUrl, [], $accessTokenHeaders);

        if ($result->getResultCode() == 200) {
            $tokeData = $result->getData();
            $currentDate = date('Y-m-d H:i:s');

            if ($tokeData && $tokeData->access_token <> null) {
                $date = new DateTime($currentDate);
                $date->add(new DateInterval('PT0H3600S'));
                $datePlusTwoHours = $date->format('Y-m-d H:i:s');
                $tokenObject = array(
                    "access_token" => $tokeData->access_token,
                    "create_time" => $currentDate,
                    "expire_time" => $datePlusTwoHours,
                    "status" => 1,
                );
                $this->CI->db->where(["type" => "disbursements"])->set($tokenObject)->update("mtn_momo_access_token");
                $this->accessToken = $tokeData->access_token;

            } else {
                exit(json_encode(new Response(1000, 'update access token not found in response')));
            }
        } else {
            echo json_encode(new Response(1000, $result->getMessage()));
        }

    }

    //get initial access token for Collections
    public function getInitialAccessToken()
    {
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyCollectionsProduction;
            $this->apiuser = $this->apiuserForCollectionProduction;
            $this->apiKey = $this->apiKeyCollectionProduction;
            $this->accesstokenUrl = $this->accesstokenUrlProduction;
        }

        $accessTokenHeaders = array(
            'Authorization:Basic ' . base64_encode($this->apiuser . ":" . $this->apiKey),
            'Ocp-Apim-Subscription-Key:' . $this->OcpApimSubscriptionKey,
        );

        $result = $this->sendPostRequest($this->accesstokenUrl, null, $accessTokenHeaders);
        if ($result->getResultCode() == 200) {
            $tokeData = $result->getData();
            $currentDate = date('Y-m-d H:i:s');

            $date = new DateTime($currentDate);
            $date->add(new DateInterval('PT0H3600S'));
            $datePlusTwoHours = $date->format('Y-m-d H:i:s');

            if ($tokeData && $tokeData->access_token <> null) {
                $tokenObject = array(
                    "access_token" => $tokeData->access_token,
                    "create_time" => $currentDate,
                    "expire_time" => $datePlusTwoHours,
                    "status" => 1,
                );
                $this->CI->db->insert('mtn_momo_access_token', $tokenObject);
                $this->accessToken = $tokeData->access_token;
            } else {
                exit(json_encode(new Response(1000, 'initial access token not found in response')));
            }

        } else {
            echo json_encode(new Response(1000, $result->getMessage()));
        }

    }

    //get initial access token for disbursements
    public function getDisbursementInitialAccessToken()
    {
        if ($this->is_live_service) {
            $this->OcpApimSubscriptionKey = $this->OcpApimSubscriptionKeyDisbursementsProduction;
            $this->apiuser = $this->disbursementApiUserProduction;
            $this->apiKey = $this->disbursementApiKeyProduction;
            $this->accesstokenUrl = $this->disbursementTokenUrlProduction;
        }

        $accessTokenHeaders = array(
            'Authorization:Basic ' . base64_encode($this->apiuser . ":" . $this->apiKey),
            'Ocp-Apim-Subscription-Key:' . $this->OcpApimSubscriptionKey,
        );

        $result = $this->sendPostRequest($this->accesstokenUrl, null, $accessTokenHeaders);


        if ($result->getResultCode() == 200) {
            $tokeData = $result->getData();
            $currentDate = date('Y-m-d H:i:s');

            $date = new DateTime($currentDate);
            $date->add(new DateInterval('PT0H3600S'));
            $datePlusTwoHours = $date->format('Y-m-d H:i:s');

            if ($tokeData && $tokeData->access_token <> null) {
                $tokenObject = array(
                    "access_token" => $tokeData->access_token,
                    "create_time" => $currentDate,
                    "expire_time" => $datePlusTwoHours,
                    "status" => 1,
                    "type" => "disbursements",
                );
                $this->CI->db->insert('mtn_momo_access_token', $tokenObject);
                $this->accessToken = $tokeData->access_token;
            } else {
                exit(json_encode(new Response(1000, 'initial access token not found in response')));
            }

        } else {
            echo json_encode(new Response(1000, $result->getMessage()));
        }

    }

    /**
     * @param $url
     * @param $data
     * @param $headers
     * @return Response
     */
    private function sendPostRequest($url, $data, $headers)
    {
        $curl = curl_init();
        array_push($headers, 'cache-control: no-cache');
        array_push($headers, 'Connection: keep-alive');
        array_push($headers, 'Content-Length: ' . strlen(json_encode($data)));
        array_push($headers, 'Accept-Encoding: gzip, deflate');
        array_push($headers, 'Host: ericssonbasicapi1.azure-api.net');
        array_push($headers, 'User-Agent: PostmanRuntime/7.19.0');
        array_push($headers, 'Accept:*/*');

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_MAXREDIRS => 20,
//            CURLOPT_TIMEOUT_MS => 9000,
//            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data <> null ? json_encode($data) : json_encode([]),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
//        echo json_encode($data)."\n\n";
//        echo json_encode($response);

        $helper = new Ballistics();
        $helper->log_to_system(json_encode($data) . "\n" . json_encode($response));

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $http_connect_time = curl_getinfo($curl, CURLINFO_CONNECT_TIME);

        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        $headers = explode("\r\n", $headers);
        $headers = array_filter($headers);

        ///need to take care of this error in case of network connection failure
        $splitHttpHeaderResponse = count($headers) > 0 ? explode(" ", $headers[0], 20) : [];
        $httpResponseHeader = new stdClass();
        $httpResponseHeader->code = $http_response_code;
        $httpResponseHeader->remark = '';

        for ($i = 2; $i < count($splitHttpHeaderResponse); $i++) {
            $httpResponseHeader->remark .= $splitHttpHeaderResponse[$i] . " ";
        }

        if ($err) {
            return new Response(1000, $err);
        } else {
            $code = $httpResponseHeader->code;
            $msg = $httpResponseHeader->remark;

            return new Response($code, $msg, $body ? json_decode($body) : null);
        }
    }

    /**
     * @param $url
     * @param array $data
     * @param array $headers
     * @return Response
     */
    function sendGetRequest($url, $data = [], $headers = [])
    {
        $helper = new Ballistics();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_TIMEOUT_MS => 9000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers ?: [],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $http_connect_time = curl_getinfo($curl, CURLINFO_CONNECT_TIME);

        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        $headers = explode("\r\n", $headers);
        $headers = array_filter($headers);

        ///need to take care of this error in case of network connection failure
        $splitHttpHeaderResponse = count($headers) > 0 ? explode(" ", $headers[0], 20) : [];
        $httpResponseHeader = new stdClass();
        $httpResponseHeader->code = $http_response_code;
        $httpResponseHeader->remark = '';

        $helper->log_to_system("TIME|" . $http_connect_time . "|URL|" . $url . "|HEADERS|" . json_encode($body) . "|BODY|" . json_encode($body));


        for ($i = 2; $i < count($splitHttpHeaderResponse); $i++) {
            $httpResponseHeader->remark .= $splitHttpHeaderResponse[$i] . " ";
        }

        if ($err) {
            return new Response(http_response_code(), $err);
        } else {
            $code = $httpResponseHeader->code;
            $msg = $httpResponseHeader->remark;

            return new Response($code, $msg, ($body ?: []));
        }
    }

    private function guidv4()
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
