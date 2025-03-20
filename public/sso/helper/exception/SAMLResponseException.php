<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class SAMLResponseException extends \Exception
{
    private $samlResponse;
    private $isCertError;
    public function __construct($h3, $cM, $HR, $JT)
    {
        $this->xml = $HR;
        $this->isCertError = $JT;
        parent::__construct($h3, $cM, NULL);
    }
    public function getSamlResponse()
    {
        return Messages::parse("\123\101\115\114\x5f\x52\x45\x53\x50\x4f\x4e\x53\105", array("\x78\155\154" => $this->parseXML($this->xml)));
    }
    public function getPluginCert()
    {
    }
    public function getCertInResponse()
    {
    }
    public function isCertError()
    {
        return $this->isCertError;
    }
    public static function parseXML($HR)
    {
        $ro = new \DOMDocument();
        $ro->preserveWhiteSpace = TRUE;
        $ro->formatOutput = TRUE;
        $ro->loadXML($HR->ownerDocument->saveXML($HR));
        return htmlentities($ro->saveXml());
    }
}
