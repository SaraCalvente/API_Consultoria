<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidSAMLInstantException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\111\116\x56\101\x4c\111\x44\137\x49\116\x53\124\x41\116\x54");
        $cM = 117;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\x5d\x3a\x20{$this->message}\xa";
    }
}
