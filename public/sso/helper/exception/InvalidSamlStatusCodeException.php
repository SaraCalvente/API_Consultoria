<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidSamlStatusCodeException extends SAMLResponseException
{
    public function __construct($n0, $HR)
    {
        $h3 = Messages::parse("\111\x4e\126\x41\x4c\x49\x44\x5f\111\x4e\123\124\x41\116\x54", array("\163\164\x61\x74\x75\x73\x63\x6f\144\145" => $n0));
        $cM = 117;
        parent::__construct($h3, $cM, $HR, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\133{$this->code}\x5d\x3a\40{$this->message}\12";
    }
}
