<?php


namespace MiniOrange\Classes\Actions;

class ProcessUserAction
{
    private $attrs;
    private $relayState;
    private $sessionIndex;
    public function __construct($dj, $Ag, $uQ)
    {
        $this->attrs = $dj;
        $this->relayState = $Ag;
        $this->sessionIndex = $uQ;
    }
    function execute()
    {
    }
}
