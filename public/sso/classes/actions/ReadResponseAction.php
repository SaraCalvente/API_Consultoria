<?php


namespace MiniOrange\Classes\Actions;

use MiniOrange\Classes\SamlResponse;
use MiniOrange\Helper\Utilities;
class ReadResponseAction
{
    public static function execute()
    {
        $Pp = $_REQUEST["\123\x41\x4d\x4c\x52\x65\x73\160\x6f\x6e\x73\145"];
        $Ag = array_key_exists("\122\145\x6c\x61\171\123\x74\x61\164\145", $_REQUEST) ? $_REQUEST["\x52\145\x6c\x61\x79\123\x74\141\x74\x65"] : "\x2f";
        $Pp = base64_decode($Pp);
        if (array_key_exists("\123\x41\115\x4c\122\x65\x73\x70\157\x6e\163\x65", $_POST)) {
            goto cv;
        }
        $Pp = gzinflate($Pp);
        cv:
        $iw = new \DOMDocument();
        $iw->loadXML($Pp);
        $oZ = $iw->firstChild;
        if (!($oZ->localName == "\x4c\157\x67\x6f\165\x74\x52\x65\163\160\157\x6e\x73\145")) {
            goto aI;
        }
        return;
        aI:
        $Pp = new SamlResponse($oZ);
        return $Pp;
    }
}
