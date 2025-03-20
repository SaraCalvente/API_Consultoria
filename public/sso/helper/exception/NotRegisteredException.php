<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class NotRegisteredException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\111\x4e\x56\x41\x4c\111\104\x5f\x4c\111\103\x45\116\x53\x45");
        $cM = 102;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\40{$this->message}\12";
    }
}
