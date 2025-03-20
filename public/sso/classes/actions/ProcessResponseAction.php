<?php


namespace MiniOrange\Classes\Actions;

use MiniOrange\Classes\SamlResponse;
use MiniOrange\Helper\Exception\InvalidAudienceException;
use MiniOrange\Helper\Exception\InvalidDestinationException;
use MiniOrange\Helper\Exception\InvalidIssuerException;
use MiniOrange\Helper\Exception\InvalidSamlStatusCodeException;
use MiniOrange\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\Helper\Lib\XMLSecLibs\XMLSecurityKey;
use MiniOrange\Helper\PluginSettings;
use MiniOrange\Helper\SAMLUtilities;
class ProcessResponseAction
{
    private $samlResponse;
    private $acsUrl;
    private $responseSigned;
    private $assertionSigned;
    private $issuer;
    private $spEntityId;
    private $pluginSettings;
    public function __construct(SamlResponse $oZ)
    {
        $this->pluginSettings = PluginSettings::getPluginSettings();
        $this->acsUrl = $this->pluginSettings->getAcsUrl();
        $this->issuer = $this->pluginSettings->getIdpEntityId();
        $this->spEntityId = $this->pluginSettings->getSpEntityId();
        $this->samlResponse = $oZ;
    }
    public function execute()
    {
        $this->validateStatusCode();
        $this->validateDestinationURL();
        $this->validateIssuerAndAudience();
    }
    private function validateStatusCode()
    {
        $n0 = $this->samlResponse->getStatusCode();
        if (!(strpos($n0, "\123\x75\143\x63\145\163\x73") === false)) {
            goto ot;
        }
        throw new InvalidSamlStatusCodeException($n0, $this->samlResponse->getXML());
        ot:
    }
    private function validateIssuerAndAudience()
    {
        $Na = current($this->samlResponse->getAssertions())->getIssuer();
        $JG = current(current($this->samlResponse->getAssertions())->getValidAudiences());
        if (!(strcmp($this->issuer, $Na) != 0)) {
            goto et;
        }
        throw new InvalidIssuerException($this->issuer, $Na, $this->samlResponse->getXML());
        et:
        if (!(strcmp($JG, $this->spEntityId) != 0)) {
            goto Rz;
        }
        throw new InvalidAudienceException($this->spEntityId, $JG, $this->samlResponse->getXML());
        Rz:
    }
    private function validateDestinationURL()
    {
        $Bi = $this->samlResponse->getDestination();
        if (!($Bi !== NULL && $Bi !== $this->acsUrl)) {
            goto Ab;
        }
        throw new InvalidDestinationException($Bi, $this->acsUrl, $this->samlResponse);
        Ab:
    }
}
