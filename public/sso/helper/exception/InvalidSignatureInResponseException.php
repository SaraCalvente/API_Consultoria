<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidSignatureInResponseException extends SAMLResponseException
{
    private $pluginCert;
    private $certInResponse;
    public function __construct($HU, $XX, $HR)
    {
        $h3 = Messages::parse("\x49\x4e\126\101\114\111\104\137\x52\105\123\x50\117\x4e\123\105\x5f\123\x49\x47\x4e\x41\x54\125\x52\x45");
        $cM = 120;
        $this->pluginCert = $HU;
        $this->certInResponse = $XX;
        parent::__construct($h3, $cM, $HR, TRUE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
    public function getPluginCert()
    {
        return Messages::parse("\106\117\122\x4d\101\124\x54\x45\x44\x5f\x43\x45\122\124", array("\x63\x65\162\164" => $this->pluginCert));
    }
    public function getCertInResponse()
    {
        return Messages::parse("\106\117\122\x4d\x41\124\124\105\x44\137\103\105\122\x54", array("\x63\145\x72\x74" => $this->certInResponse));
    }
}
