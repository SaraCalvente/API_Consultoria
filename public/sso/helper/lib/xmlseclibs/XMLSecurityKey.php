<?php


namespace MiniOrange\Helper\Lib\XMLSecLibs;

use DOMElement;
use Exception;
class XMLSecurityKey
{
    const TRIPLEDES_CBC = "\150\x74\164\160\72\57\x2f\x77\167\x77\x2e\167\63\x2e\x6f\162\147\x2f\x32\x30\x30\61\57\x30\64\57\170\x6d\x6c\145\156\x63\x23\164\162\x69\x70\154\145\144\x65\x73\55\143\x62\x63";
    const AES128_CBC = "\150\x74\164\160\72\57\x2f\167\167\167\x2e\167\63\56\x6f\x72\x67\x2f\62\x30\x30\x31\x2f\x30\64\57\x78\155\x6c\x65\x6e\143\x23\x61\145\163\61\x32\70\x2d\x63\x62\x63";
    const AES192_CBC = "\x68\x74\x74\160\x3a\57\57\167\167\167\56\x77\63\56\157\162\147\x2f\x32\x30\60\x31\57\x30\x34\57\170\x6d\x6c\x65\156\143\x23\141\x65\x73\x31\x39\x32\55\143\x62\143";
    const AES256_CBC = "\x68\x74\x74\x70\x3a\x2f\57\x77\167\x77\x2e\167\63\56\x6f\x72\x67\57\x32\x30\60\61\57\x30\64\57\170\x6d\154\x65\156\143\43\x61\145\163\x32\x35\x36\55\143\142\143";
    const RSA_1_5 = "\150\164\x74\160\72\57\57\x77\x77\x77\x2e\167\x33\56\157\x72\x67\x2f\x32\60\x30\x31\57\60\64\x2f\x78\155\154\x65\x6e\x63\43\162\x73\141\55\x31\x5f\x35";
    const RSA_OAEP_MGF1P = "\x68\x74\x74\x70\72\x2f\57\x77\x77\x77\x2e\167\63\x2e\x6f\x72\x67\57\62\x30\x30\61\57\x30\x34\x2f\170\x6d\154\145\156\x63\43\162\163\141\x2d\x6f\141\x65\x70\x2d\155\x67\146\x31\x70";
    const DSA_SHA1 = "\x68\164\164\x70\x3a\x2f\x2f\167\167\167\56\x77\x33\56\x6f\x72\147\x2f\62\60\60\x30\x2f\60\x39\57\x78\x6d\154\144\163\x69\147\43\x64\163\141\x2d\x73\150\x61\61";
    const RSA_SHA1 = "\150\164\164\160\x3a\x2f\x2f\x77\x77\x77\56\x77\x33\x2e\157\162\147\x2f\62\60\x30\60\57\60\71\x2f\x78\155\154\x64\x73\151\147\43\162\163\x61\x2d\163\x68\x61\61";
    const RSA_SHA256 = "\x68\164\x74\x70\72\x2f\x2f\x77\167\x77\x2e\167\x33\x2e\157\162\x67\x2f\62\60\x30\x31\x2f\60\x34\57\170\x6d\154\144\x73\151\147\55\x6d\x6f\x72\145\43\162\163\x61\55\163\x68\141\62\x35\66";
    const RSA_SHA384 = "\150\164\x74\x70\x3a\x2f\x2f\167\167\167\x2e\x77\63\x2e\x6f\162\x67\x2f\62\60\60\61\57\x30\64\57\x78\155\x6c\x64\163\151\147\55\x6d\x6f\162\145\x23\162\x73\141\55\x73\x68\141\x33\x38\64";
    const RSA_SHA512 = "\x68\x74\x74\x70\72\x2f\57\x77\x77\x77\56\x77\x33\56\157\x72\x67\57\x32\60\x30\61\x2f\x30\64\x2f\170\x6d\154\x64\163\x69\x67\55\x6d\x6f\162\145\x23\x72\x73\141\55\x73\150\141\65\x31\62";
    const HMAC_SHA1 = "\x68\164\164\160\72\x2f\x2f\167\x77\x77\56\x77\x33\x2e\157\162\x67\x2f\62\x30\x30\x30\57\60\71\57\x78\x6d\154\x64\163\x69\147\x23\150\x6d\x61\143\55\163\150\x61\x31";
    private $cryptParams = array();
    public $type = 0;
    public $key = null;
    public $passphrase = '';
    public $iv = null;
    public $name = null;
    public $keyChain = null;
    public $isEncrypted = false;
    public $encryptedCtx = null;
    public $guid = null;
    private $x509Certificate = null;
    private $X509Thumbprint = null;
    public function __construct($yf, $Jr = null)
    {
        switch ($yf) {
            case self::TRIPLEDES_CBC:
                $this->cryptParams["\154\151\x62\x72\141\x72\x79"] = "\157\160\x65\x6e\x73\x73\x6c";
                $this->cryptParams["\x63\151\x70\x68\x65\x72"] = "\x64\x65\163\55\145\x64\145\63\x2d\x63\142\x63";
                $this->cryptParams["\x74\171\160\x65"] = "\x73\x79\x6d\155\145\164\162\x69\143";
                $this->cryptParams["\155\x65\164\150\157\144"] = "\x68\164\x74\x70\x3a\x2f\x2f\x77\167\167\56\x77\x33\x2e\157\162\147\x2f\62\60\60\x31\x2f\60\x34\x2f\170\155\x6c\145\156\143\43\164\x72\151\x70\x6c\x65\x64\145\163\55\x63\142\x63";
                $this->cryptParams["\153\145\171\x73\x69\x7a\145"] = 24;
                $this->cryptParams["\x62\x6c\157\143\153\163\151\172\x65"] = 8;
                goto un;
            case self::AES128_CBC:
                $this->cryptParams["\x6c\x69\142\162\141\x72\171"] = "\157\x70\x65\156\x73\163\x6c";
                $this->cryptParams["\x63\x69\x70\150\145\x72"] = "\x61\145\x73\x2d\x31\x32\x38\x2d\143\142\x63";
                $this->cryptParams["\164\x79\160\x65"] = "\163\x79\x6d\155\145\x74\x72\151\x63";
                $this->cryptParams["\x6d\145\x74\x68\157\144"] = "\x68\164\x74\160\72\57\57\x77\x77\167\56\167\63\x2e\x6f\x72\x67\x2f\x32\60\x30\61\57\x30\64\x2f\x78\155\x6c\145\x6e\x63\x23\141\x65\163\x31\x32\x38\55\x63\142\x63";
                $this->cryptParams["\x6b\x65\171\163\x69\x7a\x65"] = 16;
                $this->cryptParams["\x62\154\x6f\x63\x6b\163\x69\172\x65"] = 16;
                goto un;
            case self::AES192_CBC:
                $this->cryptParams["\154\x69\142\162\x61\162\x79"] = "\157\160\x65\x6e\x73\x73\154";
                $this->cryptParams["\143\151\x70\150\x65\x72"] = "\141\x65\x73\55\61\71\x32\x2d\143\x62\x63";
                $this->cryptParams["\x74\171\160\145"] = "\x73\171\x6d\155\145\164\162\151\143";
                $this->cryptParams["\x6d\x65\164\150\x6f\x64"] = "\x68\164\x74\160\72\57\57\x77\167\167\x2e\x77\63\56\x6f\x72\147\x2f\x32\x30\60\x31\57\60\x34\57\x78\155\154\145\x6e\143\x23\x61\x65\x73\x31\71\62\55\143\x62\x63";
                $this->cryptParams["\x6b\145\x79\163\151\x7a\x65"] = 24;
                $this->cryptParams["\x62\154\157\143\153\163\151\172\x65"] = 16;
                goto un;
            case self::AES256_CBC:
                $this->cryptParams["\154\x69\142\x72\x61\x72\171"] = "\x6f\160\x65\x6e\163\163\154";
                $this->cryptParams["\x63\151\x70\150\x65\x72"] = "\141\145\x73\55\x32\65\x36\55\x63\142\143";
                $this->cryptParams["\x74\171\160\145"] = "\x73\171\155\x6d\145\164\x72\x69\143";
                $this->cryptParams["\x6d\x65\164\150\157\x64"] = "\150\x74\x74\x70\72\x2f\x2f\167\167\167\x2e\x77\63\56\x6f\x72\147\x2f\x32\x30\x30\61\57\60\x34\57\x78\x6d\x6c\145\156\x63\x23\141\x65\x73\62\x35\x36\55\143\142\x63";
                $this->cryptParams["\153\145\171\163\151\x7a\145"] = 32;
                $this->cryptParams["\142\x6c\x6f\143\x6b\163\x69\172\x65"] = 16;
                goto un;
            case self::RSA_1_5:
                $this->cryptParams["\154\x69\x62\162\x61\x72\x79"] = "\x6f\x70\x65\x6e\x73\x73\x6c";
                $this->cryptParams["\160\141\144\144\x69\156\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x6d\x65\164\x68\x6f\x64"] = "\x68\164\164\x70\72\x2f\57\167\x77\167\56\x77\x33\56\157\x72\x67\x2f\62\x30\60\61\x2f\60\x34\57\170\155\154\x65\x6e\x63\43\162\x73\141\x2d\x31\137\65";
                if (!(is_array($Jr) && !empty($Jr["\164\x79\160\145"]))) {
                    goto FL;
                }
                if (!($Jr["\x74\171\x70\x65"] == "\160\x75\x62\154\x69\x63" || $Jr["\x74\171\x70\145"] == "\160\162\x69\166\x61\x74\145")) {
                    goto Nt;
                }
                $this->cryptParams["\164\x79\x70\145"] = $Jr["\x74\x79\x70\145"];
                goto un;
                Nt:
                FL:
                throw new Exception("\103\x65\x72\x74\151\146\151\143\141\x74\x65\x20\x22\x74\x79\x70\x65\x22\x20\50\160\x72\x69\x76\141\x74\x65\x2f\x70\x75\x62\x6c\x69\x63\51\x20\x6d\165\163\x74\x20\142\145\x20\x70\141\x73\x73\x65\144\x20\x76\x69\x61\x20\x70\141\x72\141\155\x65\x74\x65\162\163");
            case self::RSA_OAEP_MGF1P:
                $this->cryptParams["\154\x69\142\x72\141\x72\171"] = "\x6f\160\x65\156\x73\163\x6c";
                $this->cryptParams["\x70\141\144\144\151\156\147"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\x65\x74\150\157\x64"] = "\x68\x74\x74\x70\72\x2f\57\x77\x77\x77\x2e\x77\63\56\157\x72\147\57\62\x30\x30\61\57\x30\x34\x2f\170\155\x6c\x65\x6e\143\x23\162\x73\x61\x2d\x6f\x61\145\160\55\x6d\147\x66\x31\x70";
                $this->cryptParams["\150\141\163\x68"] = null;
                if (!(is_array($Jr) && !empty($Jr["\164\x79\x70\x65"]))) {
                    goto jB;
                }
                if (!($Jr["\164\x79\160\x65"] == "\x70\165\x62\x6c\151\x63" || $Jr["\164\171\x70\x65"] == "\160\x72\151\x76\141\164\145")) {
                    goto Zl;
                }
                $this->cryptParams["\x74\171\160\x65"] = $Jr["\164\x79\160\145"];
                goto un;
                Zl:
                jB:
                throw new Exception("\x43\x65\162\164\151\x66\151\x63\141\x74\145\x20\42\x74\x79\x70\145\x22\40\x28\x70\x72\x69\166\x61\x74\145\x2f\160\165\142\x6c\151\143\51\x20\x6d\165\x73\x74\x20\142\x65\40\160\141\163\163\x65\x64\40\x76\151\x61\x20\x70\141\x72\141\x6d\145\164\145\x72\x73");
            case self::RSA_SHA1:
                $this->cryptParams["\154\x69\x62\x72\x61\162\x79"] = "\157\x70\x65\156\163\x73\154";
                $this->cryptParams["\x6d\x65\164\x68\157\x64"] = "\x68\x74\164\160\72\x2f\x2f\x77\167\x77\56\167\x33\x2e\157\x72\147\57\62\60\x30\60\57\x30\x39\57\x78\155\x6c\144\x73\151\147\43\162\163\141\x2d\x73\x68\141\61";
                $this->cryptParams["\x70\141\x64\x64\151\156\x67"] = OPENSSL_PKCS1_PADDING;
                if (!(is_array($Jr) && !empty($Jr["\164\171\x70\145"]))) {
                    goto B0;
                }
                if (!($Jr["\x74\171\160\145"] == "\x70\165\142\x6c\x69\x63" || $Jr["\x74\171\160\x65"] == "\x70\162\x69\166\x61\x74\145")) {
                    goto X8;
                }
                $this->cryptParams["\164\x79\160\145"] = $Jr["\164\171\x70\x65"];
                goto un;
                X8:
                B0:
                throw new Exception("\103\145\x72\164\x69\146\151\x63\141\164\145\x20\x22\x74\171\x70\145\x22\x20\x28\160\x72\151\166\x61\164\145\x2f\x70\165\x62\x6c\151\x63\51\x20\x6d\165\163\164\x20\142\x65\x20\x70\x61\x73\163\145\144\x20\x76\x69\141\40\160\141\162\x61\x6d\x65\164\x65\x72\163");
            case self::RSA_SHA256:
                $this->cryptParams["\x6c\x69\142\162\141\162\171"] = "\157\160\x65\156\163\163\x6c";
                $this->cryptParams["\155\x65\x74\x68\157\144"] = "\x68\164\164\160\x3a\x2f\57\167\167\167\56\x77\63\56\x6f\x72\x67\57\62\x30\x30\61\x2f\x30\x34\57\170\x6d\x6c\144\x73\151\147\x2d\x6d\x6f\162\145\x23\x72\x73\141\x2d\163\x68\141\x32\65\x36";
                $this->cryptParams["\x70\141\144\x64\151\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\151\x67\x65\x73\164"] = "\x53\110\x41\62\65\66";
                if (!(is_array($Jr) && !empty($Jr["\164\171\x70\x65"]))) {
                    goto Dk;
                }
                if (!($Jr["\x74\171\160\x65"] == "\160\165\142\154\x69\x63" || $Jr["\x74\x79\160\x65"] == "\x70\x72\151\x76\x61\x74\145")) {
                    goto rN;
                }
                $this->cryptParams["\164\x79\x70\145"] = $Jr["\164\171\160\145"];
                goto un;
                rN:
                Dk:
                throw new Exception("\103\x65\162\164\x69\146\x69\143\141\x74\145\x20\x22\164\171\160\145\42\x20\50\160\162\151\x76\x61\x74\x65\x2f\x70\165\x62\154\x69\143\x29\x20\155\165\163\x74\x20\142\x65\x20\x70\x61\x73\x73\145\x64\x20\x76\151\141\x20\160\141\162\141\x6d\x65\x74\145\x72\163");
            case self::RSA_SHA384:
                $this->cryptParams["\154\151\x62\x72\141\x72\x79"] = "\x6f\160\x65\x6e\x73\163\x6c";
                $this->cryptParams["\x6d\x65\164\x68\x6f\144"] = "\150\164\x74\x70\x3a\x2f\x2f\x77\167\167\56\167\63\x2e\x6f\x72\x67\x2f\62\x30\60\x31\57\60\64\57\x78\x6d\154\x64\x73\x69\x67\55\155\x6f\x72\145\x23\162\163\141\55\163\x68\x61\x33\70\x34";
                $this->cryptParams["\x70\x61\x64\x64\x69\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\x69\x67\x65\x73\x74"] = "\123\x48\x41\x33\x38\x34";
                if (!(is_array($Jr) && !empty($Jr["\x74\x79\x70\145"]))) {
                    goto fV;
                }
                if (!($Jr["\x74\x79\x70\145"] == "\x70\x75\142\154\151\x63" || $Jr["\164\171\160\x65"] == "\x70\162\151\166\x61\x74\x65")) {
                    goto G_;
                }
                $this->cryptParams["\x74\x79\160\145"] = $Jr["\x74\x79\160\x65"];
                goto un;
                G_:
                fV:
                throw new Exception("\x43\x65\162\x74\151\x66\151\x63\x61\x74\x65\x20\42\164\x79\x70\x65\42\40\50\x70\162\151\166\141\164\145\x2f\160\x75\142\154\x69\143\x29\40\155\x75\163\164\x20\142\x65\x20\160\141\163\x73\145\144\x20\x76\151\141\x20\160\x61\162\x61\155\x65\164\x65\162\x73");
            case self::RSA_SHA512:
                $this->cryptParams["\154\151\142\x72\141\x72\x79"] = "\157\160\145\x6e\x73\163\154";
                $this->cryptParams["\x6d\145\164\150\157\x64"] = "\x68\164\164\160\x3a\x2f\x2f\x77\x77\167\x2e\x77\x33\56\x6f\162\x67\x2f\62\60\60\61\x2f\60\64\x2f\x78\155\154\144\x73\151\147\x2d\x6d\157\162\x65\43\162\x73\141\x2d\163\x68\x61\65\x31\x32";
                $this->cryptParams["\160\141\144\144\151\156\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\151\147\x65\x73\164"] = "\123\110\101\65\x31\x32";
                if (!(is_array($Jr) && !empty($Jr["\164\x79\160\x65"]))) {
                    goto mO;
                }
                if (!($Jr["\x74\x79\x70\145"] == "\160\x75\142\154\151\x63" || $Jr["\164\171\x70\145"] == "\160\162\151\166\x61\164\145")) {
                    goto u_;
                }
                $this->cryptParams["\164\x79\160\x65"] = $Jr["\164\171\160\x65"];
                goto un;
                u_:
                mO:
                throw new Exception("\x43\x65\162\164\151\146\x69\143\141\x74\x65\40\x22\x74\171\x70\x65\x22\x20\x28\160\162\151\166\141\x74\x65\x2f\x70\x75\x62\154\151\x63\x29\x20\155\x75\x73\164\40\x62\145\x20\160\x61\163\x73\145\144\40\x76\x69\x61\x20\160\x61\162\x61\155\x65\164\145\x72\x73");
            case self::HMAC_SHA1:
                $this->cryptParams["\154\151\x62\x72\x61\162\171"] = $yf;
                $this->cryptParams["\155\x65\164\x68\157\144"] = "\x68\x74\x74\160\72\x2f\57\x77\167\167\56\x77\63\56\157\162\x67\57\62\60\60\x30\x2f\60\x39\x2f\170\155\154\x64\163\x69\147\x23\x68\155\141\x63\x2d\x73\150\x61\x31";
                goto un;
            default:
                throw new Exception("\111\156\166\x61\x6c\x69\x64\x20\113\145\x79\40\x54\x79\x70\145");
        }
        b2:
        un:
        $this->type = $yf;
    }
    public function getSymmetricKeySize()
    {
        if (isset($this->cryptParams["\x6b\145\171\163\x69\172\x65"])) {
            goto RM;
        }
        return null;
        RM:
        return $this->cryptParams["\x6b\145\x79\x73\151\172\145"];
    }
    public function generateSessionKey()
    {
        if (isset($this->cryptParams["\x6b\x65\171\163\x69\172\x65"])) {
            goto XA;
        }
        throw new Exception("\125\x6e\x6b\156\x6f\167\156\40\153\x65\x79\40\x73\151\172\x65\x20\x66\x6f\162\x20\x74\171\x70\145\40\x22" . $this->type . "\42\x2e");
        XA:
        $PZ = $this->cryptParams["\x6b\x65\171\163\x69\x7a\x65"];
        $w5 = openssl_random_pseudo_bytes($PZ);
        if (!($this->type === self::TRIPLEDES_CBC)) {
            goto NW;
        }
        $Hz = 0;
        W5:
        if (!($Hz < strlen($w5))) {
            goto MO;
        }
        $XL = ord($w5[$Hz]) & 254;
        $aZ = 1;
        $dv = 1;
        QU:
        if (!($dv < 8)) {
            goto QQ;
        }
        $aZ ^= $XL >> $dv & 1;
        xx:
        $dv++;
        goto QU;
        QQ:
        $XL |= $aZ;
        $w5[$Hz] = chr($XL);
        pW:
        $Hz++;
        goto W5;
        MO:
        NW:
        $this->key = $w5;
        return $w5;
    }
    public static function getRawThumbprint($WZ)
    {
        $B3 = explode("\12", $WZ);
        $L2 = '';
        $GR = false;
        foreach ($B3 as $I5) {
            if (!$GR) {
                goto lV;
            }
            if (!(strncmp($I5, "\x2d\x2d\x2d\x2d\x2d\x45\116\104\40\103\x45\122\x54\111\x46\111\x43\101\x54\x45", 20) == 0)) {
                goto TU;
            }
            goto d9;
            TU:
            $L2 .= trim($I5);
            goto B6;
            lV:
            if (!(strncmp($I5, "\55\x2d\55\55\55\102\105\107\111\x4e\40\x43\x45\122\x54\x49\106\111\103\101\x54\x45", 22) == 0)) {
                goto Ea;
            }
            $GR = true;
            Ea:
            B6:
            L6:
        }
        d9:
        if (empty($L2)) {
            goto wk;
        }
        return strtolower(sha1(base64_decode($L2)));
        wk:
        return null;
    }
    public function loadKey($w5, $ZB = false, $Yp = false)
    {
        if ($ZB) {
            goto qb;
        }
        $this->key = $w5;
        goto qn;
        qb:
        $this->key = file_get_contents($w5);
        qn:
        if ($Yp) {
            goto wQ;
        }
        $this->x509Certificate = null;
        goto Un;
        wQ:
        $this->key = openssl_x509_read($this->key);
        openssl_x509_export($this->key, $C0);
        $this->x509Certificate = $C0;
        $this->key = $C0;
        Un:
        if (!($this->cryptParams["\154\x69\142\x72\141\162\x79"] == "\157\160\x65\156\x73\163\x6c")) {
            goto gR;
        }
        switch ($this->cryptParams["\x74\x79\160\145"]) {
            case "\x70\165\142\x6c\151\x63":
                if (!$Yp) {
                    goto rx;
                }
                $this->X509Thumbprint = self::getRawThumbprint($this->key);
                rx:
                $this->key = openssl_get_publickey($this->key);
                if ($this->key) {
                    goto kc;
                }
                throw new Exception("\125\156\x61\142\x6c\145\40\x74\157\40\x65\x78\x74\x72\x61\143\164\40\160\165\142\x6c\x69\143\x20\x6b\x65\x79");
                kc:
                goto T1;
            case "\x70\x72\x69\x76\x61\x74\145":
                $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                goto T1;
            case "\163\x79\155\x6d\145\164\x72\151\143":
                if (!(strlen($this->key) < $this->cryptParams["\x6b\x65\171\x73\151\x7a\145"])) {
                    goto t_;
                }
                throw new Exception("\x4b\145\x79\x20\x6d\165\x73\x74\40\x63\157\x6e\164\141\151\156\40\x61\x74\40\x6c\145\x61\163\164\x20\62\x35\40\x63\150\x61\x72\141\143\x74\145\162\x73\40\146\157\x72\40\x74\150\x69\x73\40\143\x69\160\150\145\x72");
                t_:
                goto T1;
            default:
                throw new Exception("\125\x6e\153\156\x6f\x77\x6e\x20\x74\x79\160\145");
        }
        lx:
        T1:
        gR:
    }
    private function padISO10126($L2, $wx)
    {
        if (!($wx > 256)) {
            goto KM;
        }
        throw new Exception("\x42\154\157\x63\x6b\x20\x73\151\172\145\x20\150\x69\147\150\x65\x72\x20\164\150\x61\x6e\40\x32\x35\x36\x20\156\157\x74\x20\x61\x6c\x6c\x6f\x77\145\144");
        KM:
        $Qt = $wx - strlen($L2) % $wx;
        $mG = chr($Qt);
        return $L2 . str_repeat($mG, $Qt);
    }
    private function unpadISO10126($L2)
    {
        $Qt = substr($L2, -1);
        $n1 = ord($Qt);
        return substr($L2, 0, -$n1);
    }
    private function encryptSymmetric($L2)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cryptParams["\143\x69\160\150\x65\x72"]));
        $L2 = $this->padISO10126($L2, $this->cryptParams["\x62\154\157\x63\x6b\163\151\172\x65"]);
        $D2 = openssl_encrypt($L2, $this->cryptParams["\x63\x69\160\150\x65\x72"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $D2)) {
            goto mV;
        }
        throw new Exception("\106\141\x69\154\x75\x72\145\x20\145\x6e\x63\162\171\x70\164\x69\156\x67\40\x44\141\164\141\x20\x28\157\x70\145\156\x73\163\x6c\x20\163\x79\155\x6d\x65\x74\x72\151\x63\x29\x20\x2d\40" . openssl_error_string());
        mV:
        return $this->iv . $D2;
    }
    private function decryptSymmetric($L2)
    {
        $MQ = openssl_cipher_iv_length($this->cryptParams["\x63\151\160\150\x65\162"]);
        $this->iv = substr($L2, 0, $MQ);
        $L2 = substr($L2, $MQ);
        $G7 = openssl_decrypt($L2, $this->cryptParams["\143\151\160\150\145\x72"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $G7)) {
            goto Lq;
        }
        throw new Exception("\106\141\x69\154\165\x72\145\40\x64\x65\x63\x72\x79\160\164\x69\x6e\x67\40\104\141\164\x61\x20\50\x6f\x70\145\156\x73\163\x6c\40\163\x79\x6d\x6d\x65\164\x72\151\x63\51\40\x2d\40" . openssl_error_string());
        Lq:
        return $this->unpadISO10126($G7);
    }
    private function encryptPublic($L2)
    {
        if (openssl_public_encrypt($L2, $D2, $this->key, $this->cryptParams["\x70\141\x64\144\x69\156\147"])) {
            goto Ws;
        }
        throw new Exception("\106\141\x69\154\165\x72\x65\x20\x65\156\143\x72\x79\160\164\151\156\x67\x20\x44\141\164\x61\40\x28\157\160\x65\156\163\x73\154\x20\160\x75\142\154\x69\x63\51\x20\x2d\40" . openssl_error_string());
        Ws:
        return $D2;
    }
    private function decryptPublic($L2)
    {
        if (openssl_public_decrypt($L2, $G7, $this->key, $this->cryptParams["\160\x61\x64\x64\x69\x6e\147"])) {
            goto F4;
        }
        throw new Exception("\106\x61\151\154\165\162\x65\40\144\x65\x63\x72\171\160\164\x69\x6e\147\x20\104\141\164\x61\40\50\157\x70\x65\x6e\163\x73\154\40\160\165\142\x6c\151\x63\51\40\55\x20" . openssl_error_string());
        F4:
        return $G7;
    }
    private function encryptPrivate($L2)
    {
        if (openssl_private_encrypt($L2, $D2, $this->key, $this->cryptParams["\160\141\144\x64\x69\x6e\x67"])) {
            goto X0;
        }
        throw new Exception("\x46\x61\151\154\x75\x72\145\40\x65\156\x63\x72\x79\160\x74\151\156\147\40\104\x61\x74\141\40\x28\x6f\160\x65\x6e\163\x73\154\x20\160\x72\151\166\141\x74\145\x29\40\x2d\40" . openssl_error_string());
        X0:
        return $D2;
    }
    private function decryptPrivate($L2)
    {
        if (openssl_private_decrypt($L2, $G7, $this->key, $this->cryptParams["\160\x61\144\x64\x69\x6e\147"])) {
            goto D_;
        }
        throw new Exception("\106\141\x69\x6c\165\162\145\x20\x64\145\x63\x72\x79\x70\164\x69\x6e\147\40\x44\141\x74\141\40\x28\x6f\160\x65\156\163\163\154\40\160\162\x69\x76\x61\x74\145\x29\x20\55\x20" . openssl_error_string());
        D_:
        return $G7;
    }
    private function signOpenSSL($L2)
    {
        $HC = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\151\x67\145\x73\x74"])) {
            goto R1;
        }
        $HC = $this->cryptParams["\144\x69\147\x65\163\164"];
        R1:
        if (openssl_sign($L2, $Jt, $this->key, $HC)) {
            goto X_;
        }
        throw new Exception("\x46\141\x69\x6c\165\162\145\x20\x53\151\x67\x6e\151\156\x67\x20\104\141\164\141\x3a\40" . openssl_error_string() . "\x20\55\x20" . $HC);
        X_:
        return $Jt;
    }
    private function verifyOpenSSL($L2, $Jt)
    {
        $HC = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\x69\x67\145\163\x74"])) {
            goto pr;
        }
        $HC = $this->cryptParams["\x64\151\x67\145\163\164"];
        pr:
        return openssl_verify($L2, $Jt, $this->key, $HC);
    }
    public function encryptData($L2)
    {
        if (!($this->cryptParams["\154\x69\142\x72\x61\x72\x79"] === "\x6f\160\145\156\x73\163\154")) {
            goto MY;
        }
        switch ($this->cryptParams["\x74\171\x70\145"]) {
            case "\163\171\155\155\x65\164\x72\x69\x63":
                return $this->encryptSymmetric($L2);
            case "\160\165\142\x6c\151\143":
                return $this->encryptPublic($L2);
            case "\160\162\151\x76\141\164\x65":
                return $this->encryptPrivate($L2);
        }
        d1:
        k4:
        MY:
    }
    public function decryptData($L2)
    {
        if (!($this->cryptParams["\x6c\151\x62\x72\141\162\171"] === "\157\x70\145\156\x73\x73\x6c")) {
            goto NI;
        }
        switch ($this->cryptParams["\164\171\x70\145"]) {
            case "\163\171\x6d\155\145\164\x72\x69\x63":
                return $this->decryptSymmetric($L2);
            case "\160\165\x62\x6c\151\143":
                return $this->decryptPublic($L2);
            case "\x70\162\151\x76\x61\x74\145":
                return $this->decryptPrivate($L2);
        }
        q4:
        rI:
        NI:
    }
    public function signData($L2)
    {
        switch ($this->cryptParams["\154\x69\x62\162\141\162\x79"]) {
            case "\157\160\145\156\163\x73\x6c":
                return $this->signOpenSSL($L2);
            case self::HMAC_SHA1:
                return hash_hmac("\163\x68\141\x31", $L2, $this->key, true);
        }
        oD:
        ua:
    }
    public function verifySignature($L2, $Jt)
    {
        switch ($this->cryptParams["\x6c\x69\142\x72\x61\x72\171"]) {
            case "\x6f\x70\x65\x6e\163\x73\154":
                return $this->verifyOpenSSL($L2, $Jt);
            case self::HMAC_SHA1:
                $g3 = hash_hmac("\163\x68\141\x31", $L2, $this->key, true);
                return strcmp($Jt, $g3) == 0;
        }
        iw:
        r6:
    }
    public function getAlgorith()
    {
        return $this->getAlgorithm();
    }
    public function getAlgorithm()
    {
        return $this->cryptParams["\x6d\145\x74\150\157\144"];
    }
    public static function makeAsnSegment($yf, $co)
    {
        switch ($yf) {
            case 2:
                if (!(ord($co) > 127)) {
                    goto r4;
                }
                $co = chr(0) . $co;
                r4:
                goto Fl;
            case 3:
                $co = chr(0) . $co;
                goto Fl;
        }
        iX:
        Fl:
        $x7 = strlen($co);
        if ($x7 < 128) {
            goto N8;
        }
        if ($x7 < 256) {
            goto Eb;
        }
        if ($x7 < 65536) {
            goto B2;
        }
        $Zr = null;
        goto AO;
        B2:
        $Zr = sprintf("\x25\143\x25\x63\x25\x63\45\143\x25\163", $yf, 130, $x7 / 256, $x7 % 256, $co);
        AO:
        goto jA;
        Eb:
        $Zr = sprintf("\45\x63\x25\143\x25\143\x25\x73", $yf, 129, $x7, $co);
        jA:
        goto A3;
        N8:
        $Zr = sprintf("\45\143\x25\x63\45\163", $yf, $x7, $co);
        A3:
        return $Zr;
    }
    public static function convertRSA($nr, $rL)
    {
        $KD = self::makeAsnSegment(2, $rL);
        $XU = self::makeAsnSegment(2, $nr);
        $Iv = self::makeAsnSegment(48, $XU . $KD);
        $rC = self::makeAsnSegment(3, $Iv);
        $YY = pack("\110\52", "\x33\x30\60\x44\x30\66\x30\x39\x32\101\x38\x36\64\x38\70\66\x46\x37\x30\104\x30\61\60\61\60\61\x30\x35\x30\x30");
        $Th = self::makeAsnSegment(48, $YY . $rC);
        $i7 = base64_encode($Th);
        $tV = "\55\55\55\x2d\55\102\x45\107\111\116\40\x50\125\102\114\x49\103\x20\113\x45\131\55\55\55\x2d\x2d\xa";
        $EH = 0;
        Wv:
        if (!($V7 = substr($i7, $EH, 64))) {
            goto cD;
        }
        $tV = $tV . $V7 . "\12";
        $EH += 64;
        goto Wv;
        cD:
        return $tV . "\55\x2d\55\55\x2d\105\x4e\104\40\120\x55\102\x4c\x49\x43\x20\113\105\131\55\55\x2d\55\55\12";
    }
    public function serializeKey($ak)
    {
    }
    public function getX509Certificate()
    {
        return $this->x509Certificate;
    }
    public function getX509Thumbprint()
    {
        return $this->X509Thumbprint;
    }
    public static function fromEncryptedKeyElement(DOMElement $mg)
    {
        $tg = new XMLSecEnc();
        $tg->setNode($mg);
        if ($vr = $tg->locateKey()) {
            goto c4;
        }
        throw new Exception("\x55\x6e\141\142\x6c\x65\40\x74\157\x20\x6c\x6f\143\x61\164\x65\40\141\154\x67\157\162\151\164\x68\155\x20\x66\157\x72\40\x74\x68\x69\163\x20\105\156\143\162\171\160\164\145\144\x20\x4b\x65\x79");
        c4:
        $vr->isEncrypted = true;
        $vr->encryptedCtx = $tg;
        XMLSecEnc::staticLocateKeyInfo($vr, $mg);
        return $vr;
    }
}
