<?php


namespace MiniOrange\Classes;

use MiniOrange\Helper\Constants;
use MiniOrange\Helper\PluginSettings;
use MiniOrange\Helper\SAMLUtilities;
class AuthnRequest
{
    private $requestType = Constants::AUTHN_REQUEST;
    private $acsUrl;
    private $issuer;
    private $ssoUrl;
    private $forceAuthn;
    private $bindingType;
    public function __construct($RJ, $Na, $ji, $s7, $lE)
    {
        $this->acsUrl = $RJ;
        $this->issuer = $Na;
        $this->forceAuthn = $s7;
        $this->destination = $ji;
        $this->bindingType = $lE;
    }
    private function generateXML()
    {
        $XE = "\74\x3f\170\x6d\x6c\40\166\145\162\x73\x69\x6f\x6e\75\42\x31\56\60\x22\x20\x65\156\x63\157\144\151\x6e\147\75\x22\125\124\x46\x2d\70\42\77\76" . "\x20\74\x73\x61\155\x6c\160\72\101\x75\164\150\x6e\x52\145\161\x75\x65\x73\x74\x20\15\xa\40\40\x20\40\x20\40\40\x20\x20\40\40\x20\40\x20\40\40\40\40\x20\40\40\x20\x20\x20\40\x20\x20\x20\40\x20\40\x20\170\155\x6c\x6e\x73\72\x73\141\155\154\160\x3d\42\x75\x72\x6e\x3a\157\141\x73\151\x73\72\156\141\x6d\145\x73\x3a\x74\143\x3a\x53\x41\x4d\x4c\x3a\x32\56\x30\72\x70\162\x6f\x74\157\143\157\x6c\x22\x20\xd\xa\x20\x20\x20\40\40\x20\40\x20\x20\40\x20\x20\40\40\40\x20\x20\x20\40\40\x20\x20\x20\x20\40\x20\x20\x20\x20\x20\40\40\170\x6d\154\156\x73\75\42\165\x72\x6e\x3a\157\141\x73\x69\163\x3a\x6e\141\155\145\x73\72\164\143\72\123\x41\115\x4c\x3a\62\56\60\72\x61\x73\x73\x65\162\x74\151\x6f\156\42\40\x49\104\x3d\x22" . SAMLUtilities::generateID() . "\x22\x20\x20\x56\x65\x72\163\151\x6f\x6e\75\42\62\x2e\x30\42\40\x49\163\x73\x75\145\111\x6e\x73\164\141\156\164\x3d\x22" . SAMLUtilities::generateTimestamp() . "\x22";
        if (!($this->forceAuthn == "\124\122\125\x45")) {
            goto tI;
        }
        $XE .= "\40\x46\157\162\x63\145\x41\x75\164\150\x6e\75\42\164\x72\x75\x65\42";
        tI:
        $XE .= "\40\40\40\40\x20\x50\162\x6f\x74\157\x63\x6f\154\x42\151\x6e\x64\x69\156\147\75\x22\165\162\156\72\x6f\x61\x73\x69\163\72\x6e\141\155\x65\x73\x3a\164\143\x3a\x53\101\x4d\x4c\72\62\56\x30\x3a\142\x69\156\x64\151\156\x67\163\x3a\110\x54\x54\120\x2d\x50\x4f\123\x54\x22\x20\101\x73\x73\x65\x72\x74\x69\157\156\x43\x6f\156\163\165\155\x65\x72\x53\x65\162\x76\151\143\x65\x55\122\x4c\x3d\x22" . $this->acsUrl . "\x22\40\40\40\x20\40\40\x44\145\163\164\x69\156\141\164\x69\x6f\156\75\42" . $this->destination . "\42\x3e\xd\12\x20\40\40\40\40\x20\40\x20\x20\x20\x20\x20\40\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\x20\40\x20\40\74\163\141\155\x6c\x3a\111\x73\x73\x75\145\x72\x20\x78\155\x6c\x6e\163\72\163\141\155\154\75\42\165\162\156\x3a\x6f\141\163\151\163\x3a\x6e\x61\155\145\163\x3a\x74\x63\72\x53\x41\115\114\x3a\62\56\x30\72\x61\163\163\145\162\164\151\157\156\x22\x3e" . $this->issuer . "\74\x2f\163\x61\155\154\72\111\163\163\x75\x65\162\76\xd\12\40\40\x20\x20\40\x20\x20\40\40\40\40\x20\40\x20\x20\x20\x20\x20\40\x20\x20\40\40\x20\x20\x20\40\x20\40\40\40\x20\74\163\141\x6d\x6c\x70\x3a\x4e\141\155\145\111\104\120\157\154\x69\x63\x79\x20\101\154\154\x6f\x77\103\x72\145\141\x74\x65\75\42\164\x72\165\145\x22\40\106\157\x72\155\x61\164\x3d\x22\x75\x72\x6e\72\157\x61\163\151\163\x3a\156\x61\x6d\145\x73\72\164\143\72\123\x41\115\114\x3a\61\x2e\61\x3a\156\x61\155\145\151\144\x2d\x66\157\x72\155\x61\x74\x3a\x75\156\163\160\145\x63\x69\146\x69\145\144\42\57\76\15\xa\x20\x20\x20\40\40\x20\40\40\x20\x20\40\x20\40\x20\40\x20\40\40\x20\40\40\40\40\40\x20\x20\x20\40\x3c\57\163\141\x6d\154\160\x3a\x41\x75\164\x68\x6e\122\x65\x71\x75\145\163\x74\76";
        return $XE;
    }
    public function build()
    {
        $ef = PluginSettings::getPluginSettings();
        $XE = $this->generateXML();
        if (!(empty($this->bindingType) || $this->bindingType == $ef->getLoginBindingType())) {
            goto yW;
        }
        $i8 = gzdeflate($XE);
        $GL = base64_encode($i8);
        $X9 = urlencode($GL);
        $XE = $X9;
        yW:
        return $XE;
    }
}
