<?php


namespace MiniOrange\Helper\Exception;

use MiniOrange\Helper\Messages;
class MissingIDException extends \Exception
{
    public function __construct()
    {
        $h3 = Messages::parse("\x4d\111\123\123\111\116\x47\137\x49\x44\137\x46\122\117\115\137\x52\x45\x53\x50\117\x4e\123\105");
        $cM = 125;
        parent::__construct($h3, $cM, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
