<?php


namespace MiniOrange\Helper\Lib\XMLSecLibs;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
class XMLSecEnc
{
    const template = "\74\x78\x65\x6e\143\72\105\x6e\143\162\171\160\164\x65\144\x44\x61\164\x61\40\x78\155\154\156\163\72\x78\x65\x6e\x63\x3d\47\x68\164\x74\x70\72\57\57\167\x77\x77\56\x77\x33\x2e\x6f\162\x67\x2f\62\x30\60\61\x2f\x30\x34\x2f\170\x6d\x6c\145\156\x63\43\x27\76\xa\40\x20\x20\74\x78\x65\x6e\143\x3a\103\x69\x70\150\145\x72\x44\141\164\x61\76\xa\x20\40\40\40\40\40\74\x78\x65\x6e\143\72\103\151\160\150\x65\162\x56\x61\154\x75\x65\x3e\74\x2f\x78\x65\x6e\x63\x3a\x43\151\x70\x68\x65\x72\x56\x61\154\165\x65\x3e\12\x20\40\x20\74\x2f\x78\145\156\143\x3a\x43\x69\160\x68\x65\x72\x44\x61\x74\141\x3e\xa\74\57\x78\x65\x6e\143\72\105\156\x63\x72\171\x70\x74\x65\x64\x44\x61\x74\x61\x3e";
    const Element = "\x68\164\x74\160\72\57\57\x77\x77\167\56\167\63\x2e\157\x72\x67\x2f\62\x30\60\x31\x2f\x30\x34\x2f\x78\155\154\145\156\x63\43\x45\154\x65\x6d\x65\x6e\164";
    const Content = "\x68\164\x74\x70\x3a\x2f\57\167\x77\x77\x2e\x77\63\56\157\x72\x67\x2f\x32\x30\x30\x31\x2f\x30\x34\57\x78\x6d\x6c\x65\156\x63\43\103\157\x6e\x74\x65\x6e\x74";
    const URI = 3;
    const XMLENCNS = "\x68\164\164\x70\72\x2f\x2f\x77\x77\x77\56\167\x33\56\157\x72\147\x2f\x32\60\60\61\57\60\64\57\x78\155\154\145\156\143\43";
    private $encdoc = null;
    private $rawNode = null;
    public $type = null;
    public $encKey = null;
    private $references = array();
    public function __construct()
    {
        $this->_resetTemplate();
    }
    private function _resetTemplate()
    {
        $this->encdoc = new DOMDocument();
        $this->encdoc->loadXML(self::template);
    }
    public function addReference($gD, $uu, $yf)
    {
        if ($uu instanceof DOMNode) {
            goto l6;
        }
        throw new Exception("\x24\156\157\x64\145\40\x69\163\40\156\x6f\164\x20\157\146\40\164\171\x70\145\40\104\117\115\116\157\x64\x65");
        l6:
        $N_ = $this->encdoc;
        $this->_resetTemplate();
        $fV = $this->encdoc;
        $this->encdoc = $N_;
        $rA = XMLSecurityDSig::generateGUID();
        $mg = $fV->documentElement;
        $mg->setAttribute("\111\144", $rA);
        $this->references[$gD] = array("\156\157\144\x65" => $uu, "\164\x79\x70\145" => $yf, "\x65\156\x63\156\157\x64\145" => $fV, "\162\x65\x66\165\162\151" => $rA);
    }
    public function setNode($uu)
    {
        $this->rawNode = $uu;
    }
    public function encryptNode($vr, $GE = true)
    {
        $L2 = '';
        if (!empty($this->rawNode)) {
            goto Vh;
        }
        throw new Exception("\116\157\x64\145\40\164\x6f\x20\x65\156\x63\x72\x79\160\164\40\150\141\163\40\156\x6f\164\40\x62\145\x65\x6e\40\163\145\164");
        Vh:
        if ($vr instanceof XMLSecurityKey) {
            goto L1;
        }
        throw new Exception("\x49\156\166\x61\x6c\x69\x64\40\x4b\x65\171");
        L1:
        $Sc = $this->rawNode->ownerDocument;
        $gX = new DOMXPath($this->encdoc);
        $at = $gX->query("\57\170\145\x6e\143\72\x45\x6e\x63\162\171\160\x74\145\144\104\x61\x74\141\x2f\170\145\x6e\x63\72\103\151\160\150\145\x72\104\141\x74\x61\57\x78\x65\156\143\x3a\x43\x69\x70\150\145\162\x56\x61\154\165\x65");
        $Ru = $at->item(0);
        if (!($Ru == null)) {
            goto Tu;
        }
        throw new Exception("\105\162\x72\x6f\x72\40\x6c\157\x63\x61\x74\x69\x6e\147\x20\103\x69\x70\150\145\162\x56\141\154\165\x65\40\x65\x6c\x65\155\145\x6e\164\40\x77\x69\164\x68\151\x6e\x20\x74\145\155\160\154\141\164\145");
        Tu:
        switch ($this->type) {
            case self::Element:
                $L2 = $Sc->saveXML($this->rawNode);
                $this->encdoc->documentElement->setAttribute("\x54\x79\x70\x65", self::Element);
                goto bd;
            case self::Content:
                $r8 = $this->rawNode->childNodes;
                foreach ($r8 as $VQ) {
                    $L2 .= $Sc->saveXML($VQ);
                    qw:
                }
                VU:
                $this->encdoc->documentElement->setAttribute("\x54\x79\160\145", self::Content);
                goto bd;
            default:
                throw new Exception("\x54\x79\x70\x65\40\151\163\40\x63\x75\x72\x72\145\x6e\164\154\171\x20\156\x6f\164\40\x73\x75\160\x70\157\162\164\x65\x64");
        }
        Dm:
        bd:
        $L3 = $this->encdoc->documentElement->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\143\72\x45\156\143\162\171\160\164\151\x6f\x6e\x4d\145\x74\x68\x6f\x64"));
        $L3->setAttribute("\x41\x6c\147\157\x72\151\164\150\x6d", $vr->getAlgorithm());
        $Ru->parentNode->parentNode->insertBefore($L3, $Ru->parentNode->parentNode->firstChild);
        $J2 = base64_encode($vr->encryptData($L2));
        $yY = $this->encdoc->createTextNode($J2);
        $Ru->appendChild($yY);
        if ($GE) {
            goto ZU;
        }
        return $this->encdoc->documentElement;
        goto k0;
        ZU:
        switch ($this->type) {
            case self::Element:
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto SQ;
                }
                return $this->encdoc;
                SQ:
                $Jp = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                $this->rawNode->parentNode->replaceChild($Jp, $this->rawNode);
                return $Jp;
            case self::Content:
                $Jp = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                Po:
                if (!$this->rawNode->firstChild) {
                    goto GO;
                }
                $this->rawNode->removeChild($this->rawNode->firstChild);
                goto Po;
                GO:
                $this->rawNode->appendChild($Jp);
                return $Jp;
        }
        q0:
        H5:
        k0:
    }
    public function encryptReferences($vr)
    {
        $tz = $this->rawNode;
        $Mi = $this->type;
        foreach ($this->references as $gD => $ig) {
            $this->encdoc = $ig["\145\x6e\x63\x6e\x6f\144\x65"];
            $this->rawNode = $ig["\156\x6f\x64\x65"];
            $this->type = $ig["\164\171\160\x65"];
            try {
                $Hj = $this->encryptNode($vr);
                $this->references[$gD]["\145\156\x63\156\x6f\x64\145"] = $Hj;
            } catch (Exception $YR) {
                $this->rawNode = $tz;
                $this->type = $Mi;
                throw $YR;
            }
            ri:
        }
        j1:
        $this->rawNode = $tz;
        $this->type = $Mi;
    }
    public function getCipherValue()
    {
        if (!empty($this->rawNode)) {
            goto rP;
        }
        throw new Exception("\116\x6f\144\x65\x20\164\157\40\144\x65\143\x72\x79\160\164\x20\x68\141\x73\40\156\157\164\x20\142\145\x65\x6e\x20\x73\x65\164");
        rP:
        $Sc = $this->rawNode->ownerDocument;
        $gX = new DOMXPath($Sc);
        $gX->registerNamespace("\x78\155\154\145\x6e\x63\162", self::XMLENCNS);
        $Jx = "\x2e\x2f\x78\155\x6c\145\156\143\x72\72\103\151\160\x68\145\x72\x44\x61\164\x61\x2f\x78\x6d\x6c\145\x6e\x63\162\72\103\151\x70\150\145\162\126\x61\x6c\165\x65";
        $wP = $gX->query($Jx, $this->rawNode);
        $uu = $wP->item(0);
        if ($uu) {
            goto ln;
        }
        return null;
        ln:
        return base64_decode($uu->nodeValue);
    }
    public function decryptNode($vr, $GE = true)
    {
        if ($vr instanceof XMLSecurityKey) {
            goto VO;
        }
        throw new Exception("\111\156\x76\141\x6c\x69\144\x20\x4b\145\171");
        VO:
        $FN = $this->getCipherValue();
        if ($FN) {
            goto Va;
        }
        throw new Exception("\x43\141\156\156\157\x74\x20\154\x6f\143\141\164\145\x20\x65\x6e\x63\x72\171\x70\164\145\144\x20\144\x61\164\141");
        goto xI;
        Va:
        $G7 = $vr->decryptData($FN);
        if ($GE) {
            goto pk;
        }
        return $G7;
        goto Lf;
        pk:
        switch ($this->type) {
            case self::Element:
                $x_ = new DOMDocument();
                $x_->loadXML($G7);
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto SU;
                }
                return $x_;
                SU:
                $Jp = $this->rawNode->ownerDocument->importNode($x_->documentElement, true);
                $this->rawNode->parentNode->replaceChild($Jp, $this->rawNode);
                return $Jp;
            case self::Content:
                if ($this->rawNode->nodeType == XML_DOCUMENT_NODE) {
                    goto mT;
                }
                $Sc = $this->rawNode->ownerDocument;
                goto iO;
                mT:
                $Sc = $this->rawNode;
                iO:
                $Sn = $Sc->createDocumentFragment();
                $Sn->appendXML($G7);
                $ak = $this->rawNode->parentNode;
                $ak->replaceChild($Sn, $this->rawNode);
                return $ak;
            default:
                return $G7;
        }
        vO:
        nN:
        Lf:
        xI:
    }
    public function encryptKey($cZ, $T1, $MK = true)
    {
        if (!(!$cZ instanceof XMLSecurityKey || !$T1 instanceof XMLSecurityKey)) {
            goto ag;
        }
        throw new Exception("\x49\156\166\x61\x6c\x69\144\x20\113\x65\x79");
        ag:
        $Ho = base64_encode($cZ->encryptData($T1->key));
        $SM = $this->encdoc->documentElement;
        $fz = $this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\x63\72\x45\156\143\x72\171\160\164\x65\144\x4b\145\x79");
        if ($MK) {
            goto cs;
        }
        $this->encKey = $fz;
        goto Ys;
        cs:
        $s2 = $SM->insertBefore($this->encdoc->createElementNS("\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\167\63\56\x6f\x72\x67\x2f\62\x30\x30\60\57\x30\x39\x2f\x78\155\154\x64\x73\151\x67\43", "\144\x73\x69\147\72\113\x65\x79\x49\156\146\x6f"), $SM->firstChild);
        $s2->appendChild($fz);
        Ys:
        $L3 = $fz->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\x63\72\105\x6e\143\162\x79\x70\x74\151\x6f\x6e\115\145\164\150\157\144"));
        $L3->setAttribute("\x41\154\x67\157\x72\x69\x74\x68\x6d", $cZ->getAlgorith());
        if (empty($cZ->name)) {
            goto MW;
        }
        $s2 = $fz->appendChild($this->encdoc->createElementNS("\x68\x74\x74\160\72\57\x2f\167\x77\167\56\x77\63\56\157\162\147\57\x32\60\x30\x30\x2f\60\x39\x2f\170\155\x6c\144\x73\151\x67\x23", "\144\163\x69\147\72\113\145\171\111\x6e\146\157"));
        $s2->appendChild($this->encdoc->createElementNS("\x68\x74\164\160\x3a\x2f\57\x77\167\x77\x2e\167\63\56\157\162\147\57\62\x30\60\x30\57\x30\71\x2f\x78\x6d\154\x64\163\151\x67\x23", "\x64\x73\151\x67\x3a\113\x65\x79\x4e\141\155\x65", $cZ->name));
        MW:
        $Bn = $fz->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\143\72\103\x69\x70\150\x65\x72\x44\141\164\141"));
        $Bn->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\156\x63\x3a\x43\x69\x70\x68\145\162\126\141\x6c\165\x65", $Ho));
        if (!(is_array($this->references) && count($this->references) > 0)) {
            goto Vn;
        }
        $C9 = $fz->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\143\x3a\x52\x65\x66\145\x72\x65\x6e\143\145\x4c\x69\x73\164"));
        foreach ($this->references as $gD => $ig) {
            $rA = $ig["\x72\x65\146\165\162\151"];
            $e0 = $C9->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\156\x63\x3a\104\x61\x74\141\122\x65\x66\145\162\145\x6e\143\145"));
            $e0->setAttribute("\125\x52\x49", "\43" . $rA);
            Od:
        }
        bD:
        Vn:
        return;
    }
    public function decryptKey($fz)
    {
        if ($fz->isEncrypted) {
            goto mW;
        }
        throw new Exception("\113\x65\x79\40\x69\x73\40\x6e\157\164\x20\x45\x6e\x63\x72\171\x70\x74\x65\x64");
        mW:
        if (!empty($fz->key)) {
            goto fJ;
        }
        throw new Exception("\x4b\145\x79\40\x69\163\40\155\x69\163\x73\151\156\x67\40\x64\141\x74\141\40\x74\x6f\x20\160\145\x72\x66\157\162\x6d\x20\164\150\145\x20\x64\x65\x63\x72\171\x70\x74\x69\157\156");
        fJ:
        return $this->decryptNode($fz, false);
    }
    public function locateEncryptedData($mg)
    {
        if ($mg instanceof DOMDocument) {
            goto Mv;
        }
        $Sc = $mg->ownerDocument;
        goto I1;
        Mv:
        $Sc = $mg;
        I1:
        if (!$Sc) {
            goto KN;
        }
        $gg = new DOMXPath($Sc);
        $Jx = "\57\57\x2a\x5b\x6c\x6f\143\x61\154\55\x6e\x61\x6d\x65\50\x29\x3d\47\105\156\143\162\x79\x70\164\x65\x64\x44\141\164\x61\47\x20\141\156\x64\x20\156\x61\155\x65\163\160\x61\x63\145\x2d\x75\x72\x69\x28\51\x3d\x27" . self::XMLENCNS . "\47\135";
        $wP = $gg->query($Jx);
        return $wP->item(0);
        KN:
        return null;
    }
    public function locateKey($uu = null)
    {
        if (!empty($uu)) {
            goto Ak;
        }
        $uu = $this->rawNode;
        Ak:
        if ($uu instanceof DOMNode) {
            goto Pp;
        }
        return null;
        Pp:
        if (!($Sc = $uu->ownerDocument)) {
            goto by;
        }
        $gg = new DOMXPath($Sc);
        $gg->registerNamespace("\x78\155\154\163\145\143\x65\156\143", self::XMLENCNS);
        $Jx = "\x2e\x2f\x2f\170\155\154\163\145\x63\145\156\x63\72\x45\156\x63\162\x79\x70\x74\x69\157\156\x4d\x65\x74\x68\157\x64";
        $wP = $gg->query($Jx, $uu);
        if (!($i_ = $wP->item(0))) {
            goto m1;
        }
        $xS = $i_->getAttribute("\x41\x6c\147\x6f\162\151\x74\x68\x6d");
        try {
            $vr = new XMLSecurityKey($xS, array("\x74\x79\160\x65" => "\160\162\151\166\x61\164\145"));
        } catch (Exception $YR) {
            return null;
        }
        return $vr;
        m1:
        by:
        return null;
    }
    public static function staticLocateKeyInfo($le = null, $uu = null)
    {
        if (!(empty($uu) || !$uu instanceof DOMNode)) {
            goto xa;
        }
        return null;
        xa:
        $Sc = $uu->ownerDocument;
        if ($Sc) {
            goto Af;
        }
        return null;
        Af:
        $gg = new DOMXPath($Sc);
        $gg->registerNamespace("\170\155\x6c\163\145\143\145\x6e\x63", self::XMLENCNS);
        $gg->registerNamespace("\x78\x6d\x6c\163\x65\x63\144\163\151\147", XMLSecurityDSig::XMLDSIGNS);
        $Jx = "\56\57\x78\x6d\154\x73\145\143\144\x73\151\x67\72\x4b\145\171\111\x6e\x66\157";
        $wP = $gg->query($Jx, $uu);
        $i_ = $wP->item(0);
        if ($i_) {
            goto Bq;
        }
        return $le;
        Bq:
        foreach ($i_->childNodes as $VQ) {
            switch ($VQ->localName) {
                case "\x4b\145\171\x4e\x61\x6d\x65":
                    if (empty($le)) {
                        goto iQ;
                    }
                    $le->name = $VQ->nodeValue;
                    iQ:
                    goto JY;
                case "\x4b\x65\171\x56\141\x6c\x75\145":
                    foreach ($VQ->childNodes as $Yy) {
                        switch ($Yy->localName) {
                            case "\x44\123\101\113\x65\171\x56\x61\154\165\x65":
                                throw new Exception("\104\x53\x41\x4b\x65\171\126\141\154\x75\145\40\x63\x75\x72\162\145\156\164\x6c\171\x20\156\x6f\164\40\x73\x75\x70\160\157\x72\x74\145\144");
                            case "\x52\x53\x41\x4b\145\x79\126\141\x6c\165\x65":
                                $nr = null;
                                $rL = null;
                                if (!($Zq = $Yy->getElementsByTagName("\x4d\157\144\x75\x6c\x75\163")->item(0))) {
                                    goto OM;
                                }
                                $nr = base64_decode($Zq->nodeValue);
                                OM:
                                if (!($e2 = $Yy->getElementsByTagName("\105\x78\x70\x6f\156\145\156\164")->item(0))) {
                                    goto eC;
                                }
                                $rL = base64_decode($e2->nodeValue);
                                eC:
                                if (!(empty($nr) || empty($rL))) {
                                    goto my;
                                }
                                throw new Exception("\115\x69\163\x73\151\156\147\40\x4d\157\144\x75\154\165\x73\40\157\162\x20\105\170\160\x6f\x6e\145\156\x74");
                                my:
                                $dp = XMLSecurityKey::convertRSA($nr, $rL);
                                $le->loadKey($dp);
                                goto e0;
                        }
                        lm:
                        e0:
                        fZ:
                    }
                    s5:
                    goto JY;
                case "\x52\145\x74\x72\x69\145\x76\141\x6c\115\145\x74\x68\157\x64":
                    $yf = $VQ->getAttribute("\124\171\x70\145");
                    if (!($yf !== "\x68\x74\x74\160\72\57\x2f\x77\x77\x77\x2e\167\63\56\157\x72\147\57\x32\x30\60\x31\x2f\60\64\57\170\x6d\154\145\x6e\x63\43\105\x6e\x63\162\x79\x70\164\145\x64\x4b\x65\x79")) {
                        goto lG;
                    }
                    goto JY;
                    lG:
                    $lN = $VQ->getAttribute("\x55\122\111");
                    if (!($lN[0] !== "\x23")) {
                        goto Io;
                    }
                    goto JY;
                    Io:
                    $Q4 = substr($lN, 1);
                    $Jx = "\x2f\57\x78\155\154\163\145\143\x65\x6e\143\x3a\105\156\x63\162\x79\160\x74\145\x64\113\145\171\x5b\x40\111\144\75\47{$Q4}\x27\x5d";
                    $AO = $gg->query($Jx)->item(0);
                    if ($AO) {
                        goto te;
                    }
                    throw new Exception("\125\x6e\141\x62\154\x65\40\x74\x6f\x20\x6c\157\x63\141\x74\145\x20\x45\156\x63\162\171\x70\x74\145\x64\x4b\145\x79\x20\167\151\x74\150\40\100\111\144\75\47{$Q4}\47\56");
                    te:
                    return XMLSecurityKey::fromEncryptedKeyElement($AO);
                case "\105\156\x63\x72\x79\x70\164\145\144\113\x65\x79":
                    return XMLSecurityKey::fromEncryptedKeyElement($VQ);
                case "\130\x35\x30\71\104\141\x74\141":
                    if (!($eq = $VQ->getElementsByTagName("\130\x35\x30\x39\x43\145\x72\164\151\146\x69\143\141\164\x65"))) {
                        goto V9;
                    }
                    if (!($eq->length > 0)) {
                        goto bc;
                    }
                    $aB = $eq->item(0)->textContent;
                    $aB = str_replace(array("\15", "\xa", "\40"), '', $aB);
                    $aB = "\55\x2d\x2d\55\55\102\105\x47\x49\x4e\x20\x43\105\x52\x54\111\x46\111\103\101\124\x45\55\55\x2d\x2d\x2d\xa" . chunk_split($aB, 64, "\12") . "\55\55\55\x2d\x2d\105\116\x44\x20\x43\105\x52\124\x49\106\111\103\101\x54\x45\55\x2d\55\55\x2d\xa";
                    $le->loadKey($aB, false, true);
                    bc:
                    V9:
                    goto JY;
            }
            Fg:
            JY:
            gA:
        }
        Y_:
        return $le;
    }
    public function locateKeyInfo($le = null, $uu = null)
    {
        if (!empty($uu)) {
            goto zv;
        }
        $uu = $this->rawNode;
        zv:
        return self::staticLocateKeyInfo($le, $uu);
    }
}
