<?php


namespace MiniOrange\Classes\Actions;

use MiniOrange\Classes\AuthnRequest;
use MiniOrange\Helper\Constants;
use MiniOrange\Helper\Exception\NoIdentityProviderConfiguredException;
use MiniOrange\Helper\PluginSettings;
use MiniOrange\Helper\Utilities;
class SendAuthnRequest
{
    public static function execute()
    {
        $ef = PluginSettings::getPluginSettings();
        if (Utilities::isSPConfigured()) {
            goto i9;
        }
        throw new NoIdentityProviderConfiguredException();
        i9:
        $Ag = isset($_REQUEST["\x52\x65\x6c\x61\x79\123\164\141\164\x65"]) ? $_REQUEST["\122\x65\154\x61\x79\123\x74\x61\164\x65"] : "\x2f";
        $Fj = (new AuthnRequest($ef->getAcsUrl(), $ef->getSpEntityId(), $ef->getSamlLoginUrl(), $ef->getForceAuthentication(), $ef->getLoginBindingType()))->build();
        $lE = $ef->getLoginBindingType();
        if (empty($lE) || $lE == Constants::HTTP_REDIRECT) {
            goto mQ;
        }
        (new HttpAction())->sendHTTPPostRequest($Fj, $Ag, $ef->getSamlLoginUrl());
        goto e9;
        mQ:
        return (new HttpAction())->sendHTTPRedirectRequest($Fj, $Ag, $ef->getSamlLoginUrl());
        e9:
    }
}
