<?php


namespace MiniOrange\Helper\Lib\XMLSecLibs;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
class XMLSecurityDSig
{
    const XMLDSIGNS = "\150\x74\164\160\x3a\x2f\57\x77\167\x77\x2e\167\x33\x2e\x6f\162\x67\x2f\x32\x30\x30\x30\57\60\71\57\x78\x6d\154\x64\163\151\x67\43";
    const SHA1 = "\x68\x74\x74\x70\x3a\57\57\x77\167\167\56\167\x33\56\157\x72\147\57\x32\x30\60\x30\57\60\x39\57\x78\x6d\x6c\144\163\151\147\43\x73\150\x61\x31";
    const SHA256 = "\x68\x74\164\x70\x3a\57\x2f\167\167\x77\x2e\x77\x33\56\157\162\x67\x2f\62\x30\60\61\57\x30\64\57\170\x6d\x6c\145\156\x63\x23\163\x68\141\x32\x35\66";
    const SHA384 = "\x68\164\164\x70\x3a\x2f\57\x77\x77\x77\56\167\63\x2e\157\x72\x67\x2f\62\60\60\61\x2f\x30\64\x2f\170\x6d\154\x64\x73\151\147\55\155\x6f\162\145\x23\163\150\141\x33\x38\x34";
    const SHA512 = "\x68\x74\x74\160\x3a\x2f\57\167\x77\x77\56\167\63\56\x6f\162\147\57\62\60\x30\x31\x2f\x30\64\57\x78\155\154\145\x6e\x63\43\163\x68\141\x35\61\x32";
    const RIPEMD160 = "\x68\164\x74\x70\72\x2f\x2f\x77\167\167\x2e\167\63\56\157\162\x67\x2f\62\x30\x30\x31\57\x30\x34\x2f\x78\x6d\x6c\145\x6e\143\43\162\151\x70\145\155\x64\61\66\60";
    const C14N = "\x68\x74\x74\x70\72\57\57\167\167\167\56\x77\63\56\x6f\162\147\x2f\124\x52\57\62\60\60\x31\x2f\122\x45\x43\55\x78\155\154\x2d\x63\61\x34\x6e\x2d\62\x30\60\61\x30\63\x31\x35";
    const C14N_COMMENTS = "\150\164\x74\160\72\57\x2f\x77\167\167\x2e\167\x33\x2e\x6f\162\147\57\124\122\x2f\62\x30\x30\x31\57\x52\x45\x43\x2d\170\155\154\x2d\143\61\64\x6e\x2d\62\60\x30\61\60\63\61\x35\43\127\151\164\150\x43\157\155\x6d\145\156\164\x73";
    const EXC_C14N = "\150\x74\164\160\72\57\57\167\x77\x77\56\167\63\56\x6f\x72\x67\57\62\x30\60\61\57\x31\60\x2f\170\155\x6c\55\145\170\x63\x2d\143\61\64\x6e\43";
    const EXC_C14N_COMMENTS = "\150\164\164\x70\72\57\x2f\x77\x77\167\56\167\x33\56\157\162\147\57\62\x30\60\x31\x2f\61\x30\57\x78\155\x6c\x2d\x65\170\143\x2d\143\x31\64\156\x23\x57\x69\164\150\x43\x6f\155\x6d\x65\x6e\164\163";
    const template = "\x3c\x64\163\72\x53\x69\x67\x6e\141\x74\x75\x72\145\40\170\155\154\x6e\163\72\x64\x73\75\42\x68\x74\164\160\x3a\x2f\57\167\x77\x77\x2e\x77\x33\56\x6f\162\x67\x2f\x32\x30\60\x30\57\x30\71\57\x78\155\x6c\144\x73\151\147\x23\x22\76\12\x20\x20\74\x64\163\x3a\x53\x69\x67\x6e\x65\144\x49\156\146\x6f\76\12\x20\x20\40\40\74\x64\x73\x3a\x53\x69\147\x6e\141\x74\165\162\x65\115\x65\x74\150\x6f\x64\40\x2f\76\xa\40\40\74\x2f\x64\163\72\x53\x69\147\156\145\144\x49\x6e\x66\157\x3e\12\x3c\x2f\x64\163\72\123\x69\x67\x6e\x61\164\x75\x72\x65\x3e";
    const BASE_TEMPLATE = "\x3c\x53\151\147\156\141\x74\165\x72\x65\40\x78\155\154\156\x73\75\x22\x68\x74\164\160\x3a\57\x2f\x77\x77\x77\56\x77\x33\x2e\x6f\162\147\x2f\62\60\60\x30\57\x30\x39\x2f\x78\155\x6c\x64\163\151\x67\x23\x22\x3e\xa\x20\40\74\x53\151\x67\156\x65\144\x49\156\146\157\76\12\40\x20\x20\x20\74\x53\x69\147\x6e\141\164\x75\x72\x65\115\x65\x74\150\157\x64\40\x2f\x3e\12\40\40\x3c\x2f\x53\151\x67\x6e\145\144\x49\x6e\x66\157\76\xa\x3c\57\123\x69\x67\x6e\x61\x74\x75\162\x65\x3e";
    public $sigNode = null;
    public $idKeys = array();
    public $idNS = array();
    private $signedInfo = null;
    private $xPathCtx = null;
    private $canonicalMethod = null;
    private $prefix = '';
    private $searchpfx = "\163\145\143\x64\x73\151\147";
    private $validatedNodes = null;
    public function __construct($zp = "\x64\163")
    {
        $yH = self::BASE_TEMPLATE;
        if (empty($zp)) {
            goto PD;
        }
        $this->prefix = $zp . "\x3a";
        $zz = array("\x3c\123", "\74\57\123", "\x78\x6d\154\156\x73\x3d");
        $GE = array("\74{$zp}\x3a\x53", "\74\x2f{$zp}\72\123", "\170\155\154\156\163\72{$zp}\75");
        $yH = str_replace($zz, $GE, $yH);
        PD:
        $Bh = new DOMDocument();
        $Bh->loadXML($yH);
        $this->sigNode = $Bh->documentElement;
    }
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
    private function getXPathObj()
    {
        if (!(empty($this->xPathCtx) && !empty($this->sigNode))) {
            goto b1;
        }
        $gg = new DOMXPath($this->sigNode->ownerDocument);
        $gg->registerNamespace("\163\145\x63\x64\x73\x69\147", self::XMLDSIGNS);
        $this->xPathCtx = $gg;
        b1:
        return $this->xPathCtx;
    }
    public static function generateGUID($zp = "\x70\x66\170")
    {
        $a0 = md5(uniqid(mt_rand(), true));
        $SY = $zp . substr($a0, 0, 8) . "\x2d" . substr($a0, 8, 4) . "\55" . substr($a0, 12, 4) . "\x2d" . substr($a0, 16, 4) . "\x2d" . substr($a0, 20, 12);
        return $SY;
    }
    public static function generate_GUID($zp = "\160\x66\170")
    {
        return self::generateGUID($zp);
    }
    public function locateSignature($vm, $Cy = 0)
    {
        if ($vm instanceof DOMDocument) {
            goto IP;
        }
        $Sc = $vm->ownerDocument;
        goto f9;
        IP:
        $Sc = $vm;
        f9:
        if (!$Sc) {
            goto Hg;
        }
        $gg = new DOMXPath($Sc);
        $gg->registerNamespace("\x73\145\x63\144\163\x69\147", self::XMLDSIGNS);
        $Jx = "\x2e\57\57\163\145\143\144\163\151\x67\72\123\151\x67\x6e\141\164\x75\x72\145";
        $wP = $gg->query($Jx, $vm);
        $this->sigNode = $wP->item($Cy);
        return $this->sigNode;
        Hg:
        return null;
    }
    public function createNewSignNode($gD, $yY = null)
    {
        $Sc = $this->sigNode->ownerDocument;
        if (!is_null($yY)) {
            goto gH;
        }
        $uu = $Sc->createElementNS(self::XMLDSIGNS, $this->prefix . $gD);
        goto Pg;
        gH:
        $uu = $Sc->createElementNS(self::XMLDSIGNS, $this->prefix . $gD, $yY);
        Pg:
        return $uu;
    }
    public function setCanonicalMethod($Ry)
    {
        switch ($Ry) {
            case "\150\x74\x74\x70\x3a\x2f\57\167\x77\167\x2e\167\63\56\157\162\147\x2f\124\x52\x2f\62\60\x30\61\x2f\x52\105\103\55\170\155\154\55\x63\x31\64\156\55\x32\60\60\x31\60\x33\61\x35":
            case "\x68\164\x74\x70\72\57\x2f\167\167\x77\56\x77\x33\x2e\x6f\x72\x67\57\124\x52\57\x32\x30\x30\61\x2f\x52\x45\103\55\x78\x6d\x6c\x2d\x63\61\x34\156\55\x32\60\60\61\60\x33\x31\x35\43\127\x69\164\x68\x43\157\x6d\155\145\156\164\x73":
            case "\x68\x74\x74\160\x3a\x2f\57\x77\167\167\56\167\63\56\x6f\x72\147\57\x32\60\x30\x31\57\61\x30\57\170\x6d\x6c\x2d\145\170\143\x2d\143\x31\x34\x6e\43":
            case "\150\164\164\x70\x3a\x2f\57\x77\x77\167\x2e\x77\x33\56\x6f\162\x67\x2f\62\60\60\x31\57\61\60\57\170\155\x6c\55\x65\170\x63\55\x63\61\x34\x6e\x23\x57\x69\x74\150\x43\157\155\155\145\156\x74\163":
                $this->canonicalMethod = $Ry;
                goto RA;
            default:
                throw new Exception("\111\x6e\x76\x61\x6c\151\144\x20\103\141\156\157\x6e\151\143\141\x6c\40\115\x65\164\150\x6f\x64");
        }
        Im:
        RA:
        if (!($gg = $this->getXPathObj())) {
            goto Cd;
        }
        $Jx = "\x2e\57" . $this->searchpfx . "\72\123\151\x67\156\145\144\111\156\x66\157";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($It = $wP->item(0))) {
            goto ia;
        }
        $Jx = "\56\57" . $this->searchpfx . "\103\x61\156\157\x6e\x69\x63\x61\x6c\x69\172\x61\164\151\x6f\x6e\x4d\145\164\150\x6f\144";
        $wP = $gg->query($Jx, $It);
        if ($bU = $wP->item(0)) {
            goto B9;
        }
        $bU = $this->createNewSignNode("\103\x61\156\157\156\x69\x63\x61\154\x69\172\x61\164\x69\157\x6e\115\145\x74\x68\157\x64");
        $It->insertBefore($bU, $It->firstChild);
        B9:
        $bU->setAttribute("\x41\154\x67\x6f\x72\151\164\150\x6d", $this->canonicalMethod);
        ia:
        Cd:
    }
    private function canonicalizeData($uu, $Lp, $q2 = null, $XV = null)
    {
        $dG = false;
        $VB = false;
        switch ($Lp) {
            case "\150\164\x74\x70\x3a\57\x2f\167\x77\167\56\x77\63\56\x6f\x72\147\57\x54\x52\x2f\62\60\x30\x31\x2f\x52\105\x43\x2d\x78\x6d\154\x2d\143\61\64\156\55\x32\x30\60\x31\x30\x33\61\x35":
                $dG = false;
                $VB = false;
                goto C7;
            case "\150\164\164\x70\72\57\x2f\167\167\167\x2e\x77\x33\56\x6f\162\147\57\x54\122\x2f\62\x30\60\x31\x2f\122\x45\103\x2d\170\155\154\x2d\x63\61\64\156\55\62\x30\x30\61\x30\63\x31\65\43\x57\151\x74\150\103\x6f\x6d\155\145\x6e\164\163":
                $VB = true;
                goto C7;
            case "\x68\x74\x74\160\72\x2f\57\167\x77\167\56\167\x33\56\x6f\x72\147\57\x32\60\x30\61\57\x31\x30\57\x78\155\154\55\145\x78\x63\55\x63\61\64\x6e\43":
                $dG = true;
                goto C7;
            case "\150\164\x74\160\72\57\x2f\167\x77\x77\56\x77\x33\x2e\157\162\x67\x2f\62\x30\x30\61\x2f\61\x30\57\x78\x6d\x6c\x2d\x65\170\x63\x2d\143\61\x34\156\43\x57\151\164\150\x43\x6f\155\x6d\x65\x6e\x74\x73":
                $dG = true;
                $VB = true;
                goto C7;
        }
        Qo:
        C7:
        if (!(is_null($q2) && $uu instanceof DOMNode && $uu->ownerDocument !== null && $uu->isSameNode($uu->ownerDocument->documentElement))) {
            goto Gi;
        }
        $mg = $uu;
        hy:
        if (!($AW = $mg->previousSibling)) {
            goto el;
        }
        if (!($AW->nodeType == XML_PI_NODE || $AW->nodeType == XML_COMMENT_NODE && $VB)) {
            goto ez;
        }
        goto el;
        ez:
        $mg = $AW;
        goto hy;
        el:
        if (!($AW == null)) {
            goto hN;
        }
        $uu = $uu->ownerDocument;
        hN:
        Gi:
        return $uu->C14N($dG, $VB, $q2, $XV);
    }
    public function canonicalizeSignedInfo()
    {
        $Sc = $this->sigNode->ownerDocument;
        $Lp = null;
        if (!$Sc) {
            goto Tf;
        }
        $gg = $this->getXPathObj();
        $Jx = "\x2e\57\163\x65\143\144\163\x69\147\x3a\123\151\x67\x6e\x65\144\x49\x6e\146\x6f";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($HS = $wP->item(0))) {
            goto Fu;
        }
        $Jx = "\56\57\x73\145\143\x64\163\x69\147\x3a\103\141\x6e\157\x6e\151\143\x61\x6c\x69\x7a\141\x74\151\x6f\x6e\115\x65\164\150\157\144";
        $wP = $gg->query($Jx, $HS);
        if (!($bU = $wP->item(0))) {
            goto Y0;
        }
        $Lp = $bU->getAttribute("\x41\154\147\157\162\x69\164\150\x6d");
        Y0:
        $this->signedInfo = $this->canonicalizeData($HS, $Lp);
        return $this->signedInfo;
        Fu:
        Tf:
        return null;
    }
    public function calculateDigest($uX, $L2, $RV = true)
    {
        switch ($uX) {
            case self::SHA1:
                $Cl = "\163\150\x61\x31";
                goto lF;
            case self::SHA256:
                $Cl = "\163\x68\x61\x32\x35\66";
                goto lF;
            case self::SHA384:
                $Cl = "\163\x68\141\x33\70\64";
                goto lF;
            case self::SHA512:
                $Cl = "\163\x68\141\65\61\62";
                goto lF;
            case self::RIPEMD160:
                $Cl = "\x72\151\x70\x65\155\144\61\66\60";
                goto lF;
            default:
                throw new Exception("\103\141\156\156\157\x74\40\166\x61\154\151\144\141\164\145\x20\144\x69\x67\x65\163\x74\72\40\125\x6e\x73\165\160\160\157\162\x74\x65\144\x20\101\154\x67\157\x72\151\x74\150\x6d\40\74{$uX}\76");
        }
        Yx:
        lF:
        $YH = hash($Cl, $L2, true);
        if (!$RV) {
            goto fG;
        }
        $YH = base64_encode($YH);
        fG:
        return $YH;
    }
    public function validateDigest($Kw, $L2)
    {
        $gg = new DOMXPath($Kw->ownerDocument);
        $gg->registerNamespace("\163\145\x63\x64\163\151\x67", self::XMLDSIGNS);
        $Jx = "\x73\164\162\x69\156\147\50\56\57\163\145\143\x64\163\151\147\x3a\104\x69\x67\145\163\164\115\145\164\x68\157\x64\57\x40\101\154\147\x6f\162\151\164\x68\x6d\x29";
        $uX = $gg->evaluate($Jx, $Kw);
        $TD = $this->calculateDigest($uX, $L2, false);
        $Jx = "\x73\x74\162\x69\x6e\147\x28\56\57\163\x65\x63\x64\x73\x69\147\72\104\x69\147\x65\163\x74\126\141\x6c\165\x65\x29";
        $we = $gg->evaluate($Jx, $Kw);
        return $TD == base64_decode($we);
    }
    public function processTransforms($Kw, $qQ, $E7 = true)
    {
        $L2 = $qQ;
        $gg = new DOMXPath($Kw->ownerDocument);
        $gg->registerNamespace("\163\x65\143\144\x73\x69\x67", self::XMLDSIGNS);
        $Jx = "\56\57\x73\145\x63\144\163\151\x67\72\124\162\x61\x6e\163\146\157\162\x6d\163\x2f\163\x65\x63\144\163\x69\147\72\124\x72\141\x6e\x73\x66\x6f\162\155";
        $IP = $gg->query($Jx, $Kw);
        $t0 = "\150\164\164\x70\x3a\57\x2f\x77\167\x77\x2e\167\x33\x2e\157\x72\x67\57\124\x52\57\x32\x30\x30\61\x2f\122\105\103\x2d\x78\x6d\x6c\x2d\143\61\x34\156\55\x32\x30\x30\61\60\x33\61\65";
        $q2 = null;
        $XV = null;
        foreach ($IP as $wD) {
            $Oe = $wD->getAttribute("\x41\154\147\x6f\162\x69\x74\x68\155");
            switch ($Oe) {
                case "\150\x74\164\x70\x3a\x2f\57\x77\x77\x77\x2e\167\63\x2e\157\162\x67\x2f\x32\x30\x30\61\x2f\61\60\x2f\x78\155\x6c\55\145\170\143\x2d\143\61\x34\x6e\43":
                case "\x68\164\x74\160\x3a\x2f\57\167\167\x77\56\x77\x33\56\x6f\x72\147\57\62\x30\60\x31\x2f\61\60\x2f\x78\155\154\55\x65\170\x63\x2d\x63\x31\x34\x6e\x23\x57\x69\164\150\x43\157\155\155\x65\x6e\x74\x73":
                    if (!$E7) {
                        goto yU;
                    }
                    $t0 = $Oe;
                    goto Zv;
                    yU:
                    $t0 = "\x68\x74\164\x70\x3a\x2f\x2f\167\167\x77\x2e\x77\63\56\157\162\147\x2f\62\x30\60\x31\57\x31\60\x2f\x78\155\x6c\55\145\170\x63\55\x63\x31\64\x6e\x23";
                    Zv:
                    $uu = $wD->firstChild;
                    ox:
                    if (!$uu) {
                        goto LM;
                    }
                    if (!($uu->localName == "\x49\156\x63\x6c\165\x73\151\166\x65\116\x61\155\145\x73\160\x61\143\145\163")) {
                        goto o0;
                    }
                    if (!($Qx = $uu->getAttribute("\x50\162\145\x66\x69\x78\114\151\163\x74"))) {
                        goto Ib;
                    }
                    $mT = array();
                    $y3 = explode("\40", $Qx);
                    foreach ($y3 as $Qx) {
                        $V5 = trim($Qx);
                        if (empty($V5)) {
                            goto Oi;
                        }
                        $mT[] = $V5;
                        Oi:
                        HC:
                    }
                    vB:
                    if (!(count($mT) > 0)) {
                        goto s9;
                    }
                    $XV = $mT;
                    s9:
                    Ib:
                    goto LM;
                    o0:
                    $uu = $uu->nextSibling;
                    goto ox;
                    LM:
                    goto Ya;
                case "\x68\x74\164\160\x3a\57\x2f\x77\x77\x77\x2e\167\x33\x2e\x6f\x72\x67\57\124\122\x2f\62\60\60\x31\57\122\x45\x43\55\170\155\x6c\55\143\61\x34\x6e\x2d\x32\60\60\61\60\x33\x31\x35":
                case "\x68\x74\x74\x70\x3a\x2f\57\x77\167\x77\56\x77\63\x2e\157\x72\147\x2f\x54\122\x2f\x32\x30\x30\61\57\x52\105\x43\x2d\x78\155\x6c\x2d\x63\x31\x34\156\x2d\62\x30\x30\61\x30\x33\61\65\x23\x57\151\164\x68\103\157\155\x6d\145\x6e\x74\163":
                    if (!$E7) {
                        goto wx;
                    }
                    $t0 = $Oe;
                    goto AD;
                    wx:
                    $t0 = "\150\x74\164\160\72\x2f\x2f\x77\x77\x77\x2e\x77\x33\56\x6f\x72\147\x2f\x54\x52\x2f\62\60\x30\x31\x2f\x52\x45\103\55\170\155\154\55\143\x31\64\x6e\55\x32\60\60\x31\x30\x33\x31\65";
                    AD:
                    goto Ya;
                case "\150\x74\x74\x70\x3a\x2f\x2f\x77\167\167\56\x77\x33\x2e\157\x72\147\x2f\x54\122\x2f\x31\x39\71\71\57\x52\105\x43\55\170\160\x61\x74\150\x2d\x31\x39\x39\71\x31\61\61\66":
                    $uu = $wD->firstChild;
                    Sc:
                    if (!$uu) {
                        goto zP;
                    }
                    if (!($uu->localName == "\130\x50\141\164\150")) {
                        goto lo;
                    }
                    $q2 = array();
                    $q2["\x71\x75\145\162\x79"] = "\x28\x2e\57\57\56\40\174\40\x2e\x2f\x2f\x40\x2a\40\x7c\x20\56\57\x2f\x6e\141\155\145\x73\x70\x61\143\145\72\72\x2a\x29\x5b" . $uu->nodeValue . "\x5d";
                    $jl["\156\x61\x6d\145\x73\160\141\x63\x65\163"] = array();
                    $Hx = $gg->query("\56\57\x6e\x61\155\x65\163\x70\x61\x63\145\x3a\72\x2a", $uu);
                    foreach ($Hx as $S1) {
                        if (!($S1->localName != "\x78\x6d\154")) {
                            goto Wt;
                        }
                        $q2["\156\141\x6d\145\x73\160\141\x63\x65\163"][$S1->localName] = $S1->nodeValue;
                        Wt:
                        oI:
                    }
                    n0:
                    goto zP;
                    lo:
                    $uu = $uu->nextSibling;
                    goto Sc;
                    zP:
                    goto Ya;
            }
            D5:
            Ya:
            g_:
        }
        Vd:
        if (!$L2 instanceof DOMNode) {
            goto Zq;
        }
        $L2 = $this->canonicalizeData($qQ, $t0, $q2, $XV);
        Zq:
        return $L2;
    }
    public function processRefNode($Kw)
    {
        $Aa = null;
        $E7 = true;
        if ($lN = $Kw->getAttribute("\125\122\111")) {
            goto zk;
        }
        $E7 = false;
        $Aa = $Kw->ownerDocument;
        goto R6;
        zk:
        $lk = parse_url($lN);
        if (empty($lk["\160\x61\164\x68"])) {
            goto XW;
        }
        $Aa = file_get_contents($lk);
        goto p_;
        XW:
        if ($Gq = $lk["\x66\x72\x61\147\155\145\x6e\164"]) {
            goto ob;
        }
        $Aa = $Kw->ownerDocument;
        goto fz;
        ob:
        $E7 = false;
        $gX = new DOMXPath($Kw->ownerDocument);
        if (!($this->idNS && is_array($this->idNS))) {
            goto S4;
        }
        foreach ($this->idNS as $yN => $hF) {
            $gX->registerNamespace($yN, $hF);
            RW:
        }
        c9:
        S4:
        $ai = "\100\x49\144\75\42" . $Gq . "\x22";
        if (!is_array($this->idKeys)) {
            goto i0;
        }
        foreach ($this->idKeys as $PD) {
            $ai .= "\40\157\x72\x20\100{$PD}\x3d\x27{$Gq}\x27";
            b8:
        }
        x3:
        i0:
        $Jx = "\57\57\x2a\133" . $ai . "\x5d";
        $Aa = $gX->query($Jx)->item(0);
        fz:
        p_:
        R6:
        $L2 = $this->processTransforms($Kw, $Aa, $E7);
        if ($this->validateDigest($Kw, $L2)) {
            goto cL;
        }
        return false;
        cL:
        if (!$Aa instanceof DOMNode) {
            goto TS;
        }
        if (!empty($Gq)) {
            goto yE;
        }
        $this->validatedNodes[] = $Aa;
        goto bl;
        yE:
        $this->validatedNodes[$Gq] = $Aa;
        bl:
        TS:
        return true;
    }
    public function getRefNodeID($Kw)
    {
        if (!($lN = $Kw->getAttribute("\125\122\x49"))) {
            goto ON;
        }
        $lk = parse_url($lN);
        if (!empty($lk["\160\141\164\150"])) {
            goto AJ;
        }
        if (!($Gq = $lk["\x66\162\141\x67\155\145\156\164"])) {
            goto LB;
        }
        return $Gq;
        LB:
        AJ:
        ON:
        return null;
    }
    public function getRefIDs()
    {
        $N3 = array();
        $gg = $this->getXPathObj();
        $Jx = "\56\57\163\145\143\144\163\151\147\x3a\x53\x69\147\x6e\145\144\111\156\x66\157\57\x73\145\x63\x64\163\151\x67\x3a\x52\x65\x66\x65\162\145\156\143\x65";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($wP->length == 0)) {
            goto HE;
        }
        throw new Exception("\x52\145\146\x65\162\x65\156\x63\x65\x20\x6e\157\x64\x65\x73\x20\156\x6f\164\40\146\157\165\156\144");
        HE:
        foreach ($wP as $Kw) {
            $N3[] = $this->getRefNodeID($Kw);
            RY:
        }
        Wc:
        return $N3;
    }
    public function validateReference()
    {
        $wA = $this->sigNode->ownerDocument->documentElement;
        if ($wA->isSameNode($this->sigNode)) {
            goto Lz;
        }
        if (!($this->sigNode->parentNode != null)) {
            goto M7;
        }
        $this->sigNode->parentNode->removeChild($this->sigNode);
        M7:
        Lz:
        $gg = $this->getXPathObj();
        $Jx = "\x2e\57\x73\x65\x63\144\x73\151\x67\72\123\151\147\156\145\x64\111\x6e\x66\x6f\x2f\163\x65\143\x64\x73\151\x67\72\122\x65\146\145\x72\145\x6e\x63\145";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($wP->length == 0)) {
            goto Pc;
        }
        throw new Exception("\122\x65\x66\x65\x72\x65\x6e\143\x65\x20\x6e\x6f\144\x65\163\40\x6e\157\x74\x20\x66\157\165\156\144");
        Pc:
        $this->validatedNodes = array();
        foreach ($wP as $Kw) {
            if ($this->processRefNode($Kw)) {
                goto Z6;
            }
            $this->validatedNodes = null;
            throw new Exception("\x52\x65\146\x65\162\145\156\143\145\40\x76\x61\154\151\144\x61\164\151\157\x6e\40\146\141\x69\154\145\x64");
            Z6:
            VA:
        }
        zn:
        return true;
    }
    private function addRefInternal($Hy, $uu, $Oe, $Gj = null, $zV = null)
    {
        $zp = null;
        $OZ = null;
        $T4 = "\111\144";
        $A4 = true;
        $Zh = false;
        if (!is_array($zV)) {
            goto Xr;
        }
        $zp = empty($zV["\x70\162\145\x66\x69\170"]) ? null : $zV["\x70\x72\x65\146\151\170"];
        $OZ = empty($zV["\160\162\x65\x66\151\x78\x5f\156\x73"]) ? null : $zV["\x70\x72\145\x66\151\x78\137\x6e\163"];
        $T4 = empty($zV["\x69\x64\137\x6e\x61\x6d\x65"]) ? "\x49\144" : $zV["\x69\144\137\156\x61\x6d\145"];
        $A4 = !isset($zV["\157\166\145\x72\167\x72\151\x74\x65"]) ? true : (bool) $zV["\x6f\166\x65\x72\167\x72\x69\164\145"];
        $Zh = !isset($zV["\146\x6f\162\143\145\x5f\x75\x72\x69"]) ? false : (bool) $zV["\x66\157\x72\143\145\x5f\x75\x72\151"];
        Xr:
        $Sz = $T4;
        if (empty($zp)) {
            goto yP;
        }
        $Sz = $zp . "\72" . $Sz;
        yP:
        $Kw = $this->createNewSignNode("\122\145\x66\x65\x72\145\x6e\143\x65");
        $Hy->appendChild($Kw);
        if (!$uu instanceof DOMDocument) {
            goto KK;
        }
        if ($Zh) {
            goto dD;
        }
        goto Aw;
        KK:
        $lN = null;
        if ($A4) {
            goto HB;
        }
        $lN = $OZ ? $uu->getAttributeNS($OZ, $T4) : $uu->getAttribute($T4);
        HB:
        if (!empty($lN)) {
            goto Dp;
        }
        $lN = self::generateGUID();
        $uu->setAttributeNS($OZ, $Sz, $lN);
        Dp:
        $Kw->setAttribute("\x55\x52\x49", "\43" . $lN);
        goto Aw;
        dD:
        $Kw->setAttribute("\x55\x52\x49", '');
        Aw:
        $wK = $this->createNewSignNode("\x54\162\141\x6e\163\x66\157\162\x6d\x73");
        $Kw->appendChild($wK);
        if (is_array($Gj)) {
            goto l1;
        }
        if (!empty($this->canonicalMethod)) {
            goto cV;
        }
        goto gt;
        l1:
        foreach ($Gj as $wD) {
            $qh = $this->createNewSignNode("\124\x72\141\156\x73\146\x6f\x72\x6d");
            $wK->appendChild($qh);
            if (is_array($wD) && !empty($wD["\150\x74\164\160\x3a\x2f\57\x77\x77\167\x2e\167\63\x2e\x6f\162\147\57\x54\122\x2f\61\71\x39\x39\57\122\105\x43\55\x78\x70\x61\164\x68\55\61\71\x39\x39\x31\61\x31\x36"]) && !empty($wD["\x68\164\x74\x70\x3a\57\57\167\x77\167\56\x77\63\56\x6f\162\147\57\124\122\57\x31\71\x39\71\x2f\x52\x45\x43\x2d\170\160\x61\x74\x68\x2d\61\x39\x39\x39\x31\x31\x31\66"]["\161\165\x65\x72\x79"])) {
                goto Sm;
            }
            $qh->setAttribute("\x41\x6c\x67\157\x72\151\x74\150\x6d", $wD);
            goto QV;
            Sm:
            $qh->setAttribute("\101\154\147\157\x72\x69\164\150\155", "\x68\164\x74\x70\x3a\x2f\x2f\x77\x77\x77\56\x77\x33\x2e\x6f\x72\x67\x2f\124\x52\x2f\61\x39\71\x39\57\x52\105\x43\x2d\x78\160\141\164\x68\x2d\61\x39\71\71\x31\61\x31\x36");
            $Gz = $this->createNewSignNode("\130\120\141\164\x68", $wD["\x68\x74\x74\x70\x3a\x2f\57\x77\167\x77\x2e\167\x33\56\157\162\147\57\124\122\57\x31\71\71\x39\x2f\122\105\103\x2d\x78\160\141\164\x68\x2d\61\x39\71\x39\61\61\x31\x36"]["\161\165\x65\162\x79"]);
            $qh->appendChild($Gz);
            if (empty($wD["\150\x74\164\x70\72\57\x2f\x77\x77\x77\56\167\63\x2e\x6f\x72\x67\x2f\x54\x52\x2f\61\x39\71\x39\x2f\122\105\103\x2d\x78\160\141\x74\150\55\61\71\71\71\61\61\61\x36"]["\156\141\155\x65\x73\160\x61\x63\145\163"])) {
                goto gz;
            }
            foreach ($wD["\150\164\164\160\x3a\57\57\167\167\x77\56\167\63\x2e\157\162\x67\57\124\x52\x2f\61\x39\71\71\x2f\122\x45\x43\x2d\170\x70\x61\x74\x68\55\x31\71\71\71\x31\x31\61\66"]["\x6e\x61\x6d\x65\163\160\x61\x63\145\x73"] as $zp => $xW) {
                $Gz->setAttributeNS("\150\164\x74\160\x3a\x2f\57\167\x77\x77\56\x77\x33\x2e\x6f\x72\147\57\x32\60\60\60\57\170\155\x6c\x6e\163\x2f", "\x78\155\154\x6e\163\72{$zp}", $xW);
                fs:
            }
            Q9:
            gz:
            QV:
            Zy:
        }
        vL:
        goto gt;
        cV:
        $qh = $this->createNewSignNode("\124\162\x61\x6e\x73\x66\x6f\162\x6d");
        $wK->appendChild($qh);
        $qh->setAttribute("\x41\x6c\x67\157\x72\x69\x74\150\x6d", $this->canonicalMethod);
        gt:
        $zx = $this->processTransforms($Kw, $uu);
        $TD = $this->calculateDigest($Oe, $zx);
        $gn = $this->createNewSignNode("\104\x69\x67\x65\x73\x74\x4d\145\164\150\x6f\144");
        $Kw->appendChild($gn);
        $gn->setAttribute("\101\154\x67\157\x72\151\164\150\155", $Oe);
        $we = $this->createNewSignNode("\x44\151\147\145\x73\x74\x56\141\x6c\165\x65", $TD);
        $Kw->appendChild($we);
    }
    public function addReference($uu, $Oe, $Gj = null, $zV = null)
    {
        if (!($gg = $this->getXPathObj())) {
            goto aN;
        }
        $Jx = "\x2e\x2f\163\x65\143\144\x73\x69\x67\72\123\151\x67\x6e\145\x64\x49\156\146\x6f";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($CM = $wP->item(0))) {
            goto iJ;
        }
        $this->addRefInternal($CM, $uu, $Oe, $Gj, $zV);
        iJ:
        aN:
    }
    public function addReferenceList($jy, $Oe, $Gj = null, $zV = null)
    {
        if (!($gg = $this->getXPathObj())) {
            goto jJ;
        }
        $Jx = "\56\x2f\163\x65\x63\x64\x73\x69\x67\x3a\x53\151\x67\156\x65\144\x49\156\x66\x6f";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($CM = $wP->item(0))) {
            goto a1;
        }
        foreach ($jy as $uu) {
            $this->addRefInternal($CM, $uu, $Oe, $Gj, $zV);
            pn:
        }
        GG:
        a1:
        jJ:
    }
    public function addObject($L2, $Kr = null, $tV = null)
    {
        $s0 = $this->createNewSignNode("\x4f\x62\152\145\x63\164");
        $this->sigNode->appendChild($s0);
        if (empty($Kr)) {
            goto NU;
        }
        $s0->setAttribute("\x4d\x69\155\x65\x54\171\160\145", $Kr);
        NU:
        if (empty($tV)) {
            goto Bd;
        }
        $s0->setAttribute("\x45\156\x63\x6f\144\x69\156\147", $tV);
        Bd:
        if ($L2 instanceof DOMElement) {
            goto j6;
        }
        $Hd = $this->sigNode->ownerDocument->createTextNode($L2);
        goto Mb;
        j6:
        $Hd = $this->sigNode->ownerDocument->importNode($L2, true);
        Mb:
        $s0->appendChild($Hd);
        return $s0;
    }
    public function locateKey($uu = null)
    {
        if (!empty($uu)) {
            goto GE;
        }
        $uu = $this->sigNode;
        GE:
        if ($uu instanceof DOMNode) {
            goto oH;
        }
        return null;
        oH:
        if (!($Sc = $uu->ownerDocument)) {
            goto i4;
        }
        $gg = new DOMXPath($Sc);
        $gg->registerNamespace("\x73\x65\x63\144\163\151\147", self::XMLDSIGNS);
        $Jx = "\163\x74\162\x69\156\x67\50\x2e\57\x73\x65\143\144\163\x69\x67\x3a\x53\151\x67\156\145\144\x49\156\146\157\57\163\145\143\144\163\x69\147\x3a\x53\x69\x67\x6e\141\x74\165\162\145\x4d\145\164\150\x6f\144\57\100\101\x6c\x67\x6f\162\x69\x74\150\155\x29";
        $Oe = $gg->evaluate($Jx, $uu);
        if (!$Oe) {
            goto ft;
        }
        try {
            $vr = new XMLSecurityKey($Oe, array("\164\171\x70\x65" => "\160\165\x62\x6c\151\143"));
        } catch (Exception $YR) {
            return null;
        }
        return $vr;
        ft:
        i4:
        return null;
    }
    public function verify($vr)
    {
        $Sc = $this->sigNode->ownerDocument;
        $gg = new DOMXPath($Sc);
        $gg->registerNamespace("\x73\145\x63\144\163\x69\147", self::XMLDSIGNS);
        $Jx = "\163\x74\162\151\x6e\147\x28\56\x2f\x73\145\x63\144\x73\151\147\x3a\123\x69\147\x6e\141\x74\x75\x72\145\x56\x61\x6c\165\x65\x29";
        $H3 = $gg->evaluate($Jx, $this->sigNode);
        if (!empty($H3)) {
            goto l0;
        }
        throw new Exception("\x55\x6e\x61\x62\154\145\x20\x74\157\x20\154\157\143\x61\x74\145\40\123\x69\147\x6e\x61\x74\165\x72\145\126\141\154\165\145");
        l0:
        return $vr->verifySignature($this->signedInfo, base64_decode($H3));
    }
    public function signData($vr, $L2)
    {
        return $vr->signData($L2);
    }
    public function sign($vr, $Ss = null)
    {
        if (!($Ss != null)) {
            goto Y7;
        }
        $this->resetXPathObj();
        $this->appendSignature($Ss);
        $this->sigNode = $Ss->lastChild;
        Y7:
        if (!($gg = $this->getXPathObj())) {
            goto X9;
        }
        $Jx = "\56\57\x73\x65\x63\x64\163\x69\x67\x3a\x53\151\147\156\x65\x64\x49\x6e\146\157";
        $wP = $gg->query($Jx, $this->sigNode);
        if (!($CM = $wP->item(0))) {
            goto EI;
        }
        $Jx = "\x2e\57\163\145\x63\x64\163\x69\x67\72\123\151\x67\156\x61\164\x75\x72\145\x4d\145\x74\150\x6f\x64";
        $wP = $gg->query($Jx, $CM);
        $rV = $wP->item(0);
        $rV->setAttribute("\101\x6c\x67\x6f\x72\x69\164\150\x6d", $vr->type);
        $L2 = $this->canonicalizeData($CM, $this->canonicalMethod);
        $H3 = base64_encode($this->signData($vr, $L2));
        $fu = $this->createNewSignNode("\x53\151\147\x6e\141\x74\x75\x72\x65\126\141\154\x75\x65", $H3);
        if ($L1 = $CM->nextSibling) {
            goto CA;
        }
        $this->sigNode->appendChild($fu);
        goto PO;
        CA:
        $L1->parentNode->insertBefore($fu, $L1);
        PO:
        EI:
        X9:
    }
    public function appendCert()
    {
    }
    public function appendKey($vr, $ak = null)
    {
        $vr->serializeKey($ak);
    }
    public function insertSignature($uu, $xi = null)
    {
        $iw = $uu->ownerDocument;
        $jH = $iw->importNode($this->sigNode, true);
        if ($xi == null) {
            goto oc;
        }
        return $uu->insertBefore($jH, $xi);
        goto ix;
        oc:
        return $uu->insertBefore($jH);
        ix:
    }
    public function appendSignature($e3, $pP = false)
    {
        $xi = $pP ? $e3->firstChild : null;
        return $this->insertSignature($e3, $xi);
    }
    public static function get509XCert($WZ, $fb = true)
    {
        $qw = self::staticGet509XCerts($WZ, $fb);
        if (empty($qw)) {
            goto Y1;
        }
        return $qw[0];
        Y1:
        return '';
    }
    public static function staticGet509XCerts($qw, $fb = true)
    {
        if ($fb) {
            goto IS;
        }
        return array($qw);
        goto GA;
        IS:
        $L2 = '';
        $cA = array();
        $B3 = explode("\xa", $qw);
        $GR = false;
        foreach ($B3 as $I5) {
            if (!$GR) {
                goto NX;
            }
            if (!(strncmp($I5, "\55\55\55\x2d\x2d\x45\116\104\40\x43\x45\x52\x54\x49\106\111\103\x41\124\105", 20) == 0)) {
                goto cP;
            }
            $GR = false;
            $cA[] = $L2;
            $L2 = '';
            goto io;
            cP:
            $L2 .= trim($I5);
            goto QM;
            NX:
            if (!(strncmp($I5, "\55\x2d\x2d\x2d\55\x42\105\107\111\116\x20\103\x45\122\x54\111\106\x49\x43\x41\x54\105", 22) == 0)) {
                goto qu;
            }
            $GR = true;
            qu:
            QM:
            io:
        }
        YV:
        return $cA;
        GA:
    }
    public static function staticAdd509Cert($eQ, $WZ, $fb = true, $oG = false, $gg = null, $zV = null)
    {
        if (!$oG) {
            goto cF;
        }
        $WZ = file_get_contents($WZ);
        cF:
        if ($eQ instanceof DOMElement) {
            goto Xl;
        }
        throw new Exception("\111\156\166\x61\154\x69\144\x20\x70\x61\162\145\156\164\x20\116\157\x64\145\x20\x70\x61\162\x61\155\x65\x74\x65\x72");
        Xl:
        $gd = $eQ->ownerDocument;
        if (!empty($gg)) {
            goto WJ;
        }
        $gg = new DOMXPath($eQ->ownerDocument);
        $gg->registerNamespace("\x73\145\x63\144\163\151\147", self::XMLDSIGNS);
        WJ:
        $Jx = "\56\57\163\145\x63\x64\x73\151\x67\72\113\x65\171\x49\156\x66\x6f";
        $wP = $gg->query($Jx, $eQ);
        $s2 = $wP->item(0);
        $D0 = '';
        if (!$s2) {
            goto pM;
        }
        $Qx = $s2->lookupPrefix(self::XMLDSIGNS);
        if (empty($Qx)) {
            goto lL;
        }
        $D0 = $Qx . "\72";
        lL:
        goto Rl;
        pM:
        $Qx = $eQ->lookupPrefix(self::XMLDSIGNS);
        if (empty($Qx)) {
            goto zU;
        }
        $D0 = $Qx . "\72";
        zU:
        $C4 = false;
        $s2 = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\x4b\145\171\x49\x6e\x66\157");
        $Jx = "\56\x2f\x73\x65\x63\x64\x73\x69\x67\72\x4f\x62\x6a\x65\143\164";
        $wP = $gg->query($Jx, $eQ);
        if (!($n_ = $wP->item(0))) {
            goto cU;
        }
        $n_->parentNode->insertBefore($s2, $n_);
        $C4 = true;
        cU:
        if ($C4) {
            goto D6;
        }
        $eQ->appendChild($s2);
        D6:
        Rl:
        $qw = self::staticGet509XCerts($WZ, $fb);
        $dR = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\130\65\x30\71\x44\x61\x74\x61");
        $s2->appendChild($dR);
        $ET = false;
        $Z2 = false;
        if (!is_array($zV)) {
            goto L9;
        }
        if (empty($zV["\151\x73\163\x75\x65\162\x53\145\x72\x69\141\154"])) {
            goto EW;
        }
        $ET = true;
        EW:
        if (empty($zV["\163\165\x62\152\x65\143\x74\x4e\x61\x6d\x65"])) {
            goto qD;
        }
        $Z2 = true;
        qD:
        L9:
        foreach ($qw as $Z0) {
            if (!($ET || $Z2)) {
                goto w6;
            }
            if (!($Ex = openssl_x509_parse("\55\x2d\55\55\55\102\x45\x47\x49\116\x20\x43\x45\122\x54\111\x46\x49\103\x41\124\x45\55\55\x2d\x2d\x2d\xa" . chunk_split($Z0, 64, "\xa") . "\55\55\x2d\55\55\105\116\104\x20\x43\x45\x52\124\x49\106\x49\103\101\x54\x45\x2d\x2d\55\x2d\x2d\xa"))) {
                goto hs;
            }
            if (!($Z2 && !empty($Ex["\163\x75\142\152\x65\x63\164"]))) {
                goto em;
            }
            if (is_array($Ex["\x73\165\142\152\145\143\x74"])) {
                goto nA;
            }
            $XN = $Ex["\x69\163\x73\165\145\162"];
            goto eX;
            nA:
            $L_ = array();
            foreach ($Ex["\x73\165\142\x6a\145\143\x74"] as $w5 => $yY) {
                if (is_array($yY)) {
                    goto c0;
                }
                array_unshift($L_, "{$w5}\x3d{$yY}");
                goto eo;
                c0:
                foreach ($yY as $Tn) {
                    array_unshift($L_, "{$w5}\x3d{$Tn}");
                    Sh:
                }
                XZ:
                eo:
                PX:
            }
            fA:
            $XN = implode("\54", $L_);
            eX:
            $nI = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\130\x35\60\71\x53\165\x62\152\x65\x63\x74\116\141\155\145", $XN);
            $dR->appendChild($nI);
            em:
            if (!($ET && !empty($Ex["\x69\x73\163\x75\145\162"]) && !empty($Ex["\x73\145\162\x69\141\154\116\x75\155\x62\x65\x72"]))) {
                goto OO;
            }
            if (is_array($Ex["\151\163\163\x75\x65\162"])) {
                goto MH;
            }
            $xO = $Ex["\x69\163\x73\165\x65\162"];
            goto NF;
            MH:
            $L_ = array();
            foreach ($Ex["\151\x73\x73\x75\x65\x72"] as $w5 => $yY) {
                array_unshift($L_, "{$w5}\75{$yY}");
                lu:
            }
            Ep:
            $xO = implode("\54", $L_);
            NF:
            $jM = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\130\65\x30\71\111\x73\163\165\x65\162\123\x65\x72\151\x61\154");
            $dR->appendChild($jM);
            $ME = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\x58\x35\x30\71\x49\163\163\x75\145\162\x4e\x61\155\x65", $xO);
            $jM->appendChild($ME);
            $ME = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\x58\65\60\x39\x53\x65\162\x69\141\154\x4e\165\155\142\145\x72", $Ex["\x73\145\162\151\141\x6c\116\x75\x6d\x62\x65\162"]);
            $jM->appendChild($ME);
            OO:
            hs:
            w6:
            $pf = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\130\x35\x30\x39\x43\145\x72\x74\x69\146\x69\x63\x61\164\x65", $Z0);
            $dR->appendChild($pf);
            a3:
        }
        mS:
    }
    public function add509Cert($WZ, $fb = true, $oG = false, $zV = null)
    {
        if (!($gg = $this->getXPathObj())) {
            goto Bp;
        }
        self::staticAdd509Cert($this->sigNode, $WZ, $fb, $oG, $gg, $zV);
        Bp:
    }
    public function appendToKeyInfo($uu)
    {
        $eQ = $this->sigNode;
        $gd = $eQ->ownerDocument;
        $gg = $this->getXPathObj();
        if (!empty($gg)) {
            goto Zs;
        }
        $gg = new DOMXPath($eQ->ownerDocument);
        $gg->registerNamespace("\x73\145\143\x64\x73\151\147", self::XMLDSIGNS);
        Zs:
        $Jx = "\x2e\x2f\x73\x65\x63\x64\x73\x69\x67\x3a\113\x65\x79\x49\156\146\x6f";
        $wP = $gg->query($Jx, $eQ);
        $s2 = $wP->item(0);
        if ($s2) {
            goto Nm;
        }
        $D0 = '';
        $Qx = $eQ->lookupPrefix(self::XMLDSIGNS);
        if (empty($Qx)) {
            goto Ul;
        }
        $D0 = $Qx . "\72";
        Ul:
        $C4 = false;
        $s2 = $gd->createElementNS(self::XMLDSIGNS, $D0 . "\113\x65\171\x49\156\x66\x6f");
        $Jx = "\56\57\x73\145\x63\144\163\x69\x67\x3a\117\x62\x6a\145\x63\164";
        $wP = $gg->query($Jx, $eQ);
        if (!($n_ = $wP->item(0))) {
            goto Eg;
        }
        $n_->parentNode->insertBefore($s2, $n_);
        $C4 = true;
        Eg:
        if ($C4) {
            goto Bz;
        }
        $eQ->appendChild($s2);
        Bz:
        Nm:
        $s2->appendChild($uu);
        return $s2;
    }
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }
}
