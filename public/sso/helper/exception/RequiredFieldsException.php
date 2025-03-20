<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class RequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\x52\105\121\x55\x49\x52\105\x44\x5f\106\111\105\114\104\123");
        $cM = 104;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
