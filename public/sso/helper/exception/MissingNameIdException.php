<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class MissingNameIdException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\115\111\x53\123\x49\116\107\137\x4e\101\x4d\x45\111\x44");
        $cM = 126;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\x5d\72\40{$this->message}\12";
    }
}
