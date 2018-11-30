<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/8/18
 * Time: 4:28 PM
 */

namespace App\Responses;


class Response
{
    public $status;
    public $data;
    public $messages;

    protected $msg_array = [
        200 => "OK",
        400 => "Bad Request",
        401 => "Unauthorized",
    ];

    public function __construct($code = 200, $msg = null){
        $this->code = $code;

        $this->msg = ($msg) ? $msg : $this->getMsgTextAttribute();
    }

    public function getMsgTextAttribute(){
        return array_get($this->msg_array, $this->code, 'N/A');
    }

    public function setStatus($value){
        $this->status = $value;
    }

    public function setData($value){
        $this->data = $value;
    }

    public function setMessage($value){
        $this->messages = $value;
    }
}