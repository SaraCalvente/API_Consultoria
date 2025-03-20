<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidSAMLVersionException extends SAMLResponseException
{
    public function __construct($HR)
    {
        $h3 = Messages::parse("\x49\x4e\x56\x41\x4c\x49\104\137\x53\101\x4d\114\137\126\105\x52\x53\x49\x4f\116");
        $cM = 118;
        parent::__construct($h3, $cM, $HR, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
