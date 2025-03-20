<?php


namespace MiniOrange\Helper;

class PluginSettings
{
    private $idp_name;
    private $idp_entity_id;
    private $saml_login_url;
    private $saml_login_binding_type;
    private $sign_response;
    private $sign_assertion;
    private $force_authentication;
    private $sp_base_url;
    private $sp_entity_id;
    private $acs_url;
    private $account_matcher;
    private $application_url;
    private static $obj;
    private final function __construct()
    {
        $Qc = '';
        if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\144\x61\164\141" . DIRECTORY_SEPARATOR . "\x63\157\156\x66\151\x67\56\x6a\163\x6f\156")) {
            goto Rq;
        }
        $Qc = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\144\141\x74\x61" . DIRECTORY_SEPARATOR . "\143\157\156\x66\151\x67\x2e\x6a\163\157\156");
        Rq:
        $Jv = json_decode($Qc, true);
        $Qc = '';
        if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x64\x61\x74\141" . DIRECTORY_SEPARATOR . "\x75\x73\145\162\x5f\x63\x6f\156\x66\x69\x67\56\x6a\x73\x6f\x6e")) {
            goto QP;
        }
        $Qc = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x64\x61\x74\141" . DIRECTORY_SEPARATOR . "\165\163\x65\x72\x5f\143\157\x6e\146\151\x67\x2e\x6a\x73\157\156");
        QP:
        $R3 = json_decode($Qc, true);
        $this->idp_name = '';
        $this->idp_entity_id = '';
        $this->saml_login_url = '';
        $this->saml_login_binding_type = "\x48\x74\164\x70\122\x65\x64\151\x72\145\143\x74";
        $this->sign_response = false;
        $this->sign_assertion = false;
        $this->force_authentication = false;
        $this->sp_base_url = '';
        $this->sp_entity_id = '';
        $this->acs_url = '';
        $this->account_matcher = '';
        $this->application_url = '';
        if (empty($R3)) {
            goto oA;
        }
        $this->application_url = $R3["\141\x70\x70\154\x69\143\141\164\x69\x6f\x6e\x5f\165\x72\154"];
        oA:
        if (empty($Jv)) {
            goto IZ;
        }
        if (!array_key_exists("\151\x64\x70\137\156\x61\155\145", $Jv)) {
            goto Kd;
        }
        $this->idp_name = $Jv["\x69\x64\x70\x5f\x6e\141\x6d\145"];
        Kd:
        if (!array_key_exists("\151\144\160\x5f\x65\x6e\x74\x69\164\171\137\151\x64", $Jv)) {
            goto xO;
        }
        $this->idp_entity_id = $Jv["\151\x64\x70\137\x65\x6e\x74\x69\x74\x79\x5f\x69\x64"];
        xO:
        if (!array_key_exists("\163\141\155\154\137\x6c\x6f\x67\x69\x6e\x5f\x75\162\154", $Jv)) {
            goto tb;
        }
        $this->saml_login_url = $Jv["\163\141\x6d\x6c\137\154\157\147\x69\x6e\137\165\162\x6c"];
        tb:
        if (!array_key_exists("\x6c\x6f\147\151\x6e\x5f\142\151\156\144\151\156\147\x5f\x74\x79\160\145", $Jv)) {
            goto va;
        }
        $this->saml_login_binding_type = $Jv["\154\x6f\x67\x69\156\x5f\142\151\156\144\x69\156\147\137\164\x79\x70\145"];
        va:
        if (!array_key_exists("\163\x69\x67\x6e\137\x72\145\x73\160\x6f\x6e\163\x65", $Jv)) {
            goto Uy;
        }
        $this->sign_response = $Jv["\x73\x69\147\x6e\137\162\x65\x73\x70\x6f\156\163\145"];
        Uy:
        if (!array_key_exists("\x73\151\147\156\137\141\x73\163\145\x72\x74\151\x6f\x6e", $Jv)) {
            goto ja;
        }
        $this->sign_assertion = $Jv["\163\x69\x67\156\x5f\141\163\163\x65\162\x74\151\x6f\156"];
        ja:
        if (!array_key_exists("\x66\x6f\x72\143\x65\x5f\x61\x75\164\150\145\156\164\151\x63\141\x74\151\157\156", $Jv)) {
            goto Kn;
        }
        $this->force_authentication = $Jv["\x66\157\x72\143\x65\137\x61\x75\164\x68\145\156\164\151\x63\141\x74\151\x6f\156"];
        Kn:
        if (!array_key_exists("\x73\151\164\x65\x5f\142\141\x73\145\137\x75\x72\154", $Jv)) {
            goto Wf;
        }
        $this->sp_base_url = $Jv["\x73\151\164\x65\137\x62\141\163\x65\x5f\x75\x72\154"];
        Wf:
        if (!array_key_exists("\x73\x70\137\145\156\x74\151\164\x79\137\x69\144", $Jv)) {
            goto l8;
        }
        $this->sp_entity_id = $Jv["\163\x70\x5f\145\156\164\x69\164\x79\x5f\x69\x64"];
        l8:
        if (!array_key_exists("\x61\143\x73\x5f\165\162\154", $Jv)) {
            goto nV;
        }
        $this->acs_url = $Jv["\141\x63\163\137\165\162\x6c"];
        nV:
        if (!array_key_exists("\163\141\x6d\154\x5f\141\143\x63\x6f\x75\156\164\137\x6d\x61\164\143\x68\x65\x72", $Jv)) {
            goto ne;
        }
        $this->account_matcher = $Jv["\163\x61\155\154\x5f\141\143\143\x6f\x75\156\164\x5f\155\141\164\143\150\x65\x72"];
        ne:
        IZ:
    }
    public static function getPluginSettings()
    {
        if (isset(self::$obj)) {
            goto fL;
        }
        self::$obj = new PluginSettings();
        fL:
        return self::$obj;
    }
    public function getIdpName()
    {
        return $this->idp_name;
    }
    public function getIdpEntityId()
    {
        return $this->idp_entity_id;
    }
    public function getSamlLoginUrl()
    {
        return $this->saml_login_url;
    }
    public function getResponseSigned()
    {
        if ($this->sign_response != false) {
            goto Y3;
        }
        return FALSE;
        goto wO;
        Y3:
        return TRUE;
        wO:
    }
    public function getAssertionSigned()
    {
        if ($this->sign_assertion != false) {
            goto iN;
        }
        return FALSE;
        goto E6;
        iN:
        return TRUE;
        E6:
    }
    public function getLoginBindingType()
    {
        return $this->saml_login_binding_type;
    }
    public function getForceAuthentication()
    {
        if ($this->force_authentication != false) {
            goto Kk;
        }
        return FALSE;
        goto Uk;
        Kk:
        return TRUE;
        Uk:
    }
    public function getSiteBaseUrl()
    {
        return $this->sp_base_url;
    }
    public function getSpEntityId()
    {
        return $this->sp_entity_id;
    }
    public function getAcsUrl()
    {
        return $this->acs_url;
    }
    public function getAccountMatcher()
    {
        return $this->account_matcher;
    }
    public function getApplicationUrl()
    {
        return $this->application_url;
    }
}
