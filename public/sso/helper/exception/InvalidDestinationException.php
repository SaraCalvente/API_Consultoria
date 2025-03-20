<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidDestinationException extends SAMLResponseException
{
    public function __construct($Yq, $Vt, $HR)
    {
        $h3 = Messages::parse("\111\116\x56\x41\114\x49\104\137\104\x45\x53\124\111\x4e\x41\x54\111\117\116", array("\144\x65\x73\x74\151\156\x61\x74\x69\x6f\156" => $Yq, "\143\165\x72\x72\145\156\164\x75\162\x6c" => $Vt));
        $cM = 108;
        parent::__construct($h3, $cM, $HR, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\x5d\72\40{$this->message}\xa";
    }
}
