<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/4/2019
 * Time: 4:01 PM
 */

class EmailHelper{
    protected $CI;
    private  $viewfolder;
    private  $data=null;

    public function __construct(){
        $this->CI =& get_instance();
        $this->viewfolder="emailhelpers/";
    }

    public function composeEmailOnAccountCreation($type="user",$id, $hash){
        $this->CI->load->library("session","upload","pagination","user_agent","database");
        $emailResultAfterComposition=null;

        $user=$this->CI->db->where("id",$id)->get("users")->row();
        $info['userObject'] = $user ?: null;
        $info['hashedActivationKey']=$hash;

        $this->data=$info;
        $emailResultAfterComposition=$this->CI->load->view($this->viewfolder."AccountCreationApplicationUser",$this->data,true);
        return $emailResultAfterComposition;
    }

    public  function composeEmailForApplicationUserPlaceOrder($itemId, $userId, $orderId){
        $this->CI->load->library("session","upload","pagination","user_agent","database");
        $user=$this->CI->db->where("id",$userId)->get("users")->row();
        $item=$this->CI->db->where("id",$itemId)->get("items")->row();
        $orderDeatisl=$this->CI->db->where("id",$orderId)->get("orders")->row();

        $info['userObject']=$user;
        $info['orderObject']=$orderDeatisl;
        $info['item']=$item;

        $this->data=$info;
        $emailResultAfterComposition=$this->CI->load->view($this->viewfolder."PlacedOrderCustomer",$this->data,true);
        return $emailResultAfterComposition;
    }

    public  function composeEmailForApplicationClientPlaceOrder($itemId){

    }

    public  function composeEmailForApplicationUserOrderApproved($orderId){

    }

}