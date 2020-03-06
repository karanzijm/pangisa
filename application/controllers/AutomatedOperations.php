<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/14/2019
 * Time: 1:46 PM
 */

include APPPATH . "/controllers/Ballistics.php";
class AutomatedOperations extends CI_Controller {
    private $helper=null;
    private $limit=20;

    public function __construct(){
        parent::__construct();
        $_SERVER['CI_ENV']="production";
        $this->load->library("Lib");
        $this->load->library("EmailHelper");
        $this->load->model("BusinessModel");
        $this->helper=new Ballistics();
    }

    public function Index(){
        echo "Automatic Class";
    }

    public function SendSmsAutomaticSmsOne(){
        $messages=$this->db->where(["status"=>0])->limit($this->limit)->get("message_outbox")->result_array();

        if(count($messages)<1){return;}

        foreach ( $messages as $message):
            $text='Pangisa -'.$message['message'];
            $to=$message['phone_number'];

            $resultFromMessage=$this->helper->sendSmsSmsOne($to,$text);
            $response=json_decode($resultFromMessage,1);

            $messageId=$response['message_id'];
            $status=$response['status']=='F'?0:1;
            $remarks=$response['remarks'];

            $dbUpdateSmsResult['messageid']=$messageId;
            $dbUpdateSmsResult['response']=$remarks;
            $dbUpdateSmsResult['status']=$status;
            $dbUpdateSmsResult['response_date']=date("Y-m-dd h:i:s");

            $this->db->where(["id"=>$message['id']])->update("message_outbox", $dbUpdateSmsResult);
        endforeach;

    }

    public function SendEmailAutomatedService(){
        $email=$this->db->where(["status"=>0])->limit($this->limit)->get("email_outbox")->result_array();

        if(count($email)<1){return;}

        foreach ( $email as $message):
            $body=$message['body'];
            $to=$message['email_address'];
            $subject=$message['subject'];
            $resultFromMessage=$this->helper->sendMail($subject,$to,$body);

            echo $resultFromMessage==true?"sent ":"not sent";

            $this->db->where(["id"=>$message['id']])->update("email_outbox", ["status"=>$resultFromMessage==true?1:0]);
        endforeach;
    }
}