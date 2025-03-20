<?php


namespace MiniOrange;

include_once "\x61\x75\x74\x6f\154\157\141\x64\56\160\x68\160";
use MiniOrange\Classes\Actions\ProcessResponseAction;
use MiniOrange\Classes\Actions\ProcessUserAction;
use MiniOrange\Classes\Actions\ReadResponseAction;
use MiniOrange\Classes\Actions\TestResultActions;
use MiniOrange\Helper\Constants;
use MiniOrange\Helper\Messages;
use MiniOrange\Helper\Utilities;
use MiniOrange\Helper\PluginSettings;
final class SSO
{
    public function __construct()
    {
        $ef = PluginSettings::getPluginSettings();
        if (array_key_exists("\x53\x41\x4d\114\122\145\x73\160\157\x6e\x73\x65", $_REQUEST) && !empty($_REQUEST["\123\x41\x4d\114\x52\145\x73\160\157\156\x73\145"])) {
            goto d3;
        }
        Utilities::showErrorMessage(Messages::MISSING_SAML_RESPONSE);
        goto PP;
        d3:
        try {
            $Tz = array_key_exists("\122\x65\x6c\x61\x79\x53\x74\x61\164\x65", $_REQUEST) ? $_REQUEST["\x52\x65\x6c\x61\171\123\164\141\164\145"] : "\x2f";
            $c7 = ReadResponseAction::execute();
            $r7 = new ProcessResponseAction($c7);
            $r7->execute();
            $gt = current(current($c7->getAssertions())->getNameId());
            $dj = current($c7->getAssertions())->getAttributes();
            $dj["\116\141\x6d\145\x49\104"] = array("\60" => $gt);
            $uQ = current($c7->getAssertions())->getSessionIndex();
            if (strcasecmp($Tz, Constants::TEST_RELAYSTATE) == 0) {
                goto RH;
            }
            (new ProcessUserAction($dj, $Tz, $uQ))->execute();
            session_start();
            $_SESSION["\x65\155\x61\x69\154"] = $dj["\x4e\141\155\x65\x49\x44"][0];
            $_SESSION["\165\x73\145\x72\x6e\141\155\x65"] = $dj["\116\141\x6d\x65\111\x44"][0];
            $Ug = $ef->getApplicationUrl();
            if (!empty($Ug)) {
                goto xi;
            }
            echo "\74\150\x74\155\154\76\xd\12\40\40\40\x20\40\40\x20\40\x20\40\x20\x20\40\x20\40\x20\40\x20\x20\x20\40\40\40\x20\x3c\142\x6f\144\171\76\x59\x6f\x75\40\150\x61\166\x65\x20\x62\145\x65\x6e\40\x6c\157\147\147\145\x64\40\x69\156\x21\74\142\x72\57\x3e\15\xa\40\40\40\40\x20\x20\x20\x20\40\x20\x20\40\40\x20\x20\40\x20\x20\x20\40\40\x20\x20\x20\x49\x66\40\x79\x6f\165\40\x77\141\x6e\164\40\x74\157\40\x72\x65\x64\151\x72\145\x63\x74\40\164\157\40\141\x20\144\151\146\146\145\162\145\x6e\x74\40\x55\x52\114\x20\141\x66\164\x65\162\40\154\157\x67\147\x69\156\147\x20\x69\156\x2c\x20\143\157\x6e\x66\x69\x67\165\x72\x65\x20\164\150\145\x20\x41\x70\160\154\x69\x63\141\x74\151\x6f\156\40\x75\162\154\40\151\156\40\123\x74\x65\x70\40\65\40\157\x66\x20\74\x62\76\110\x6f\167\x20\x74\157\40\123\x65\164\165\x70\77\74\x2f\x62\76\40\x74\x61\x62\x20\157\x66\40\164\x68\145\40\143\157\x6e\156\145\x63\164\x6f\x72\56\15\xa\40\40\40\40\x20\40\40\40\40\40\x20\x20\40\40\40\x20\40\x20\40\40\x20\40\x20\x20\x3c\x2f\x62\x6f\x64\171\x3e\15\12\x20\x20\40\40\x20\x20\40\40\40\40\x20\40\x20\40\x20\40\x20\x20\x20\x20\x20\x20\x20\x20\x3c\57\150\x74\x6d\154\x3e";
            goto vZ;
            xi:
            header("\x4c\x6f\143\x61\x74\x69\x6f\x6e\x3a\40" . $Ug);
            die;
            vZ:
            goto sS;
            RH:
            (new TestResultActions($dj))->execute();
            sS:
        } catch (\Exception $YR) {
            if (strcasecmp($Tz, Constants::TEST_RELAYSTATE) == 0) {
                goto Bg;
            }
            Utilities::showErrorMessage($YR->getMessage());
            goto KW;
            Bg:
            (new TestResultActions(array(), $YR))->execute();
            KW:
        }
        PP:
    }
}
new SSO();
