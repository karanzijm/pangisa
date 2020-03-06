<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/19/2018
 * Time: 9:00 PM
 */

class ItemsModel extends CI_Model {
    public function retrieve($filter = null, $limit = null, $offset=null){
    }

    public function update($id, $data){
    }

    public function delete($id){
    }

    public function getForm(){
        $categories=$this->db->select("id, name")->from("categories")->get()->result_array();
        $price_points=$this->db->select("id, name")->from("price_points")->get()->result_array();
        $form=array(
            ["name"=>"Item Name","variable"=>"name","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Item Brand","variable"=>"brand","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Item Category","variable"=>"category","type"=>"select","input_type"=>null,"required"=>"true", "options"=>$categories],
//            ["name"=>"Sub Category","variable"=>"sub_category","type"=>"text","input_type"=>"text","required"=>"false"],
            ["name"=>"Color","variable"=>"color","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Size","variable"=>"size","type"=>"text","input_type"=>"text","required"=>"true"],
            ["name"=>"Rear Photo","variable"=>"rear_view","type"=>"text","input_type"=>"file","required"=>"true"],
            ["name"=>"Front View","variable"=>"front_view","type"=>"text","input_type"=>"file","required"=>"true"],
            ["name"=>"Side View","variable"=>"side_view","type"=>"text","input_type"=>"file","required"=>"true"],
            ["name"=>"Identification Number","variable"=>"identification_number","type"=>"text","input_type"=>"text","required"=>"false"],
            ["name"=>"Brief Description","variable"=>"brief_description","type"=>"text_area","input_type"=>"text","required"=>"true"],
            ["name"=>"Features","variable"=>"features","type"=>"text_area","input_type"=>"text","required"=>"true"],
            ["name"=>"Year of Make","variable"=>"year_of_make","type"=>"text","input_type"=>"number","required"=>"true"],
            ["name"=>"Price","variable"=>"price","type"=>"text","input_type"=>"number","required"=>"false"],
            ["name"=>"Charging Rate","variable"=>"price_point","type"=>"select","input_type"=>null,"required"=>"false", "options"=>$price_points],
            ["name"=>"Pick Up Location ","variable"=>"pick_up_location","type"=>"text","input_type"=>"text","required"=>"false"],
            );
        return $form;
    }

    /**
     * @param $information
     * @return mixed
     */
    public function save($information){
        $this->db->insert("items",$information);
    }
}