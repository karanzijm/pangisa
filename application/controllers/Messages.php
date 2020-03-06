<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 10/7/2019
 * Time: 12:29 PM
 */

include "CentralAccess.php";

class Messages extends CI_Controller
{
    private $title = "Pangisa";
    private $ctrl_name = 'Customer';
    private $error = null;
    private $link = null;
    private $active_link = null;
    private $form_header_title = null;
    private $page_title = null;
    private $current_page_filter = null;
    private $viewFolder = "customer/";
    private $breadcrumbs = null;
    private $sys_user = 4;
    private $client_user = 6;
    private $vendor_user = 5;
    private $helper = null;
    private $limit = 100;
    private $pagination_display = null;
    private $form_header_link;
    private $userId = null;
    private $ca_h = null;

    public function __construct()
    {
        parent::__construct();
        $this->link = base_url() . $this->ctrl_name . '/';
        $_SERVER['CI_ENV'] = "production";
        $this->load->library("Lib");
        $this->load->library("EmailHelper");
        $this->load->library("user_agent");
        $this->ca_h = new CentralAccess();
        $this->helper = $this->ca_h->getBallisticHelper();
        $this->userId = $this->session->user['id'];
    }

    public function index($senderId = null)
    {
        $this->checkLogin();
        $chatsMessages = [];
        $personToChatWith = [];

        //update the unread for every message one views
        if ($senderId) {
            $this->db->where(['sender' => $senderId, "receiver" => $this->userId])->set(['seen' => 1])->update('chats');
        };

        //check if there is a chat post
        $postChatData = $this->input->post();
        if (count($postChatData) > 0) {
            $receiver = $postChatData['receiver'];
            $sender = $this->userId;
            $message = $postChatData['message'];

            $saveMessageObject = [];
            $saveMessageObject['sender'] = $sender;
            $saveMessageObject['receiver'] = $receiver;
            $saveMessageObject['message'] = $message;
            $saveMessageObject['date'] = date('Y-m-d h:i:s');
            $saveMessageObject['seen'] = 0;
            $saveMessageObject['deleted'] = 0;

            $this->db->insert('chats', $saveMessageObject);
        }

        $chats = $this->db->select("distinct(sender) as senderId, DATE_FORMAT(date, '%M %D, %Y %H:%i:%s') as date, message ,(select name from users where id=senderId) as sender, (select count(*) from chats where sender=senderId and receiver=$this->userId and seen=0) as unread ")
            ->from("chats")
            ->where(["receiver" => $this->userId])
            ->order_by("date", "desc")
            ->order_by("seen", "asc")
            ->group_by("sender")
            ->get()
            ->result_array();

        if (count($chats) < 1) {
            $data['chatWith'] = null;
            $data['chatsMessages'] = [];
            $data['chats'] = [];
            $response_data['data'] = $data;
            $response_data['title'] = "Messages - Pangsia";
            $this->load->view("admin_portal/admin_header", $response_data);
            $this->load->view("chats");
            return;
        }

        $senderIdToRetrieveMessage = $senderId ? $senderId : $chats[0]['senderId'];

        $chatsMessages = $this->db->select("sender as senderId, DATE_FORMAT(date, '%M %D, %Y %H:%i:%s') as date, message ,(select name from users where id=senderId) as sender")
            ->from("chats")
            ->where("(receiver=" . $this->userId . " and sender=" . $senderIdToRetrieveMessage . ") or (receiver=" . $senderIdToRetrieveMessage . " and sender=" . $this->userId . ")")
            ->order_by("id", "asc")
            ->get()
            ->result_array();

        $personToChatWith = $this->db->select("id,name, email")->from("users")->where("id", $senderIdToRetrieveMessage)->get()->row();

        $data['chatWith'] = $personToChatWith ?: null;
        $data['chatsMessages'] = count($chatsMessages) ? $chatsMessages : [];
        $data['chats'] = $chats;

        $response_data['data'] = $data;
        $this->load->view("admin_portal/admin_header");
        $this->load->view("chats", $response_data);
    }

    public function checkLogin()
    {
        if (!$this->session->user['id']) {
            Redirect(base_url() . "Index/login");
        }
    }

}
