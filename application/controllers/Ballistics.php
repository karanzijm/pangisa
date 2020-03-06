<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 11/6/2018
 * Time: 8:40 AM
 */

include APPPATH . "third_party/phpmailer/PHPMailer.php";
include APPPATH . "third_party/phpmailer/POP3.php";
include APPPATH . "third_party/phpmailer/SMTP.php";
include APPPATH . "third_party/phpmailer/Exception.php";

class Ballistics
{
    public function __construct(){
    }

    private function logMTNactivity($info,$uri=null, $traceid=null){
        $uri_segment=null;

        if($uri){$uri_segment=str_replace("/","->",$uri);}
        file_put_contents(APPPATH."logs/mtn.log", date('Y-m-d H:i:s')."|".$uri_segment."|".$traceid."|".$info."\n", FILE_APPEND);
    }

    function my_error_handler($error_no, $error_msg, $errfile, $errline)
    {
        $error=new stdClass();
        $error->error_number=$error_no;
        $error->error_message=$error_msg;
        $error->err_file=$errfile;
        $error->error_line_in_file=$errline;

        $this->log_to_system("|ERROR|".json_encode($error),$_SESSION['called_link'],$_SESSION['unique_trace_id']);

        $error_response = "
            <div style='padding: 40vh 0vw;margin:0px; background: #fff; font-family: Calibri; text-align: center; '>
                <img src='/Pangisa/resources/images/icon.png' width='100'/>
                <p><h3>We have run into an issue!.</h3>Please share the following number with support team for tracking purposes " . $_SESSION['unique_trace_id'] . "</p>
            </div>
        ";

        echo $error_response;
        exit();
    }

