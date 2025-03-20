<?php


namespace MiniOrange\Classes;

use DOMDocument;
use DOMElement;
use MiniOrange\Helper\SAMLUtilities;
class SamlResponse
{
    private $assertions;
    private $destination;
    private $certificates;
    private $signatureData;
    private $statusCode;
    private $xml;
    public function __construct(DOMElement $HR = NULL)
    {
        $this->assertions = array();
        $this->certificates = array();
        if (!($HR === NULL)) {
            goto Hz;
        }
        return;
        Hz:
        $this->xml = $HR;
        $fR = SAMLUtilities::validateElement($HR);
        if (!($fR !== FALSE)) {
            goto WT;
        }
        $this->certificates = $fR["\103\145\x72\164\151\x66\151\x63\141\164\x65\163"];
        $this->signatureData = $fR;
        WT:
        $Sc = $HR->ownerDocument;
        $gg = new \DOMXpath($Sc);
        if (!@$gg->query("\57\x73\x61\155\154\62\x70\x3a\122\145\x73\x70\157\156\163\x65", $HR)) {
            goto BS;
        }
        $GW = SAMLUtilities::xpQuery($HR, "\x2e\57\x73\141\x6d\x6c\62\x70\x3a\123\x74\x61\164\165\x73\57\x73\141\x6d\x6c\x32\160\72\123\x74\141\x74\x75\163\103\157\144\x65");
        goto fY;
        BS:
        $GW = SAMLUtilities::xpQuery($HR, "\x2e\x2f\x73\x61\x6d\154\160\x3a\123\164\x61\x74\x75\x73\57\x73\141\x6d\x6c\160\72\x53\164\x61\x74\x75\163\103\x6f\x64\145");
        fY:
        $this->statusCode = $GW[0]->getAttribute("\x56\141\x6c\x75\x65");
        if (!$this->xml->hasAttribute("\x44\x65\163\164\x69\x6e\x61\x74\151\x6f\156")) {
            goto rM;
        }
        $this->destination = $this->xml->getAttribute("\104\145\163\164\151\156\x61\164\151\x6f\x6e");
        rM:
        $uu = $this->xml->firstChild;
        xH:
        if (!($uu !== NULL)) {
            goto LS;
        }
        if (!($uu->namespaceURI !== "\165\x72\156\72\x6f\x61\x73\151\x73\72\x6e\x61\155\145\x73\x3a\x74\143\x3a\123\101\115\114\72\x32\56\x30\72\x61\163\x73\x65\162\164\151\x6f\x6e")) {
            goto ck;
        }
        goto vw;
        ck:
        if (!($uu->localName === "\x41\x73\x73\x65\x72\x74\x69\x6f\156" || $uu->localName === "\105\x6e\143\x72\x79\x70\164\x65\144\x41\x73\x73\x65\162\x74\151\157\x6e")) {
            goto UE;
        }
        $this->assertions[] = new Assertion($uu);
        UE:
        vw:
        $uu = $uu->nextSibling;
        goto xH;
        LS:
    }
    public function getAssertions()
    {
        return $this->assertions;
    }
    public function setAssertions(array $S4)
    {
        $this->assertions = $S4;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function getXML()
    {
        return $this->xml;
    }
    public function __toString()
    {
        $ro = new DOMDocument();
        $ro->preserveWhiteSpace = FALSE;
        $ro->formatOutput = TRUE;
        $ro->loadXML($this->xml->ownerDocument->saveXML($this->xml));
        return $ro->saveXml();
    }
}
