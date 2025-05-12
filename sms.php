<?php
require 'vendor/autoload.php';
require_once 'Util.php';

use AfricasTalking\SDK\AfricasTalking;

class Sms {
    protected $phone;
    protected $AT;

    function __construct($phone){
        $this->phone = $phone;
        $this->AT = new AfricasTalking(
            Util::AT_USERNAME,
            Util::AT_API_KEY
        );
    }

    public function sendSMS($message, $recipients){
        $sms = $this->AT->sms();

        $result = $sms->send([
            'to' => $recipients,
            'message' => $message,
            'from' => Util::SMS_SENDER
        ]);

        return $result;
    }
}
?>