    public function sendMail($subject,$email, $message){
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            //Server settings

            $mail->SMTPDebug = 3;
            $mail->isSMTP();
            $mail->Host = 'pangisa.co.ug';
            $mail->SMTPSecure = 'tsl';
            $mail->Port = 25;

            //Recipients
            $mail->setFrom('info@pangisa.co.ug', 'Pangisa Uganda');
            $mail->addBCC("info@pangisa.co.ug", "Info ");

            $mail->addAddress($email);     // Add a recipient
            $mail->addReplyTo('info@pangisa.co.ug', 'Pangisa Uganda');
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $msg = "<div style='padding:10px; background: #fff; margin:0px; font-size:14px; margin:0px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif'>$message</div>";

            $msg .= "<div style='background: #fff; color: #212121; padding:10px; font-family:'Trebuchet MS'>
                    <br>
                        Regards
                        <br><br>
                        <b style='color:#255;'>Pangisa Uganda</b> <br>
                        Kanjokya House, Kamwokya, Kampala<br>
                        +256-774-669-089<br>
                    </div>";

            $mail->Body    = $msg;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            return false;
        }

    }

    public function generateHashUrl($id,$email){
        $salt="PANGISA";
        $complete_word=md5($salt.$email.":$id");
        return $complete_word;
    }

    //send sms using smsone
    public  function sendSmsSmsOne($to, $message){

        //Username : jndiho@gmail.com
        //Password : zKEn2Bhree
        //System ID: SAU16700673364
        //API ID : API16717481078
        //API Password : zKEn2Bhree

        $APID="API16717481078";
        $PASSWORD="zKEn2Bhree";
        $smstype=null;
        $phonenumber=null;
        $message=trim($message);

        $link="http://apidocs.speedamobile.com/api/SendSMS";

        $smsObject=[];
        $smsObject['api_id']= $APID;
        $smsObject["api_password"]=$PASSWORD;
        $smsObject["sms_type"]='T';
        $smsObject["encoding"]= 'U';
        $smsObject["sender_id"]="PANGISA";
        $smsObject["phonenumber"]=$to;
        $smsObject["templateid"]= null;
        $smsObject["textmessage"]=$message;
        $smsObject["V1"]= null;
        $smsObject["V2"]= null;
        $smsObject["V3"]= null;
        $smsObject["V4"]= null;
        $smsObject["V5"]= null;

        //$smsonesmsapilink="http://apidocs.speedamobile.com/api/SendSMS?api_id=$APID&api_password=$PASSWORD&sms_type=T&encoding=T&sender_id=RiftSprings&phonenumber=$phonenumber&textmessage=$message";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($smsObject),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

    public function sendSmsInfoBip($to, $text){
        $data["from"]="InfoSms";
        $data["to"]=$to;
        $data["text"]=$text;
        define("PASSWORD","dondh744");
        define("USERNAME","AGRIMIS");

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://yn239.api.infobip.com/sms/2/text/single",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic ".base64_encode(USERNAME.":".PASSWORD),
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

    public function load_params($params=array()){
        $resp=[];
        foreach($params as $key=>$value){
            if($value!=null){
                $resp[$key]=$value;
            }
        }
        return $resp;
    }

    public function MessageTemplate($action,$name, $info=null){
        $ACCOUNT_CREATION="Dear $name, <br> Thank you for creating an account with Pangisa, Please approve the account before seven days elapse to start using your account";
        $ACCOUNT_CREATION.="<br><br> Please follow the link below to approve account. ";
        $ACCOUNT_CREATION.="<a href='".base_url()."Index/confirmaccount/".$info."'><button>Approve Account</button></a>";

        if($action=="ACCOUNT_CREATION") return $ACCOUNT_CREATION;
        if($action=="ACCOUNT_RENEW") return $ACCOUNT_CREATION;
        if($action=="ACCOUNT_BLOCKING") return $ACCOUNT_CREATION;
        if($action=="ACCOUNT_SUSPEND") return $ACCOUNT_CREATION;
    }

    public function log_to_system($info,$uri=null, $traceid=null){
        $uri_segment=null;

        if($uri){$uri_segment=str_replace("/","->",$uri);}

        file_put_contents(APPPATH."logs/log_file.log", date('Y-m-d H:i:s')."|".$uri_segment."|".$traceid."|".$info."\n", FILE_APPEND);
    }

    public function log_payment_to_system($info, $uri = null, $traceid = null)
    {
        $uri_segment = null;

        if ($uri) {
            $uri_segment = str_replace("/", "->", $uri);
        }

        file_put_contents(APPPATH . "logs/sasulalogs.log", date('Y-m-d H:i:s') . "|" . $uri_segment . "|" . $traceid . "|" . $info . "\n", FILE_APPEND);
    }

    public function cleanInput($record)
    {
        return html_escape(htmlspecialchars(trim($record)));
    }

    public function cleanArrayInput(&$input = array())
    {
        $res = [];

        foreach ($input as $key => $value) {
            $res[$key] = $this->cleanInput($value);
        }

    }

    public function checkPhoneNumberInternationalStandard($number){
        $contains_plus = substr($number, 0, 1)=="+"?true:false;

        $isInternationalStandard=substr($number,0,3)=="256"?true:false;;

        if($contains_plus){
            $number=substr($number,1,10);
        }

        if(!$isInternationalStandard){
            $number='256'.$number;
        }


        return $number;

    }

    public function translateStatusCodes($code){
        $res=null;

        switch ($code){
            case 0000: $res= "PENDING"; break;
            case 1003: $res= "CLIENT_REJECTED"; break;
            case 1002: $res= "CLIENT_CANCELLED"; break;
            case 1000: $res= "CLIENT_PENDING"; break;
            case 2001: $res= "ACCEPTED"; break;
            case 2002: $res= "CANCELED"; break;
            case 2003: $res= "REJECTED"; break;
            case 2000: $res= "PENDING"; break;

        }

        return (string)$res;
    }

    public  function subtractDateFromDate($startDate, $endDate){
        $startDate= new DateTime($startDate);
        $endDate= new DateTime($endDate);
        return date_diff($endDate, $startDate)->days;
    }

    public function dateDiffTimeStamp($d1,$d2){
        $date = new DateTime( $d1 );
        $date2 = new DateTime( $d2 );
        $diff = $date2->getTimestamp() - $date->getTimestamp();
       return $diff;
    }

    public  function currentDateMinusNumberOfDays($numberOfDays){
        $cur_date=date('Y-m-d h:i:s');
        $cur_date_minus=new DateTime($cur_date);
        $cur_date_minus=$cur_date_minus->modify('-'.$numberOfDays.' Days')->format('Y-m-d');
        $cur_date_minus=(array)$cur_date_minus;
        return $cur_date_minus[0];
    }

    public function RemoveElementFromAssociativeArray(array  &$array,$element){
        $res=[];

        foreach ($array as $key=>$value){
            if($key==$element){}

            $res[$key]=$value;
        }

        return $res;
    }

    public function getUserIpAddr()
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

    public function sendPostRequest($url, $data, $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        if ($err) {
            return new Response(1000, $err);
        } else {

            //if no curl error
            $headers = explode("\r\n", $headers);
            $headers = array_filter($headers);
            $splitHttpHeaderResponse = explode(" ", $headers[0], 20);

            $httpResponseHeader = new stdClass();
            $httpResponseHeader->code = $splitHttpHeaderResponse[1];
            $httpResponseHeader->remark = $splitHttpHeaderResponse[2];

            $code = 0;
            $msg = null;

            if ($httpResponseHeader->code <> 0 || $httpResponseHeader->code <> 200 || $httpResponseHeader->code <> 202) {
                $code = $httpResponseHeader->code;
                $msg = $httpResponseHeader->remark;
            }

            return new Response($code, $msg, json_decode($body, 1) ?: null);
        }

    }

    public function stripHtmlTagsFromStringOrAssociativeArray($data)
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
}
