<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class MissingIssuerValueException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\115\111\123\123\111\x4e\x47\137\x49\123\x53\x55\x45\122\x5f\x56\101\x4c\125\x45");
        $cM = 123;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\133{$this->code}\135\72\40{$this->message}\xa";
    }
}
