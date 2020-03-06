<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 10/28/2018
 * Time: 10:53 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class CoreHelper{
    protected $CI;

    public function __construct(){
    $this->CI =& get_instance();
    }

    public function saveLog($table,$action,$save,$id=null){
        $this->CI->load->library("session","upload","pagination","user_agent","database");

        $info=null;
        if($save){
            $info=$this->db->where("id",$id)->get($table)->row();
        }

        $data['original_data']=$info;
        $data['user']=$this->session->user['id'];
        $data['action']=$action;
        $data['item_id']=$id;
        $data['table']=$table;

        $this->db->insert($table,$data);
    }

}