<?php


namespace MiniOrange\Classes;

use DOMElement;
use Exception;
use DOMText;
use MiniOrange\Helper\Lib\XMLSecLibs\XMLSecurityKey;
use MiniOrange\Helper\SAMLUtilities;
use MiniOrange\Helper\Utilities;
use MiniOrange\Helper\Exception\InvalidSAMLVersionException;
use MiniOrange\Helper\Exception\MissingIDException;
use MiniOrange\Helper\Exception\MissingIssuerValueException;
use MiniOrange\Helper\Exception\InvalidNumberOfNameIDsException;
use MiniOrange\Helper\Exception\MissingNameIdException;
class Assertion
{
    private $id;
    private $issueInstant;
    private $issuer;
    private $nameId;
    private $encryptedNameId;
    private $encryptedAttribute;
    private $encryptionKey;
    private $notBefore;
    private $notOnOrAfter;
    private $validAudiences;
    private $sessionNotOnOrAfter;
    private $sessionIndex;
    private $authnInstant;
    private $authnContextClassRef;
    private $authnContextDecl;
    private $authnContextDeclRef;
    private $AuthenticatingAuthority;
    private $attributes;
    private $nameFormat;
    private $signatureKey;
    private $certificates;
    private $signatureData;
    private $requiredEncAttributes;
    private $SubjectConfirmation;
    protected $wasSignedAtConstruction = FALSE;
    public function __construct(DOMElement $HR = NULL)
    {
        $this->id = SAMLUtilities::generateId();
        $this->issueInstant = SAMLUtilities::generateTimestamp();
        $this->issuer = '';
        $this->authnInstant = SAMLUtilities::generateTimestamp();
        $this->attributes = array();
        $this->nameFormat = "\165\x72\156\72\x6f\141\x73\151\163\72\156\x61\155\145\x73\72\164\x63\x3a\x53\x41\115\x4c\x3a\x31\56\x31\72\x6e\x61\x6d\x65\151\x64\x2d\x66\x6f\x72\x6d\x61\x74\72\165\156\163\x70\x65\x63\151\x66\151\145\144";
        $this->certificates = array();
        $this->AuthenticatingAuthority = array();
        $this->SubjectConfirmation = array();
        if (!($HR === NULL)) {
            goto Hd;
        }
        return;
        Hd:
        if (!($HR->localName === "\x45\x6e\x63\162\x79\160\164\145\x64\101\163\x73\145\162\164\151\x6f\x6e")) {
            goto uU;
        }
        $L2 = SAMLUtilities::xpQuery($HR, "\56\x2f\x78\x65\x6e\x63\x3a\105\156\x63\162\171\160\x74\x65\x64\x44\x61\164\141");
        $pi = SAMLUtilities::xpQuery($HR, "\x2e\x2f\170\145\x6e\x63\x3a\x45\156\x63\162\171\x70\x74\x65\x64\x44\141\x74\141\x2f\144\x73\x3a\x4b\x65\x79\x49\156\x66\x6f");
        $Ry = $pi[0]->firstChild->firstChild->getAttribute("\x41\154\x67\x6f\x72\x69\x74\x68\x6d");
        $HC = SAMLUtilities::getEncryptionAlgorithm($Ry);
        if (count($L2) === 0) {
            goto gT;
        }
        if (count($L2) > 1) {
            goto wG;
        }
        goto C0;
        gT:
        throw new Exception("\x4d\151\163\163\x69\156\x67\x20\145\x6e\143\x72\171\x70\x74\145\x64\x20\144\x61\164\x61\x20\151\156\x20\x3c\x73\x61\155\154\72\105\x6e\143\x72\x79\x70\164\x65\x64\101\x73\163\x65\162\164\151\157\x6e\76\56");
        goto C0;
        wG:
        throw new Exception("\115\157\162\x65\40\164\x68\x61\156\40\157\156\145\40\x65\x6e\x63\x72\x79\x70\x74\x65\144\x20\x64\x61\x74\141\40\145\x6c\145\155\145\156\x74\40\151\156\40\74\163\141\x6d\154\x3a\105\x6e\143\x72\171\x70\164\145\144\x41\163\163\x65\x72\x74\151\x6f\156\76\x2e");
        C0:
        $w5 = new XMLSecurityKey($HC, array("\x74\171\160\145" => "\160\162\x69\166\141\x74\x65"));
        $dJ = file_get_contents(Utilities::getPrivateKey());
        $w5->loadKey($dJ);
        $kZ = array();
        $HR = SAMLUtilities::decryptElement($L2[0], $w5, $kZ);
        uU:
        if ($HR->hasAttribute("\x49\104")) {
            goto jO;
        }
        throw new MissingIDException();
        jO:
        $this->id = $HR->getAttribute("\111\104");
        if (!($HR->getAttribute("\126\x65\162\x73\x69\x6f\156") !== "\62\56\x30")) {
            goto Tx;
        }
        throw new InvalidSAMLVersionException($HR);
        Tx:
        $this->issueInstant = SAMLUtilities::xsDateTimeToTimestamp($HR->getAttribute("\111\163\163\x75\x65\111\156\x73\164\x61\156\164"));
        $Na = SAMLUtilities::xpQuery($HR, "\x2e\57\x73\x61\x6d\x6c\x5f\141\x73\x73\145\x72\164\151\x6f\x6e\72\x49\163\163\x75\x65\x72");
        if (!empty($Na)) {
            goto XB;
        }
        throw new MissingIssuerValueException();
        XB:
        $this->issuer = trim($Na[0]->textContent);
        $this->parseConditions($HR);
        $this->parseAuthnStatement($HR);
        $this->parseAttributes($HR);
        $this->parseEncryptedAttributes($HR);
        $this->parseSignature($HR);
        $this->parseSubject($HR);
    }
    private function parseSubject(\DOMElement $HR)
    {
        $DV = SAMLUtilities::xpQuery($HR, "\56\57\163\141\155\x6c\137\141\x73\x73\x65\162\x74\x69\x6f\x6e\72\123\165\142\152\x65\x63\164");
        if (empty($DV)) {
            goto eg;
        }
        if (count($DV) > 1) {
            goto nY;
        }
        goto Sn;
        eg:
        return;
        goto Sn;
        nY:
        throw new Exception("\x4d\x6f\x72\x65\x20\164\150\x61\156\40\x6f\156\145\40\74\x73\141\x6d\154\x3a\x53\x75\142\x6a\x65\143\164\76\40\x69\156\40\74\163\141\x6d\x6c\x3a\101\163\x73\145\162\164\x69\157\156\76\56");
        Sn:
        $DV = $DV[0];
        $E3 = SAMLUtilities::xpQuery($DV, "\56\x2f\163\x61\155\154\x5f\141\x73\163\x65\x72\164\x69\157\x6e\x3a\116\141\155\145\111\x44\x20\x7c\40\56\x2f\163\141\155\x6c\x5f\x61\163\x73\145\162\x74\151\x6f\x6e\72\105\x6e\x63\x72\171\160\164\145\x64\x49\104\x2f\x78\x65\x6e\x63\x3a\105\156\143\x72\x79\x70\164\x65\x64\104\x61\x74\141");
        if (empty($E3)) {
            goto zL;
        }
        if (count($E3) > 1) {
            goto pt;
        }
        goto dc;
        zL:
        throw new MissingNameIdException();
        goto dc;
        pt:
        throw new InvalidNumberOfNameIDsException();
        dc:
        $E3 = $E3[0];
        if ($E3->localName === "\105\x6e\143\162\x79\x70\x74\x65\x64\104\141\x74\x61") {
            goto Cy;
        }
        $this->nameId = SAMLUtilities::parseNameId($E3);
        goto Ud;
        Cy:
        $this->encryptedNameId = $E3;
        Ud:
    }
    private function parseConditions(\DOMElement $HR)
    {
        $zj = SAMLUtilities::xpQuery($HR, "\56\x2f\163\x61\155\154\137\141\163\x73\145\x72\164\x69\x6f\x6e\72\x43\x6f\x6e\x64\x69\x74\x69\157\x6e\x73");
        if (empty($zj)) {
            goto gl;
        }
        if (count($zj) > 1) {
            goto Zi;
        }
        goto TP;
        gl:
        return;
        goto TP;
        Zi:
        throw new Exception("\115\157\162\x65\40\x74\150\x61\156\40\x6f\x6e\145\40\74\x73\141\155\x6c\x3a\x43\x6f\156\144\x69\x74\151\x6f\x6e\x73\x3e\x20\x69\x6e\x20\x3c\163\x61\x6d\x6c\72\101\163\x73\x65\x72\164\151\x6f\x6e\x3e\x2e");
        TP:
        $zj = $zj[0];
        if (!$zj->hasAttribute("\x4e\157\x74\x42\145\146\x6f\x72\x65")) {
            goto yw;
        }
        $Sq = SAMLUtilities::xsDateTimeToTimestamp($zj->getAttribute("\x4e\157\x74\x42\145\146\x6f\x72\x65"));
        if (!($this->notBefore === NULL || $this->notBefore < $Sq)) {
            goto aV;
        }
        $this->notBefore = $Sq;
        aV:
        yw:
        if (!$zj->hasAttribute("\x4e\x6f\164\x4f\x6e\x4f\x72\101\x66\164\145\162")) {
            goto NZ;
        }
        $SD = SAMLUtilities::xsDateTimeToTimestamp($zj->getAttribute("\x4e\157\164\x4f\x6e\x4f\162\x41\x66\x74\145\x72"));
        if (!($this->notOnOrAfter === NULL || $this->notOnOrAfter > $SD)) {
            goto VT;
        }
        $this->notOnOrAfter = $SD;
        VT:
        NZ:
        $uu = $zj->firstChild;
        xr:
        if (!($uu !== NULL)) {
            goto UX;
        }
        if (!$uu instanceof DOMText) {
            goto p4;
        }
        goto pQ;
        p4:
        if (!($uu->namespaceURI !== "\x75\x72\x6e\x3a\157\x61\x73\151\163\x3a\156\x61\155\145\x73\x3a\164\x63\x3a\x53\101\115\114\x3a\x32\x2e\60\x3a\141\163\163\145\162\x74\151\x6f\156")) {
            goto Cn;
        }
        throw new Exception("\x55\156\153\156\157\x77\x6e\x20\x6e\x61\155\145\x73\x70\x61\143\145\x20\157\146\40\x63\157\156\144\151\x74\151\x6f\x6e\72\x20" . var_export($uu->namespaceURI, TRUE));
        Cn:
        switch ($uu->localName) {
            case "\101\165\x64\x69\145\x6e\x63\x65\x52\145\163\x74\x72\151\x63\164\x69\157\156":
                $X0 = SAMLUtilities::extractStrings($uu, "\x75\x72\x6e\x3a\157\x61\x73\151\x73\x3a\156\x61\x6d\x65\163\72\x74\143\72\x53\101\115\x4c\72\x32\x2e\60\72\141\163\x73\145\162\x74\x69\x6f\156", "\x41\x75\x64\151\145\156\x63\x65");
                if ($this->validAudiences === NULL) {
                    goto Mn;
                }
                $this->validAudiences = array_intersect($this->validAudiences, $X0);
                goto a5;
                Mn:
                $this->validAudiences = $X0;
                a5:
                goto oj;
            case "\x4f\x6e\x65\x54\151\x6d\x65\125\163\x65":
                goto oj;
            case "\120\x72\x6f\170\x79\x52\x65\163\x74\x72\x69\143\164\x69\x6f\156":
                goto oj;
            default:
                throw new Exception("\125\156\x6b\x6e\157\x77\156\40\143\157\x6e\x64\x69\x74\151\157\x6e\72\x20" . var_export($uu->localName, TRUE));
        }
        EB:
        oj:
        pQ:
        $uu = $uu->nextSibling;
        goto xr;
        UX:
    }
    private function parseAuthnStatement(\DOMElement $HR)
    {
        $O7 = SAMLUtilities::xpQuery($HR, "\56\x2f\163\141\x6d\x6c\137\141\x73\x73\x65\x72\x74\x69\x6f\156\72\101\x75\164\x68\x6e\123\164\x61\164\145\x6d\x65\156\x74");
        if (empty($O7)) {
            goto rl;
        }
        if (count($O7) > 1) {
            goto hp;
        }
        goto Td;
        rl:
        $this->authnInstant = NULL;
        return;
        goto Td;
        hp:
        throw new Exception("\115\x6f\x72\x65\40\x74\150\141\x74\x20\x6f\x6e\x65\40\74\x73\141\x6d\154\72\101\165\164\150\156\x53\164\141\x74\x65\155\x65\x6e\x74\76\x20\x69\156\x20\x3c\x73\141\155\154\72\101\x73\163\145\162\164\151\x6f\156\x3e\40\156\157\164\40\x73\x75\x70\160\157\x72\164\145\144\56");
        Td:
        $At = $O7[0];
        if ($At->hasAttribute("\x41\165\x74\150\156\x49\x6e\x73\x74\x61\x6e\x74")) {
            goto Wg;
        }
        throw new Exception("\115\151\163\x73\151\x6e\x67\x20\162\145\161\x75\x69\162\x65\x64\40\x41\165\x74\x68\x6e\x49\156\163\x74\141\x6e\x74\40\x61\164\x74\162\x69\x62\x75\164\145\40\x6f\156\x20\74\163\141\x6d\154\72\101\165\164\x68\156\123\x74\x61\164\x65\155\145\156\164\x3e\x2e");
        Wg:
        $this->authnInstant = SAMLUtilities::xsDateTimeToTimestamp($At->getAttribute("\x41\165\164\x68\156\x49\156\x73\x74\x61\156\164"));
        if (!$At->hasAttribute("\x53\x65\163\163\151\x6f\156\x4e\x6f\x74\117\x6e\117\x72\101\146\164\x65\x72")) {
            goto Zf;
        }
        $this->sessionNotOnOrAfter = SAMLUtilities::xsDateTimeToTimestamp($At->getAttribute("\123\145\163\x73\151\x6f\156\116\x6f\x74\x4f\x6e\x4f\162\x41\x66\x74\145\162"));
        Zf:
        if (!$At->hasAttribute("\x53\145\x73\163\151\x6f\156\x49\x6e\x64\x65\170")) {
            goto Fp;
        }
        $this->sessionIndex = $At->getAttribute("\x53\x65\x73\163\x69\157\156\x49\x6e\x64\145\170");
        Fp:
        $this->parseAuthnContext($At);
    }
    private function parseAuthnContext(\DOMElement $Ps)
    {
        $HB = SAMLUtilities::xpQuery($Ps, "\56\x2f\x73\x61\155\154\137\x61\163\x73\x65\x72\164\x69\157\156\72\x41\x75\164\150\x6e\103\x6f\x6e\164\145\x78\164");
        if (count($HB) > 1) {
            goto cC;
        }
        if (empty($HB)) {
            goto ok;
        }
        goto yh;
        cC:
        throw new Exception("\x4d\157\x72\x65\x20\x74\x68\x61\x6e\40\x6f\156\x65\40\x3c\163\141\x6d\x6c\x3a\101\x75\x74\x68\x6e\103\x6f\156\x74\145\x78\164\x3e\40\151\x6e\40\74\x73\141\x6d\154\x3a\101\x75\164\150\x6e\x53\x74\x61\164\145\155\x65\x6e\164\x3e\x2e");
        goto yh;
        ok:
        throw new Exception("\x4d\x69\163\x73\x69\156\147\x20\x72\x65\161\165\151\162\145\144\x20\x3c\x73\141\155\x6c\72\101\x75\x74\150\x6e\103\157\156\164\x65\170\x74\x3e\x20\151\x6e\x20\x3c\x73\141\x6d\x6c\72\x41\x75\x74\x68\156\123\164\x61\x74\145\x6d\x65\x6e\x74\76\56");
        yh:
        $xe = $HB[0];
        $Ul = SAMLUtilities::xpQuery($xe, "\x2e\57\x73\x61\x6d\154\x5f\141\x73\163\145\x72\x74\x69\157\x6e\x3a\x41\x75\x74\150\x6e\103\157\156\164\145\170\x74\104\145\143\154\x52\145\x66");
        if (count($Ul) > 1) {
            goto GQ;
        }
        if (count($Ul) === 1) {
            goto qo;
        }
        goto Sv;
        GQ:
        throw new Exception("\x4d\157\x72\x65\x20\164\x68\x61\156\40\157\156\x65\40\x3c\x73\x61\155\x6c\72\x41\165\164\x68\x6e\103\157\x6e\164\x65\170\164\x44\145\x63\154\x52\x65\x66\x3e\x20\146\157\165\156\144\77");
        goto Sv;
        qo:
        $this->setAuthnContextDeclRef(trim($Ul[0]->textContent));
        Sv:
        $t6 = SAMLUtilities::xpQuery($xe, "\56\x2f\163\141\x6d\x6c\137\141\163\x73\145\162\164\151\x6f\156\x3a\101\165\164\150\x6e\103\x6f\x6e\x74\x65\170\x74\104\145\x63\154");
        if (count($t6) > 1) {
            goto My;
        }
        if (count($t6) === 1) {
            goto Dq;
        }
        goto X1;
        My:
        throw new Exception("\115\157\162\145\x20\164\150\x61\156\x20\157\x6e\145\x20\x3c\163\141\x6d\x6c\72\x41\x75\x74\150\x6e\103\x6f\x6e\164\145\x78\x74\x44\145\x63\154\x3e\40\x66\x6f\165\x6e\x64\x3f");
        goto X1;
        Dq:
        $this->setAuthnContextDecl(new SAML2_XML_Chunk($t6[0]));
        X1:
        $Et = SAMLUtilities::xpQuery($xe, "\x2e\x2f\x73\141\x6d\x6c\137\141\163\x73\x65\162\x74\x69\x6f\x6e\x3a\101\x75\164\150\x6e\103\x6f\x6e\164\145\x78\x74\x43\x6c\141\163\x73\x52\145\x66");
        if (count($Et) > 1) {
            goto Oq;
        }
        if (count($Et) === 1) {
            goto yC;
        }
        goto UV;
        Oq:
        throw new Exception("\115\x6f\162\x65\40\164\x68\x61\x6e\x20\x6f\156\145\40\x3c\163\x61\x6d\154\x3a\101\x75\x74\150\x6e\x43\157\x6e\x74\x65\x78\x74\103\x6c\x61\163\x73\x52\x65\146\x3e\x20\151\x6e\40\74\163\x61\155\x6c\72\x41\x75\164\150\x6e\x43\157\x6e\x74\145\x78\164\76\56");
        goto UV;
        yC:
        $this->setAuthnContextClassRef(trim($Et[0]->textContent));
        UV:
        if (!(empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef))) {
            goto Ze;
        }
        throw new Exception("\x4d\x69\x73\x73\151\x6e\x67\40\x65\x69\x74\x68\145\162\40\74\163\141\x6d\x6c\x3a\101\165\x74\150\x6e\103\x6f\x6e\164\145\x78\x74\x43\154\141\163\163\x52\x65\x66\76\x20\x6f\x72\40\x3c\x73\141\155\154\72\101\165\164\150\x6e\x43\x6f\156\x74\x65\x78\164\104\145\143\x6c\122\145\x66\76\40\x6f\x72\x20\74\163\141\x6d\154\x3a\x41\x75\164\150\x6e\103\157\156\x74\x65\170\x74\104\x65\x63\x6c\x3e");
        Ze:
        $this->AuthenticatingAuthority = SAMLUtilities::extractStrings($xe, "\165\162\x6e\72\157\141\x73\151\x73\72\156\x61\x6d\x65\x73\72\164\x63\72\x53\101\115\114\72\62\x2e\x30\x3a\x61\x73\163\x65\x72\x74\x69\x6f\x6e", "\101\x75\164\150\x65\156\164\x69\143\x61\164\x69\x6e\x67\101\x75\164\150\157\x72\151\164\x79");
    }
    private function parseAttributes(\DOMElement $HR)
    {
        $rU = TRUE;
        $c2 = SAMLUtilities::xpQuery($HR, "\x2e\x2f\x73\x61\x6d\154\137\141\163\163\145\162\164\x69\x6f\x6e\x3a\101\164\x74\x72\x69\x62\x75\164\145\123\164\141\164\x65\x6d\x65\156\x74\x2f\163\x61\155\154\137\x61\163\x73\x65\x72\164\151\x6f\x6e\72\x41\x74\x74\162\151\x62\165\x74\x65");
        foreach ($c2 as $UR) {
            if ($UR->hasAttribute("\x4e\x61\x6d\145")) {
                goto dh;
            }
            throw new Exception("\115\151\x73\x73\x69\x6e\x67\40\156\x61\x6d\145\40\157\x6e\x20\74\x73\x61\x6d\x6c\72\x41\164\164\x72\x69\x62\165\164\x65\x3e\40\145\x6c\x65\155\x65\x6e\164\x2e");
            dh:
            $gD = $UR->getAttribute("\x4e\x61\155\145");
            if ($UR->hasAttribute("\x4e\x61\x6d\x65\106\157\x72\x6d\x61\x74")) {
                goto HM;
            }
            $nA = "\165\162\156\72\x6f\x61\x73\151\x73\72\x6e\x61\x6d\145\163\x3a\164\x63\72\x53\x41\115\x4c\x3a\x31\56\x31\x3a\156\x61\x6d\x65\x69\x64\x2d\146\157\x72\x6d\141\x74\x3a\x75\x6e\163\x70\145\143\151\146\x69\145\x64";
            goto Ew;
            HM:
            $nA = $UR->getAttribute("\116\141\x6d\x65\106\157\x72\x6d\141\x74");
            Ew:
            if ($rU) {
                goto AI;
            }
            if (!($this->nameFormat !== $nA)) {
                goto Nb;
            }
            $this->nameFormat = "\x75\x72\156\x3a\x6f\141\163\151\163\x3a\156\x61\155\x65\x73\x3a\164\x63\72\123\101\x4d\x4c\x3a\61\56\61\72\156\141\155\x65\151\x64\55\x66\157\162\x6d\141\x74\72\165\x6e\163\x70\x65\x63\x69\146\151\x65\144";
            Nb:
            goto tN;
            AI:
            $this->nameFormat = $nA;
            $rU = FALSE;
            tN:
            if (array_key_exists($gD, $this->attributes)) {
                goto Tm;
            }
            $this->attributes[$gD] = array();
            Tm:
            $nE = SAMLUtilities::xpQuery($UR, "\56\57\x73\x61\155\x6c\137\141\x73\163\145\x72\x74\151\157\156\x3a\101\x74\x74\x72\151\142\x75\164\145\x56\x61\x6c\x75\145");
            foreach ($nE as $yY) {
                $this->attributes[$gD][] = trim($yY->textContent);
                Bl:
            }
            DB:
            IX:
        }
        Zn:
    }
    private function parseEncryptedAttributes(\DOMElement $HR)
    {
        $this->encryptedAttribute = SAMLUtilities::xpQuery($HR, "\x2e\57\163\x61\155\154\137\x61\163\163\x65\x72\164\151\157\x6e\x3a\x41\x74\164\162\151\142\165\164\x65\123\x74\x61\164\145\x6d\145\156\164\x2f\163\141\x6d\154\x5f\141\163\x73\145\x72\164\x69\157\x6e\72\x45\156\x63\x72\171\x70\x74\145\144\101\x74\164\x72\x69\142\165\x74\x65");
    }
    private function parseSignature(\DOMElement $HR)
    {
        $fR = SAMLUtilities::validateElement($HR);
        if (!($fR !== FALSE)) {
            goto F9;
        }
        $this->wasSignedAtConstruction = TRUE;
        $this->certificates = $fR["\103\145\x72\x74\151\x66\x69\143\141\164\x65\x73"];
        $this->signatureData = $fR;
        F9:
    }
    public function validate(XMLSecurityKey $w5)
    {
        if (!($this->signatureData === NULL)) {
            goto Fs;
        }
        return FALSE;
        Fs:
        SAMLUtilities::validateSignature($this->signatureData, $w5);
        return TRUE;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($Q4)
    {
        $this->id = $Q4;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($y1)
    {
        $this->issueInstant = $y1;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($Na)
    {
        $this->issuer = $Na;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto zb;
        }
        throw new Exception("\101\164\x74\145\x6d\x70\164\x65\x64\40\x74\x6f\40\162\x65\164\162\x69\145\166\145\x20\145\x6e\143\x72\x79\160\164\145\144\40\x4e\141\155\x65\111\x44\40\x77\151\164\150\157\x75\x74\x20\144\x65\143\x72\171\x70\x74\151\156\x67\40\151\x74\40\x66\x69\162\x73\164\56");
        zb:
        return $this->nameId;
    }
    public function setNameId($E3)
    {
        $this->nameId = $E3;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto cE;
        }
        return TRUE;
        cE:
        return FALSE;
    }
    public function decryptNameId(XMLSecurityKey $w5, array $kZ = array())
    {
        if (!($this->encryptedNameId === NULL)) {
            goto J9;
        }
        return;
        J9:
        $E3 = SAMLUtilities::decryptElement($this->encryptedNameId, $w5, $kZ);
        SAMLUtilities::getContainer()->debugMessage($E3, "\144\145\143\x72\x79\160\164");
        $this->nameId = SAMLUtilities::parseNameId($E3);
        $this->encryptedNameId = NULL;
    }
    public function decryptAttributes(XMLSecurityKey $w5, array $kZ = array())
    {
        if (!($this->encryptedAttribute === NULL)) {
            goto Nf;
        }
        return;
        Nf:
        $rU = TRUE;
        $c2 = $this->encryptedAttribute;
        foreach ($c2 as $PJ) {
            $UR = SAMLUtilities::decryptElement($PJ->getElementsByTagName("\105\156\143\162\x79\160\164\145\144\x44\x61\x74\141")->item(0), $w5, $kZ);
            if ($UR->hasAttribute("\116\x61\155\145")) {
                goto Um;
            }
            throw new Exception("\115\x69\x73\163\x69\156\x67\x20\156\x61\155\x65\40\157\x6e\x20\74\x73\141\x6d\x6c\72\x41\164\164\162\151\x62\165\x74\145\x3e\40\x65\x6c\145\155\x65\156\164\x2e");
            Um:
            $gD = $UR->getAttribute("\116\141\x6d\145");
            if ($UR->hasAttribute("\x4e\x61\155\145\x46\157\162\155\141\164")) {
                goto wL;
            }
            $nA = "\x75\162\156\x3a\x6f\x61\163\x69\x73\x3a\156\141\x6d\145\163\72\164\x63\x3a\123\101\x4d\x4c\x3a\x32\56\60\72\141\164\164\162\156\141\x6d\x65\55\146\x6f\162\155\141\x74\72\165\156\x73\x70\145\x63\x69\146\x69\x65\x64";
            goto ma;
            wL:
            $nA = $UR->getAttribute("\116\141\x6d\x65\106\x6f\x72\x6d\x61\x74");
            ma:
            if ($rU) {
                goto Y4;
            }
            if (!($this->nameFormat !== $nA)) {
                goto S3;
            }
            $this->nameFormat = "\165\162\x6e\72\x6f\141\163\x69\x73\x3a\x6e\141\155\x65\x73\x3a\164\143\x3a\x53\101\x4d\x4c\72\x32\56\x30\72\141\164\x74\x72\x6e\141\x6d\145\55\146\157\162\x6d\x61\164\72\x75\156\x73\x70\145\143\x69\x66\x69\145\144";
            S3:
            goto OI;
            Y4:
            $this->nameFormat = $nA;
            $rU = FALSE;
            OI:
            if (array_key_exists($gD, $this->attributes)) {
                goto D7;
            }
            $this->attributes[$gD] = array();
            D7:
            $nE = SAMLUtilities::xpQuery($UR, "\x2e\x2f\163\x61\x6d\154\x5f\141\163\163\145\162\x74\151\157\156\x3a\101\164\x74\162\x69\x62\165\x74\x65\126\141\154\165\x65");
            foreach ($nE as $yY) {
                $this->attributes[$gD][] = trim($yY->textContent);
                Ff:
            }
            aj:
            Jx:
        }
        T7:
    }
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    public function setNotBefore($Sq)
    {
        $this->notBefore = $Sq;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($SD)
    {
        $this->notOnOrAfter = $SD;
    }
    public function setEncryptedAttributes($nV)
    {
        $this->requiredEncAttributes = $nV;
    }
    public function getValidAudiences()
    {
        return $this->validAudiences;
    }
    public function setValidAudiences(array $re = NULL)
    {
        $this->validAudiences = $re;
    }
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }
    public function setAuthnInstant($pr)
    {
        $this->authnInstant = $pr;
    }
    public function getSessionNotOnOrAfter()
    {
        return $this->sessionNotOnOrAfter;
    }
    public function setSessionNotOnOrAfter($GM)
    {
        $this->sessionNotOnOrAfter = $GM;
    }
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }
    public function setSessionIndex($uQ)
    {
        $this->sessionIndex = $uQ;
    }
    public function getAuthnContext()
    {
        if (empty($this->authnContextClassRef)) {
            goto Og;
        }
        return $this->authnContextClassRef;
        Og:
        if (empty($this->authnContextDeclRef)) {
            goto d2;
        }
        return $this->authnContextDeclRef;
        d2:
        return NULL;
    }
    public function setAuthnContext($uM)
    {
        $this->setAuthnContextClassRef($uM);
    }
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
    public function setAuthnContextClassRef($Dt)
    {
        $this->authnContextClassRef = $Dt;
    }
    public function setAuthnContextDecl(SAML2_XML_Chunk $Ph)
    {
        if (empty($this->authnContextDeclRef)) {
            goto CT;
        }
        throw new Exception("\101\x75\164\x68\156\103\x6f\156\x74\x65\x78\x74\104\145\x63\154\x52\145\146\x20\x69\x73\40\x61\x6c\162\x65\141\144\x79\40\x72\x65\x67\151\x73\x74\x65\x72\145\x64\41\40\x4d\x61\171\40\157\x6e\154\x79\x20\x68\x61\166\145\40\x65\151\164\x68\145\162\40\141\40\104\x65\x63\x6c\x20\157\x72\x20\x61\x20\104\x65\143\154\x52\145\146\54\x20\x6e\157\x74\x20\x62\157\x74\x68\x21");
        CT:
        $this->authnContextDecl = $Ph;
    }
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }
    public function setAuthnContextDeclRef($a3)
    {
        if (empty($this->authnContextDecl)) {
            goto VD;
        }
        throw new Exception("\x41\165\x74\150\156\103\x6f\x6e\164\145\x78\164\104\145\143\154\x20\x69\163\40\141\x6c\162\145\141\144\171\x20\x72\x65\147\151\x73\x74\x65\162\x65\144\41\x20\115\x61\171\40\x6f\x6e\x6c\171\40\150\141\x76\x65\40\x65\x69\164\150\x65\x72\40\x61\x20\104\x65\143\x6c\40\157\162\x20\141\40\104\145\x63\154\x52\x65\x66\54\x20\156\157\164\x20\x62\x6f\x74\x68\41");
        VD:
        $this->authnContextDeclRef = $a3;
    }
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }
    public function getAuthenticatingAuthority()
    {
        return $this->AuthenticatingAuthority;
    }
    public function setAuthenticatingAuthority($nH)
    {
        $this->AuthenticatingAuthority = $nH;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes(array $c2)
    {
        $this->attributes = $c2;
    }
    public function getAttributeNameFormat()
    {
        return $this->nameFormat;
    }
    public function setAttributeNameFormat($nA)
    {
        $this->nameFormat = $nA;
    }
    public function getSubjectConfirmation()
    {
        return $this->SubjectConfirmation;
    }
    public function setSubjectConfirmation(array $qM)
    {
        $this->SubjectConfirmation = $qM;
    }
    public function getSignatureKey()
    {
        return $this->signatureKey;
    }
    public function setSignatureKey(XMLsecurityKey $xI = NULL)
    {
        $this->signatureKey = $xI;
    }
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
    public function setEncryptionKey(XMLSecurityKey $Sp = NULL)
    {
        $this->encryptionKey = $Sp;
    }
    public function setCertificates(array $Ro)
    {
        $this->certificates = $Ro;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function getWasSignedAtConstruction()
    {
        return $this->wasSignedAtConstruction;
    }
}
