<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidIdentityProviderException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\111\116\126\101\114\x49\x44\137\111\104\x50");
        $cM = 119;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\135\x3a\40{$this->message}\12";
    }
}
