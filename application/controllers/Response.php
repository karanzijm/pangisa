<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 12/17/2018
 * Time: 10:52 AM
 */

class Response implements JsonSerializable
{
    private $resultCode;
    private $message;
    private $data;

    public  function __construct($code=null,$msg=null, $data=null){
        $this->resultCode=$code;
        $this->message=$msg;
        $this->data=$data;
    }


    /**
     * @return mixed
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * @param mixed $resultCode
     */
    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}