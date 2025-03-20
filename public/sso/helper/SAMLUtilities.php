<?php


namespace MiniOrange\Helper;

use DOMElement;
use DOMNode;
use DOMDocument;
use Exception;
use MiniOrange\Helper\Lib\XMLSecLibs\XMLSecurityKey;
use MiniOrange\Helper\Lib\XMLSecLibs\XMLSecEnc;
use MiniOrange\Helper\Lib\XMLSecLibs\XMLSecurityDSig;
class SAMLUtilities
{
    public static function generateID()
    {
        return "\137" . self::stringToHex(self::generateRandomBytes(21));
    }
    public static function stringToHex($iy)
    {
        $ut = '';
        $Hz = 0;
        LL:
        if (!($Hz < strlen($iy))) {
            goto sG;
        }
        $ut .= sprintf("\x25\x30\62\x78", ord($iy[$Hz]));
        dj:
        $Hz++;
        goto LL;
        sG:
        return $ut;
    }
    public static function generateRandomBytes($x7)
    {
        return openssl_random_pseudo_bytes($x7);
    }
    public static function generateTimestamp($mr = NULL)
    {
        if (!($mr === NULL)) {
            goto dH;
        }
        $mr = time();
        dH:
        return gmdate("\131\x2d\x6d\55\144\134\x54\x48\72\151\x3a\163\134\x5a", $mr);
    }
    public static function xpQuery(DomNode $uu, $Jx)
    {
        static $hA = NULL;
        if ($uu instanceof DOMDocument) {
            goto QZ;
        }
        $Sc = $uu->ownerDocument;
        goto sc;
        QZ:
        $Sc = $uu;
        sc:
        if (!($hA === NULL || !$hA->document->isSameNode($Sc))) {
            goto Kp;
        }
        $hA = new \DOMXPath($Sc);
        $hA->registerNamespace("\x73\157\141\x70\x2d\x65\156\x76", "\x68\164\x74\160\x3a\57\x2f\163\x63\x68\145\155\141\163\56\170\155\x6c\163\x6f\x61\x70\x2e\x6f\162\147\57\163\157\141\160\x2f\145\156\166\x65\154\x6f\160\145\57");
        $hA->registerNamespace("\x73\141\x6d\x6c\x5f\160\162\x6f\x74\x6f\143\x6f\154", "\165\162\156\72\157\x61\x73\x69\x73\x3a\x6e\x61\x6d\145\x73\x3a\x74\143\72\x53\x41\x4d\114\72\62\56\60\72\x70\162\157\164\x6f\143\157\x6c");
        $hA->registerNamespace("\x73\141\x6d\154\137\x61\x73\163\145\x72\x74\151\157\x6e", "\x75\162\156\72\x6f\x61\x73\151\x73\x3a\x6e\141\155\145\x73\72\x74\x63\72\123\x41\x4d\x4c\72\62\x2e\60\x3a\x61\x73\x73\145\x72\164\x69\157\156");
        $hA->registerNamespace("\163\141\155\154\137\155\145\x74\141\x64\141\164\141", "\x75\x72\156\72\x6f\141\x73\151\x73\x3a\x6e\141\155\145\x73\72\164\x63\72\123\x41\x4d\x4c\72\x32\56\x30\x3a\x6d\x65\x74\141\x64\x61\x74\x61");
        $hA->registerNamespace("\144\163", "\x68\164\x74\x70\72\x2f\57\167\x77\167\56\x77\63\56\x6f\162\147\57\62\60\60\60\x2f\x30\71\x2f\170\x6d\x6c\x64\x73\x69\x67\x23");
        $hA->registerNamespace("\170\145\156\x63", "\x68\x74\164\160\x3a\57\x2f\x77\167\167\56\167\x33\56\x6f\162\x67\x2f\62\60\x30\x31\x2f\x30\x34\57\x78\x6d\x6c\x65\x6e\x63\43");
        Kp:
        $mM = $hA->query($Jx, $uu);
        $ut = array();
        $Hz = 0;
        E8:
        if (!($Hz < $mM->length)) {
            goto Xp;
        }
        $ut[$Hz] = $mM->item($Hz);
        Kc:
        $Hz++;
        goto E8;
        Xp:
        return $ut;
    }
    public static function parseNameId(DOMElement $HR)
    {
        $ut = array("\126\141\x6c\x75\x65" => trim($HR->textContent));
        foreach (array("\116\141\155\x65\121\x75\141\154\x69\x66\151\145\162", "\123\120\x4e\x61\x6d\x65\121\165\x61\x6c\151\x66\x69\145\162", "\x46\157\162\x6d\x61\x74") as $Ml) {
            if (!$HR->hasAttribute($Ml)) {
                goto R9;
            }
            $ut[$Ml] = $HR->getAttribute($Ml);
            R9:
            yA:
        }
        dI:
        return $ut;
    }
    public static function xsDateTimeToTimestamp($fM)
    {
        $os = array();
        $Mc = "\57\x5e\50\x5c\x64\134\144\x5c\144\134\x64\51\55\x28\x5c\144\134\144\51\x2d\50\x5c\144\x5c\x64\x29\124\x28\134\144\x5c\144\51\x3a\x28\x5c\x64\x5c\144\51\x3a\50\134\x64\134\x64\x29\50\x3f\72\134\56\134\144\53\51\x3f\x5a\44\57\x44";
        if (!(preg_match($Mc, $fM, $os) == 0)) {
            goto fU;
        }
        throw new Exception("\111\x6e\166\x61\154\x69\x64\40\x53\x41\x4d\114\x32\x20\164\x69\x6d\145\x73\x74\141\x6d\160\40\160\141\163\163\145\x64\x20\x74\x6f\40\170\x73\x44\141\x74\x65\x54\x69\155\145\124\157\124\151\155\145\x73\164\x61\x6d\x70\72\x20" . $fM);
        fU:
        $On = intval($os[1]);
        $Y_ = intval($os[2]);
        $j_ = intval($os[3]);
        $O1 = intval($os[4]);
        $CU = intval($os[5]);
        $lb = intval($os[6]);
        $I9 = gmmktime($O1, $CU, $lb, $Y_, $j_, $On);
        return $I9;
    }
    private static function doDecryptElement(DOMElement $FN, XMLSecurityKey $cu, array &$kZ)
    {
        $fg = new XMLSecEnc();
        $fg->setNode($FN);
        $fg->type = $FN->getAttribute("\124\x79\160\145");
        $Qf = $fg->locateKey($FN);
        if ($Qf) {
            goto qq;
        }
        throw new Exception("\103\x6f\165\154\x64\x20\156\x6f\x74\40\x6c\157\x63\x61\164\145\x20\x6b\145\x79\40\141\x6c\x67\x6f\162\151\164\150\x6d\x20\x69\x6e\40\x65\156\x63\x72\x79\x70\164\x65\144\40\144\141\x74\141\56");
        qq:
        $wY = $fg->locateKeyInfo($Qf);
        if ($wY) {
            goto e4;
        }
        throw new Exception("\103\x6f\x75\154\x64\40\x6e\157\164\x20\154\157\143\141\x74\x65\40\74\144\x73\x69\147\x3a\113\x65\171\111\156\x66\x6f\76\40\146\157\x72\x20\164\x68\x65\x20\145\x6e\x63\162\171\160\164\145\x64\x20\153\x65\x79\56");
        e4:
        $Xg = $cu->getAlgorith();
        if ($wY->isEncrypted) {
            goto Vl;
        }
        $wW = $Qf->getAlgorith();
        if (!($Xg !== $wW)) {
            goto OS;
        }
        throw new Exception("\101\x6c\x67\157\x72\151\164\150\x6d\x20\x6d\151\x73\x6d\141\164\x63\x68\40\x62\x65\164\x77\x65\x65\x6e\x20\151\x6e\160\x75\164\x20\153\145\x79\40\x61\156\144\40\153\145\x79\40\151\x6e\40\x6d\145\x73\163\x61\147\x65\x2e\x20" . "\x4b\145\171\40\167\141\163\x3a\40" . var_export($Xg, TRUE) . "\x3b\x20\x6d\x65\x73\163\141\147\x65\40\167\141\x73\x3a\40" . var_export($wW, TRUE));
        OS:
        $Qf = $cu;
        goto uF;
        Vl:
        $Vd = $wY->getAlgorith();
        if (!in_array($Vd, $kZ, TRUE)) {
            goto g1;
        }
        throw new Exception("\x41\x6c\x67\x6f\162\x69\x74\x68\155\40\x64\151\x73\x61\142\154\145\x64\x3a\40" . var_export($Vd, TRUE));
        g1:
        if (!($Vd === XMLSecurityKey::RSA_OAEP_MGF1P && $Xg === XMLSecurityKey::RSA_1_5)) {
            goto Fv;
        }
        $Xg = XMLSecurityKey::RSA_OAEP_MGF1P;
        Fv:
        if (!($Xg !== $Vd)) {
            goto bs;
        }
        throw new Exception("\x41\154\147\157\x72\151\164\150\x6d\x20\x6d\x69\x73\x6d\141\x74\143\x68\40\142\145\x74\167\x65\145\156\x20\151\x6e\x70\165\164\x20\x6b\x65\171\40\x61\156\x64\x20\153\145\x79\x20\x75\163\145\144\x20\x74\157\x20\x65\x6e\x63\162\171\160\x74\x20" . "\x20\x74\150\x65\x20\x73\171\155\x6d\145\164\162\x69\x63\x20\x6b\x65\x79\x20\x66\x6f\162\40\x74\150\145\x20\155\145\163\163\x61\x67\x65\x2e\x20\113\x65\x79\40\167\141\x73\72\40" . var_export($Xg, TRUE) . "\73\40\155\x65\x73\163\x61\x67\145\40\167\x61\163\72\40" . var_export($Vd, TRUE));
        bs:
        $fz = $wY->encryptedCtx;
        $wY->key = $cu->key;
        $Zs = $Qf->getSymmetricKeySize();
        if (!($Zs === NULL)) {
            goto Ay;
        }
        throw new Exception("\125\x6e\153\x6e\157\167\x6e\40\x6b\145\171\x20\163\151\172\145\40\146\157\x72\x20\145\x6e\x63\x72\x79\x70\x74\151\157\156\x20\141\x6c\x67\157\x72\x69\164\150\155\x3a\x20" . var_export($Qf->type, TRUE));
        Ay:
        try {
            $w5 = $fz->decryptKey($wY);
            if (!(strlen($w5) != $Zs)) {
                goto ds;
            }
            throw new Exception("\x55\156\145\x78\160\x65\143\164\x65\x64\40\x6b\x65\171\x20\163\x69\x7a\145\x20\50" . strlen($w5) * 8 . "\x62\151\x74\x73\x29\40\146\157\x72\40\x65\x6e\x63\x72\x79\160\164\x69\157\156\x20\x61\x6c\147\157\162\x69\x74\150\x6d\72\40" . var_export($Qf->type, TRUE));
            ds:
        } catch (Exception $YR) {
            $CH = $fz->getCipherValue();
            $K2 = openssl_pkey_get_details($wY->key);
            $K2 = sha1(serialize($K2), TRUE);
            $w5 = sha1($CH . $K2, TRUE);
            if (strlen($w5) > $Zs) {
                goto fl;
            }
            if (strlen($w5) < $Zs) {
                goto Mt;
            }
            goto O4;
            fl:
            $w5 = substr($w5, 0, $Zs);
            goto O4;
            Mt:
            $w5 = str_pad($w5, $Zs);
            O4:
        }
        $Qf->loadkey($w5);
        uF:
        $Oe = $Qf->getAlgorith();
        if (!in_array($Oe, $kZ, TRUE)) {
            goto an;
        }
        throw new Exception("\x41\154\147\157\162\x69\164\x68\155\x20\144\x69\163\141\142\154\x65\144\72\40" . var_export($Oe, TRUE));
        an:
        $G7 = $fg->decryptNode($Qf, FALSE);
        $HR = "\74\x72\157\157\x74\40\170\x6d\x6c\x6e\x73\x3a\x73\141\155\x6c\75\x22\x75\x72\x6e\x3a\x6f\141\163\x69\163\72\156\x61\155\x65\x73\x3a\x74\x63\x3a\123\101\115\114\x3a\62\x2e\x30\x3a\141\x73\163\x65\x72\x74\151\157\156\x22\40" . "\170\x6d\x6c\x6e\163\x3a\170\x73\x69\75\x22\150\x74\164\x70\72\57\x2f\167\167\167\x2e\x77\63\56\x6f\x72\x67\x2f\x32\60\60\x31\57\130\x4d\114\x53\143\x68\145\x6d\x61\55\x69\x6e\x73\x74\x61\156\143\145\x22\x3e" . $G7 . "\74\x2f\162\157\157\164\76";
        $vW = new DOMDocument();
        if (@$vW->loadXML($HR)) {
            goto aU;
        }
        throw new Exception("\106\x61\151\154\145\144\40\x74\x6f\40\160\141\162\x73\145\40\144\145\x63\x72\x79\160\x74\145\144\40\x58\x4d\x4c\56\40\115\x61\x79\x62\145\x20\164\150\145\x20\167\x72\x6f\x6e\147\x20\x73\x68\x61\x72\x65\144\x6b\145\x79\x20\x77\x61\x73\x20\165\163\145\x64\77");
        aU:
        $xt = $vW->firstChild->firstChild;
        if (!($xt === NULL)) {
            goto bx;
        }
        throw new Exception("\115\x69\163\x73\x69\156\147\40\x65\x6e\x63\162\171\160\x74\145\144\x20\145\x6c\x65\155\x65\x6e\164\56");
        bx:
        if ($xt instanceof DOMElement) {
            goto iZ;
        }
        throw new Exception("\104\145\143\x72\171\x70\x74\145\x64\40\x65\154\x65\x6d\145\x6e\x74\x20\167\x61\163\x20\156\x6f\164\x20\141\143\164\165\x61\x6c\x6c\171\40\x61\x20\104\117\115\105\x6c\x65\x6d\145\x6e\164\x2e");
        iZ:
        return $xt;
    }
    public static function decryptElement(DOMElement $FN, XMLSecurityKey $cu, array $kZ = array(), XMLSecurityKey $Jm = NULL)
    {
        try {
            return self::doDecryptElement($FN, $cu, $kZ);
        } catch (Exception $YR) {
            try {
                return self::doDecryptElement($FN, $Jm, $kZ);
            } catch (Exception $Gg) {
                throw new Exception("\x46\x61\151\154\x65\x64\x20\164\157\40\144\x65\x63\162\x79\x70\x74\40\130\x4d\x4c\x20\145\154\145\x6d\x65\x6e\164\56");
            }
            throw new Exception("\106\141\x69\154\x65\144\x20\x74\157\x20\x64\x65\x63\162\x79\160\164\40\130\x4d\x4c\40\x65\x6c\x65\155\x65\x6e\164\56");
        }
    }
    public static function extractStrings(DOMElement $ak, $PU, $v1)
    {
        $ut = array();
        $uu = $ak->firstChild;
        Uq:
        if (!($uu !== NULL)) {
            goto Tb;
        }
        if (!($uu->namespaceURI !== $PU || $uu->localName !== $v1)) {
            goto bO;
        }
        goto GV;
        bO:
        $ut[] = trim($uu->textContent);
        GV:
        $uu = $uu->nextSibling;
        goto Uq;
        Tb:
        return $ut;
    }
    public static function validateElement(DOMElement $SM)
    {
        $zW = new XMLSecurityDSig();
        $zW->idKeys[] = "\x49\104";
        $jH = self::xpQuery($SM, "\x2e\57\144\x73\72\123\x69\147\x6e\141\x74\x75\162\x65");
        if (count($jH) === 0) {
            goto ce;
        }
        if (count($jH) > 1) {
            goto s1;
        }
        goto kx;
        ce:
        return FALSE;
        goto kx;
        s1:
        throw new Exception("\130\115\x4c\x53\x65\143\72\x20\x6d\x6f\162\145\40\x74\x68\141\156\x20\157\156\x65\x20\163\x69\147\x6e\141\x74\165\x72\x65\40\x65\154\145\x6d\x65\x6e\164\x20\x69\156\40\x72\x6f\x6f\x74\x2e");
        kx:
        $jH = $jH[0];
        $zW->sigNode = $jH;
        $zW->canonicalizeSignedInfo();
        if ($zW->validateReference()) {
            goto BX;
        }
        throw new Exception("\130\x4d\x4c\x73\x65\143\x3a\x20\144\x69\x67\x65\163\164\40\166\141\154\x69\x64\x61\x74\x69\x6f\x6e\40\x66\x61\x69\x6c\x65\x64");
        BX:
        $NW = FALSE;
        foreach ($zW->getValidatedNodes() as $jE) {
            if ($jE->isSameNode($SM)) {
                goto uf;
            }
            if ($SM->parentNode instanceof DOMDocument && $jE->isSameNode($SM->ownerDocument)) {
                goto DJ;
            }
            goto lK;
            uf:
            $NW = TRUE;
            goto Iv;
            goto lK;
            DJ:
            $NW = TRUE;
            goto Iv;
            lK:
            N3:
        }
        Iv:
        if ($NW) {
            goto q3;
        }
        throw new Exception("\x58\115\114\123\145\143\x3a\40\x54\x68\x65\x20\162\157\157\164\40\145\x6c\x65\x6d\x65\x6e\x74\40\x69\163\x20\x6e\x6f\164\40\x73\x69\147\156\145\144\x2e");
        q3:
        $Ro = array();
        foreach (self::xpQuery($jH, "\56\x2f\144\163\72\113\x65\x79\x49\156\146\157\x2f\144\163\72\130\x35\x30\x39\104\141\x74\x61\x2f\144\x73\x3a\130\65\x30\x39\x43\x65\162\x74\x69\x66\151\143\141\x74\x65") as $RE) {
            $Ex = trim($RE->textContent);
            $Ex = str_replace(array("\xd", "\12", "\11", "\x20"), '', $Ex);
            $Ro[] = $Ex;
            en:
        }
        Dc:
        $ut = array("\123\151\x67\156\x61\164\165\x72\145" => $zW, "\103\145\162\x74\151\x66\151\143\141\x74\145\163" => $Ro);
        return $ut;
    }
    public static function validateSignature(array $QL, XMLSecurityKey $w5)
    {
        $zW = $QL["\123\x69\147\156\x61\164\x75\162\x65"];
        $Ct = self::xpQuery($zW->sigNode, "\56\57\144\x73\72\123\x69\x67\156\x65\144\111\x6e\146\157\x2f\144\x73\x3a\123\151\x67\156\x61\164\165\162\145\115\x65\x74\150\x6f\x64");
        if (!empty($Ct)) {
            goto lD;
        }
        throw new Exception("\x4d\151\163\163\x69\x6e\x67\40\x53\151\x67\x6e\x61\x74\165\x72\145\115\145\x74\150\x6f\x64\40\145\x6c\145\x6d\145\x6e\x74");
        lD:
        $Ct = $Ct[0];
        if ($Ct->hasAttribute("\101\x6c\147\157\x72\151\164\x68\x6d")) {
            goto Pe;
        }
        throw new Exception("\x4d\x69\x73\x73\151\156\x67\40\101\154\147\x6f\x72\x69\x74\x68\155\55\141\164\164\x72\151\142\165\164\x65\x20\x6f\x6e\40\123\151\x67\156\x61\164\165\162\145\x4d\x65\x74\150\x6f\x64\40\145\154\x65\155\x65\156\x74\56");
        Pe:
        $HC = $Ct->getAttribute("\x41\x6c\x67\x6f\x72\151\164\150\155");
        if (!($w5->type === XMLSecurityKey::RSA_SHA256 && $HC !== $w5->type)) {
            goto SZ;
        }
        $w5 = self::castKey($w5, $HC);
        SZ:
        if ($zW->verify($w5)) {
            goto UD;
        }
        throw new Exception("\x55\156\141\142\x6c\145\x20\164\157\x20\166\141\x6c\151\x64\x61\164\145\40\123\x67\x6e\141\x74\x75\162\145");
        UD:
    }
    public static function castKey(XMLSecurityKey $w5, $Oe, $yf = "\160\x75\142\x6c\x69\x63")
    {
        if (!($w5->type === $Oe)) {
            goto Q6;
        }
        return $w5;
        Q6:
        $s2 = openssl_pkey_get_details($w5->key);
        if (!($s2 === FALSE)) {
            goto vh;
        }
        throw new Exception("\125\156\141\142\x6c\145\40\x74\157\40\147\145\x74\x20\x6b\145\x79\40\144\x65\x74\141\151\x6c\x73\40\x66\x72\157\x6d\40\x58\115\114\123\145\x63\x75\162\151\x74\171\113\x65\x79\56");
        vh:
        if (isset($s2["\153\x65\x79"])) {
            goto zH;
        }
        throw new Exception("\115\151\x73\x73\x69\x6e\147\x20\x6b\x65\x79\40\x69\x6e\x20\x70\x75\142\154\151\x63\x20\x6b\x65\171\40\x64\x65\x74\x61\x69\154\x73\x2e");
        zH:
        $l3 = new XMLSecurityKey($Oe, array("\x74\171\x70\x65" => $yf));
        $l3->loadKey($s2["\x6b\x65\171"]);
        return $l3;
    }
    public static function processResponse($yo, $dc)
    {
        $tq = self::checkSign($yo, $dc);
        return $tq;
    }
    public static function checkSign($yo, $dc)
    {
        $Ro = $dc["\103\x65\x72\x74\x69\x66\151\x63\141\164\145\163"];
        if (!(count($Ro) === 0)) {
            goto dU;
        }
        return FALSE;
        dU:
        $o8 = array();
        $o8[] = $yo;
        $QT = self::findCertificate($o8, $Ro);
        if (!($QT === FALSE)) {
            goto yX;
        }
        return FALSE;
        yX:
        $hI = NULL;
        $w5 = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array("\164\x79\160\145" => "\160\x75\142\x6c\x69\143"));
        $w5->loadKey($QT);
        try {
            self::validateSignature($dc, $w5);
            return TRUE;
        } catch (Exception $YR) {
            $hI = $YR;
        }
        if ($hI !== NULL) {
            goto ip;
        }
        return FALSE;
        goto W1;
        ip:
        throw $hI;
        W1:
    }
    private static function findCertificate(array $ub, array $Ro)
    {
        $B4 = array();
        foreach ($Ro as $WZ) {
            $eh = strtolower(sha1(base64_decode($WZ)));
            if (in_array($eh, $ub, TRUE)) {
                goto xo;
            }
            $B4[] = $eh;
            goto ZA;
            xo:
            $eP = "\x2d\55\55\55\55\102\x45\x47\x49\x4e\40\x43\x45\122\x54\x49\x46\x49\x43\101\124\x45\55\x2d\55\55\55\12" . chunk_split($WZ, 64) . "\x2d\55\55\x2d\55\x45\116\x44\40\103\x45\x52\124\111\106\x49\103\101\124\105\55\55\55\x2d\55\12";
            return $eP;
            ZA:
        }
        M3:
        return FALSE;
    }
    public static function parseBoolean(DOMElement $uu, $TS, $nq = null)
    {
        if ($uu->hasAttribute($TS)) {
            goto mZ;
        }
        return $nq;
        mZ:
        $yY = $uu->getAttribute($TS);
        switch (strtolower($yY)) {
            case "\60":
            case "\146\x61\154\x73\x65":
                return false;
            case "\x31":
            case "\x74\x72\165\x65":
                return true;
            default:
                throw new Exception("\111\x6e\x76\141\x6c\x69\144\40\166\141\x6c\165\x65\40\157\x66\40\x62\x6f\157\154\145\141\x6e\x20\x61\x74\164\162\x69\x62\x75\164\x65\x20" . var_export($TS, true) . "\72\40" . var_export($yY, true));
        }
        fa:
        O1:
    }
    public static function insertSignature(XMLSecurityKey $w5, array $Ro, DOMElement $SM, DomNode $pP = NULL)
    {
        $zW = new XMLSecurityDSig();
        $zW->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        switch ($w5->type) {
            case XMLSecurityKey::RSA_SHA256:
                $yf = XMLSecurityDSig::SHA256;
                goto k3;
            case XMLSecurityKey::RSA_SHA384:
                $yf = XMLSecurityDSig::SHA384;
                goto k3;
            case XMLSecurityKey::RSA_SHA512:
                $yf = XMLSecurityDSig::SHA512;
                goto k3;
            default:
                $yf = XMLSecurityDSig::SHA1;
        }
        Co:
        k3:
        $zW->addReferenceList(array($SM), $yf, array("\x68\164\x74\160\72\57\57\167\167\x77\56\x77\63\56\x6f\162\x67\x2f\x32\60\60\60\x2f\x30\x39\57\170\155\x6c\x64\x73\x69\x67\43\145\156\166\x65\x6c\x6f\160\x65\x64\x2d\x73\151\147\x6e\x61\x74\x75\x72\145", XMLSecurityDSig::EXC_C14N), array("\x69\144\137\156\x61\155\145" => "\111\104", "\157\166\145\162\167\x72\x69\164\x65" => FALSE));
        $zW->sign($w5);
        foreach ($Ro as $v0) {
            $zW->add509Cert($v0, TRUE);
            Fd:
        }
        YH:
        $zW->insertSignature($SM, $pP);
    }
    public static function signXML($HR, $l5, $ax, $gF = '')
    {
        $qe = array("\164\x79\x70\x65" => "\160\x72\151\166\141\x74\145");
        $w5 = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $qe);
        $w5->loadKey($ax);
        $iw = new DOMDocument();
        $iw->loadXML($HR);
        $mg = $iw->firstChild;
        if (!empty($gF)) {
            goto ey;
        }
        self::insertSignature($w5, array($l5), $mg);
        goto Kt;
        ey:
        $FT = $iw->getElementsByTagName($gF)->item(0);
        self::insertSignature($w5, array($l5), $mg, $FT);
        Kt:
        $HE = $mg->ownerDocument->saveXML($mg);
        return $HE;
    }
    public static function getEncryptionAlgorithm($Ry)
    {
        switch ($Ry) {
            case "\150\x74\x74\x70\x3a\57\x2f\167\x77\x77\x2e\x77\x33\x2e\x6f\x72\x67\57\x32\x30\x30\x31\x2f\60\64\57\170\x6d\154\x65\x6e\143\x23\x74\162\x69\x70\154\x65\x64\x65\163\55\143\142\143":
                return XMLSecurityKey::TRIPLEDES_CBC;
            case "\150\x74\164\x70\x3a\x2f\57\x77\167\167\56\x77\63\x2e\157\162\147\57\x32\x30\x30\61\57\60\64\x2f\x78\x6d\154\145\x6e\x63\x23\x61\145\x73\61\x32\x38\x2d\x63\142\143":
                return XMLSecurityKey::AES128_CBC;
            case "\150\164\164\x70\72\x2f\x2f\167\167\x77\56\x77\x33\x2e\157\x72\147\57\x32\60\x30\x31\x2f\x30\64\57\x78\155\x6c\145\156\x63\x23\141\145\x73\61\71\x32\x2d\x63\x62\143":
                return XMLSecurityKey::AES192_CBC;
            case "\150\164\164\160\x3a\x2f\x2f\x77\x77\167\56\167\x33\56\x6f\x72\x67\x2f\62\x30\60\x31\x2f\60\64\x2f\x78\155\154\145\x6e\x63\43\141\x65\x73\62\x35\66\x2d\143\142\x63":
                return XMLSecurityKey::AES256_CBC;
            case "\150\164\164\x70\72\x2f\57\167\x77\x77\x2e\167\x33\56\x6f\x72\x67\57\62\60\60\x31\x2f\x30\x34\x2f\x78\x6d\x6c\145\x6e\143\x23\x72\163\x61\55\61\x5f\x35":
                return XMLSecurityKey::RSA_1_5;
            case "\150\164\164\160\x3a\x2f\x2f\167\167\167\56\x77\63\x2e\x6f\x72\x67\57\62\x30\60\61\57\60\x34\x2f\170\155\154\x65\x6e\143\43\x72\x73\x61\x2d\157\141\145\x70\55\x6d\x67\x66\x31\x70":
                return XMLSecurityKey::RSA_OAEP_MGF1P;
            case "\x68\x74\x74\x70\x3a\x2f\57\x77\167\x77\x2e\x77\63\56\157\x72\147\x2f\62\60\60\x30\x2f\60\71\x2f\x78\155\154\x64\x73\x69\x67\x23\144\163\141\x2d\x73\x68\x61\61":
                return XMLSecurityKey::DSA_SHA1;
            case "\x68\164\164\x70\72\x2f\x2f\167\x77\x77\56\x77\x33\56\x6f\162\x67\57\x32\60\60\60\x2f\60\71\x2f\x78\155\x6c\x64\x73\151\x67\43\x72\163\141\x2d\163\x68\x61\x31":
                return XMLSecurityKey::RSA_SHA1;
            case "\150\x74\164\160\72\57\x2f\x77\167\167\x2e\167\x33\x2e\x6f\162\147\57\62\60\x30\61\x2f\x30\64\x2f\x78\155\154\144\x73\151\147\55\155\157\162\x65\x23\x72\163\141\55\x73\150\141\62\x35\66":
                return XMLSecurityKey::RSA_SHA256;
            case "\x68\x74\x74\160\72\57\57\x77\x77\167\56\167\63\56\x6f\162\147\57\62\60\60\x31\57\60\64\x2f\x78\x6d\x6c\x64\163\151\x67\x2d\155\157\162\x65\x23\162\163\x61\x2d\163\150\141\x33\x38\x34":
                return XMLSecurityKey::RSA_SHA384;
            case "\150\x74\164\x70\x3a\x2f\57\x77\x77\x77\x2e\167\x33\56\157\x72\147\x2f\62\60\60\x31\57\60\64\57\x78\x6d\154\x64\x73\x69\x67\x2d\155\157\x72\x65\43\x72\163\x61\x2d\x73\150\141\x35\x31\62":
                return XMLSecurityKey::RSA_SHA512;
            default:
                throw new Exception("\x49\156\x76\141\154\151\144\x20\x45\156\143\x72\171\160\x74\x69\157\x6e\40\x4d\x65\164\x68\157\144\x3a\40" . $Ry);
        }
        A4:
        oU:
    }
    public static function sanitize_certificate($v0)
    {
        $v0 = preg_replace("\57\x5b\xd\12\135\x2b\x2f", '', $v0);
        $v0 = str_replace("\x2d", '', $v0);
        $v0 = str_replace("\x42\x45\107\x49\116\40\x43\x45\122\x54\x49\x46\111\103\x41\x54\105", '', $v0);
        $v0 = str_replace("\105\x4e\104\x20\x43\x45\x52\x54\111\x46\111\x43\101\124\105", '', $v0);
        $v0 = str_replace("\40", '', $v0);
        $v0 = chunk_split($v0, 64, "\xd\12");
        $v0 = "\x2d\55\55\55\55\102\x45\107\111\x4e\40\103\105\x52\124\111\106\111\x43\x41\124\x45\x2d\x2d\x2d\55\55\15\xa" . $v0 . "\x2d\55\x2d\x2d\x2d\105\116\x44\40\x43\105\x52\x54\111\106\x49\103\101\x54\105\55\55\55\55\x2d";
        return $v0;
    }
    public static function desanitize_certificate($v0)
    {
        $v0 = preg_replace("\57\133\15\12\x5d\x2b\x2f", '', $v0);
        $v0 = str_replace("\x2d\55\55\55\55\102\x45\x47\111\116\x20\103\x45\x52\x54\x49\x46\x49\x43\101\x54\x45\55\x2d\55\x2d\55", '', $v0);
        $v0 = str_replace("\x2d\x2d\55\55\55\105\x4e\x44\x20\103\105\122\x54\111\x46\111\103\x41\x54\105\x2d\x2d\x2d\55\55", '', $v0);
        $v0 = str_replace("\x20", '', $v0);
        return $v0;
    }
    public static function generateRandomAlphanumericValue($x7)
    {
        $Qn = "\141\x62\143\144\145\146\60\x31\62\x33\x34\x35\66\67\x38\71";
        $KW = strlen($Qn);
        $pA = '';
        $Hz = 0;
        Lk:
        if (!($Hz < $x7)) {
            goto IG;
        }
        $pA .= substr($Qn, rand(0, 15), 1);
        yf:
        $Hz++;
        goto Lk;
        IG:
        return "\x61" . $pA;
    }
}
