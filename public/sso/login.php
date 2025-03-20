<?php


namespace MiniOrange;

use MiniOrange\Classes\Actions\SendAuthnRequest;
use MiniOrange\Helper\Utilities;
include_once "\x61\165\x74\x6f\x6c\157\141\x64\56\x70\x68\160";
final class Login
{
    public function __construct()
    {
        try {
            SendAuthnRequest::execute();
        } catch (\Exception $YR) {
            Utilities::showErrorMessage($YR->getMessage());
        }
    }
}
new Login();
