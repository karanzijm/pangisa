<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 9/22/2019
 * Time: 2:42 PM
 */

class CommonDatabaseOperations extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getItemById($id)
    {
        $item = $this->db->where(['id' => $id])->get('items')->row();
        return $item ?: null;
    }

    public function getClientById($id)
    {
        $item = $this->db->where(['id' => $id])->get('users')->row();
        return $item ?: null;
    }

    public function getOrderById($id)
    {
        $item = $this->db->where(['id' => $id])->get('orders')->row();
        return $item ?: null;
    }

    public function getOrderByOrderNumber($order_number)
    {
        $item = $this->db->where(['order_number' => $order_number])->get('orders')->row();
        return $item ?: null;
    }

    public function getVendorById($id)
    {
        $item = $this->db->where(['id' => $id])->get('users')->row();
        return $item ?: null;
    }

}
