<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidAudienceException extends SAMLResponseException
{
    public function __construct($vw, $Bv, $HR)
    {
        $h3 = Messages::parse("\x49\116\126\x41\114\x49\x44\137\101\x55\x44\x49\105\x4e\x43\105", array("\x65\170\160\x65\x63\x74" => $vw, "\146\x6f\x75\156\x64" => $Bv));
        $cM = 108;
        parent::__construct($h3, $cM, $HR, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
