<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib {
    //draw table to display results
    public function DrawTable($arr,$actions=[], $class=null){
        if(!$arr) return null;
        if(count($arr)<1){return null;}

        $titles=array_keys($arr[0]);

        $table="
            <table  cellspacing='0px' cellpadding='5px' class='table lib_table ".$class."' style='width: 100% !important;' width='100%'>
                <tr>
        ";

        $counter=0;
        foreach ($titles as $title){
            if($counter++ <>0){
                $table.="<td >".strtoupper($title)."</td>";
            }
        }

        if($actions){
            $table.=strtoupper("<td>Action</td>");
        }

        $table.="</tr>";

        foreach ($arr as $ar):
            $table.="<tr>";

            $counter=0;
            foreach ($ar as $key=>$value):
                if($counter++ <>0){
                    $table.=" <td style='max-width: 150px; overflow:hidden;'>".(is_numeric($value)?number_format($value,0,3,''):ucfirst($value))."</td>";
                }
            endforeach;

            //actions
            if($actions){
                $action_string="";

                foreach ($actions as $k=>$v):
                    $action_string.="<a href='".$v."/".$ar['id']."'> ".$k."</a> &nbsp;";
                endforeach;

                $table.="<td>".$action_string."</td>";
            }

            $table.=" </tr>";
        endforeach;

        $table.="</table>";
        return $table;
    }

    //build filter
    public function BuildFilter($data,$submit_action_link){
        if(!$data) return null;
        if(count($data)<1 )return null;
        if(!$submit_action_link || empty($submit_action_link) )return null;

        $filter="<form method='post' action='".$submit_action_link."'>";
            $filter.="<table class='filter-table' style='width: auto;' cellspacing='10px'><tr>";

                //construct filter table using the titles and values sent
                foreach ($data as $dt){
                    $filter.="<td align='right'>";
                        $filter.=$dt['name'].'&nbsp;';
                        $filter.="</td><td>";
                        $filter.="<select name='".$dt['variable']."' required>";
                    $filter.="<option >Please Select </option>";
                            foreach ($dt['data'] as $k):
                                $filter.="<option value='".$k['id']."'>".$k['name']."</option> ";
                            endforeach;
                        $filter.="</select>";
                    $filter.="</td>";
                }

                $filter.="<td valign='top'><button class='btn btn-sm btn-success' >search</button></td>";

            $filter.="</tr></table>";
        $filter.="</form>";
        return $filter;
    }

    //draw tabular form
    public function DrawFormTabular($link,$form_fields){
        $form="<form action='".$link."' method='post'   enctype='multipart/form-data'>";

        //table format
        $form.="<div class='container-fluid'  style='mmin-width: 70%' c,ellspacing='0px' >";
        $form.="<div class='row'>";
        $i=0;

        foreach($form_fields as $field):
            $required=$field['required']?$field['required']:'false';
            $form.="<div class='col-sm-6'>";

            $form.='<div class="form-group build_form_form_group">
                    <label for='.$field["variable"].'>'.$field['name'].'</label>';

            //input of input tag input
                if ($field['type']=="text"):
                if($field['input_type']!="file"){
                    $form.="<input class='form-control' type='".$field['input_type']."'  name='".$field['variable']."' ".($required=='true'?'required':null)."/>";
                }else{
                    $form.="<input class='form-control-file' type='".$field['input_type']."'  name='".$field['variable']."' ".($required=='true'?'required':null)." accept='image/png, image/jpeg,image/jpg,*/pdf/*doc,*/dicx'/>";
                }

            elseif ($field['type']=="text_area") :
                $form.="<textarea id='textarea' class='form-control' required='".$required."'  name='".$field['variable']."'></textarea>";

            //check boxes
            elseif ($field['type']=="check_box") :

            $checkboxes="";
                foreach($field['options'] as $key=>$value):
                    $checkboxes.="<input class='form-control' type='checkbox' value='".$key."' >".$value."</input>";
                endforeach;

                $form.=$checkboxes."";

            //radio buttone
            elseif ($field['type']=="radio_button") :

                $form.="<textarea class='form-control' required='".$required."' ></textarea>";

            //select
            elseif ($field['type']=="select") :
                $select="<select  name='".$field['variable']."' required='".$field['required']."'>";
                $select.="<option>Please Choose a value from list</option>";
            foreach($field['options'] as  $k=>$v):
                $select.="<option value='".$v['id']."'>".$v['name']."</option>";
            endforeach;

                $select.="</select>";

                $form.=$select;
            endif;
            $form.='<small id="emailHelp" class="form-text text-muted">Please enter the '.$field['name'].'</small>';
            $form.='</div>';

            $form.="</div>";
            $i++;

            //cut on two columns
            if($i%2==0){
                $form.="</div><div class='row'>";
            }

        endforeach;

        $form.="</div></div><br><Br> ";

        $form.="<input type='submit' value='SUBMIT FORM' class='btn btn-success btn-sm'/>";

        //$tab
        $form.="</td><td></td></tr></table>";

        $form.="</form>";

        return $form;
    }

    //save logs
    private function saveLog($action,$table=null,$item_id, $flag=false){
        $data=null;

        if ($flag  && $table != null):
            $data = $this->db->where("id", $item_id)->get($table)->result_array();
            $data = count($data)>0?json_encode($data):null;
        endif;

        $dt=array(
            "item_id"=>$item_id,
            "table"=>$table,
            "action"=>$action,
            "original_data"=>$data,
            "user"=>$this->session->user['id'],
        );

        $this->db->insert("logs",$dt);
    }

    //chunk array
    private function chunkArray(array  $arr){
        $res=[];
//        $category = array ([0]=>["Raw Materials"=>"Raw Materials"],[1]=>["Groceries"=>"Groceries"],["Others"=>"others"]);
//        print_r($category);
        print_r($arr);

        foreach ($arr as $k=>$v){
            $res[]=$v;
            echo implode(',',$v);
//            foreach ($k as $key){
//                array_push($res,$key);
//            }
        }

        return $res;
    }

    //redirect when user is restricted from perfoming action

    public function restricted(){
        print "Access to this resource is restricted";
    }

    public function drawCards($information){

    }

    public function filterInput(array $information){
        $res=[];
        foreach ($information as $key=>$value){
            if($value==null || empty($value)){            }
            else{
                array_push($res,[$key=>$value]);
            }
        }

        return $res;
    }

    //delete from any table
    public function delete($table,$id){
        $this->checkLogin();
        $this->saveLog("delete",$table,$id,true);
        $this->db->where("id",$id)->delete($table);

    }
}
