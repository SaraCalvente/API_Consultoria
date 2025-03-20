<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class InvalidNumberOfNameIDsException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\111\116\126\101\x4c\111\104\137\116\x4f\x5f\117\106\137\x4e\x41\x4d\x45\x49\x44\123");
        $cM = 124;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\135\x3a\x20{$this->message}\xa";
    }
}
