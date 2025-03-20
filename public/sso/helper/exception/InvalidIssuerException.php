<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidIssuerException extends SAMLResponseException
{
    public function __construct($vw, $Bv, $HR)
    {
        $h3 = Messages::parse("\x49\116\126\101\114\111\104\137\x49\x53\x53\x55\105\x52", array("\145\170\160\145\x63\x74" => $vw, "\146\x6f\165\156\144" => $Bv));
        $cM = 101;
        parent::__construct($h3, $cM, $HR, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
