<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidOperationException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\111\x4e\x56\x41\x4c\x49\x44\x5f\x4f\x50");
        $cM = 105;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\72\40{$this->message}\xa";
    }
}
