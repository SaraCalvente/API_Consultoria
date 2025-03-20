<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class MissingAttributesException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\x4d\x49\123\x53\x49\116\x47\137\101\124\x54\122\x49\x42\125\x54\x45\x53\137\105\x58\103\105\x50\x54\x49\117\116");
        $cM = 125;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\x5d\72\x20{$this->message}\12";
    }
}
