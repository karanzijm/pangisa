<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/19/2018
 * Time: 10:39 PM
 */

class BusinessModel extends CI_Model{
    private $salt="123456789abcdefghijklmnopq";
    private $tableName="users";
    private $sys_user=4;
    private $client_user=6;
    private $vendor_user=5;

    public function retrieve($type,$filter = [], $limit = null, $offset = null){
        $this->db
            ->where(["registration_type"=>$type])
            ->select("id,name,  email, phone , location,role,user_code," .
                ($type==$this->client_user?"(select count(*) from orders where client=users.id) as orders,":null)."".
                ($type==$this->vendor_user?"(select count(*) from items where added_by=users.id) as Items,":null)."".
                ($type==$this->vendor_user?"(select count(*) from orders where owner=users.id) as Orders,":null)."".
                ($type==$this->vendor_user?"(select count(*) from orders where owner=users.id and approved=1) as Successful,":null)
                ."
                (case when status=1 then 'Active' else 'Suspended' end )as status, date(date) as date")
            ->from("users");

        if($filter){
            $this->db->where($filter);
        }

        return $this->db->limit($limit,$offset)
            ->order_by("date","desc")
            ->get()
            ->result_array();
    }

    //filter parameters
    private function filter_params($information){
        $res=[];

        foreach($information as $k=>$v){
            if($v==null || empty($v)){}
            else{
                array_push($res,[$k=>$v]);
            }
        }

        return $res;
    }

    public function update($id, $data){
    }

    public function delete($id){
    }

    public function getForm(){

        $form=array(
            ["name"=>"Individual or Company Name","variable"=>"name","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Business Location ","variable"=>"location","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Phone Number","variable"=>"phone","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Alternative Telephone","variable"=>"alternative_phone","type"=>"text","input_type"=>"text","required"=>"false"],
            ["name"=>"Email","variable"=>"email","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Company Certificate Or National Id(for individuals)","variable"=>"id_photo","type"=>"text","input_type"=>"file","required"=>"true"],
            ["name"=>"Password","variable"=>"admin_password","type"=>"text","input_type"=>"password","required"=>"true"],
            ["name"=>"Confirm Password","variable"=>"confirm_password","type"=>"text","input_type"=>"password","required"=>"true"]
        );

        return $form;
    }

    public function save($information){
        $res=null;

        if($information['admin_password']<>$information['confirm_password']){
            $res= "Password do not match, Please retry";
            return $res;
        }

        $information['admin_password']=sha1($information['admin_password']);

        $data=array(
            "name"=>$information['name'],
            "phone"=>$information['phone'],
            "email"=>$information['email'],
            "logo"=>$information['logo'],
            "admin_password"=>$information['admin_password'],
            "location"=>$information['location'],
            "contact_person"=>$information['contact_person'],
            "contact_person_phone"=>$information['contact_person_phone'],
        );

        $this->db->insert($this->tableName,$data);
        return $res;
    }
}
