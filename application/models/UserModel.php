<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/22/2018
 * Time: 3:17 PM
 */


class UserModel extends CI_Model {
private $company=null;
private $name=null;
private $email=null;
private $password=null;
private $last_login=null;
private $password_last_modified=null;
private $failure_count=null;
private $date_modified=null;
private $resgistration_type=null;


    /**
     * @param $information
     * @return mixed
     */
    public function save($information){
        $user=array(
            "id"=>$information[''],
            "company"=>$this->session->user['company'],
            "name"=>$information['name'],
            "email"=>$information['email'],
            "password"=>$information['password'],
            "resgistration_type"=>$information['resgistration_type'],
            "added_by"=>$this->session->user['id'],
            "phone"=>$information['phone'],
        );

        $this->db->insert("users",$user);
    }

    /**
     * @param null $filter
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function retrieve($filter = null, $limit = null, $offset = null){
        $this->db->select("u.id,u.name,u.email,u.phone,
                            case when u.registration_type is not null then (select name from app_user_type where id=u.registration_type) else null end as Type,
                            case when u.status=1 then 'Active'   when u.status=0 then 'Blocked' else 'Deleted'  end as status ,date(u.last_login)as 'Last Login'")
            ->where(['company'=>$this->session->user['company']]);

            if(count($filter)>0){
                $this->db->where($filter);
            }

        return $this->db->from("users u")
            ->limit($limit,$offset)
            ->order_by("u.name","desc")
            ->get()
            ->result_array();
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data){
        // TODO: Implement update() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id){

    }

    /**
     * @return mixed
     */
    public function getForm(){

        $user_type=$this->db->select("id,name")->from("app_user_type")->where(["level"=>"app_user"])->get()->result_array();

       $from=array(
           ["name"=>"Name","variable"=>"name","type"=>"text","input_type"=>"text","required"=>"true"],
           ["name"=>"Email","variable"=>"email","type"=>"text","input_type"=>"text","required"=>"true"],
           ["name"=>"User Type","variable"=>"registration_type","type"=>"select","input_type"=>"text","required"=>"true", "options"=>$user_type],
           ["name"=>"Phone Number","variable"=>"phone","type"=>"text","input_type"=>"text","required"=>"true"],
           ["name"=>"Password","variable"=>"password","type"=>"text","input_type"=>"text","required"=>"true"],
           ["name"=>"Confirm Password","variable"=>"confirm_password","type"=>"text","input_type"=>"text","required"=>"true"],


       );

       return $from;
    }
}