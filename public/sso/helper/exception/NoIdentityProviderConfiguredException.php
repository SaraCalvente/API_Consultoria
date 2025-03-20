<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class NoIdentityProviderConfiguredException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\116\117\137\111\104\x50\x5f\x43\x4f\x4e\106\111\x47");
        $cM = 101;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\x5d\72\40{$this->message}\12";
    }
}
