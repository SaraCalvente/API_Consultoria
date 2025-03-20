<?php
/*
Plugin Name: miniOrange PHP SAML 2.0 Connector
Version: 1.0.0
Author: miniOrange
*/


if (!is_user_registered()) {
    goto qA;
}
header("\114\157\x63\141\164\x69\x6f\156\x3a\x20\x61\144\155\x69\x6e\x5f\154\x6f\147\x69\156\x2e\160\150\x70");
die;
goto Yf;
qA:
header("\x4c\157\x63\x61\164\151\157\x6e\x3a\x20\162\x65\x67\x69\x73\x74\x65\x72\x2e\160\150\x70");
die;
Yf:
function is_user_registered()
{
    $Qc = '';
    if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x68\x65\154\160\145\x72" . DIRECTORY_SEPARATOR . "\144\141\164\141" . DIRECTORY_SEPARATOR . "\143\162\x65\x64\x65\x6e\164\x69\x61\154\x73\56\x6a\x73\x6f\x6e")) {
        goto DI;
    }
    $Qc = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x68\145\154\160\145\162" . DIRECTORY_SEPARATOR . "\144\x61\164\141" . DIRECTORY_SEPARATOR . "\x63\162\145\x64\145\156\164\151\x61\154\x73\x2e\x6a\x73\157\x6e");
    DI:
    if (!empty($Qc)) {
        goto ha;
    }
    return false;
    goto x8;
    ha:
    $Wz = json_decode($Qc, true);
    if (!empty($Wz)) {
        goto uR;
    }
    return false;
    goto S7;
    uR:
    return true;
    S7:
    x8:
}
?>
