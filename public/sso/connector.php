<?php


echo "\15\12\x20\x20\40\x20\x3c\x21\55\x2d\40\x45\x73\x73\x65\x6e\164\151\141\154\40\152\x61\166\x61\163\143\x72\151\160\x74\163\x20\146\x6f\x72\40\x61\160\x70\x6c\x69\x63\141\164\151\x6f\x6e\x20\x74\157\40\x77\157\162\153\55\55\x3e\15\12\40\x20\40\x20\x20\74\163\x63\x72\151\160\164\x20\x73\162\x63\75\42\151\156\143\154\x75\x64\145\163\57\x6a\x73\57\x6a\x71\165\x65\162\171\x2d\63\56\62\x2e\61\56\x6d\x69\x6e\56\152\163\x22\76\74\x2f\x73\143\x72\x69\160\164\76\xd\xa\x20\40\x20\x20\74\163\x63\x72\151\160\x74\40\163\x72\x63\75\x22\x69\156\x63\154\x75\x64\145\163\57\x6a\x73\x2f\160\157\160\x70\145\x72\x2e\155\x69\x6e\x2e\152\163\x22\x3e\74\x2f\163\x63\162\151\x70\x74\x3e\xd\12\x20\x20\40\x20\74\163\143\x72\151\x70\164\x20\163\x72\143\75\42\x69\x6e\x63\x6c\165\x64\145\163\x2f\152\163\57\x62\x6f\x6f\x74\x73\164\162\x61\160\56\x6d\151\156\56\x6a\x73\42\76\x3c\x2f\163\143\162\x69\160\164\x3e\15\xa\x20\x20\40\40\74\163\x63\162\x69\x70\164\x20\x73\x72\x63\75\42\x69\156\143\154\165\144\145\x73\x2f\x6a\x73\x2f\x6d\x61\151\x6e\x2e\x6a\x73\x22\x3e\74\x2f\163\x63\162\x69\x70\x74\76\15\xa\x20\40\x20\x20\x3c\x21\x2d\x2d\x20\x54\150\145\40\152\141\166\141\x73\x63\x72\151\x70\x74\40\160\154\165\x67\151\156\40\x74\157\40\x64\x69\x73\x70\x6c\141\171\x20\x70\141\147\145\40\154\x6f\x61\144\x69\x6e\147\40\x6f\156\40\x74\x6f\x70\55\x2d\76\xd\12\40\40\x20\x20\x3c\x73\143\162\151\x70\x74\40\163\162\143\x3d\x22\x69\x6e\x63\x6c\x75\144\x65\163\57\152\163\x2f\160\x6c\165\147\x69\156\163\x2f\x70\x61\x63\x65\x2e\x6d\151\x6e\56\x6a\163\42\76\x3c\x2f\x73\x63\x72\151\160\x74\x3e\15\xa\40\40\x20\x20";
if (class_exists("\104\x42")) {
    goto zN;
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x68\x65\154\160\145\162" . DIRECTORY_SEPARATOR . "\x44\x42\56\x70\150\x70";
zN:
if (class_exists("\103\165\163\164\x6f\155\x65\162\123\141\155\x6c")) {
    goto l2;
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x63\x6c\x61\x73\x73\145\163" . DIRECTORY_SEPARATOR . "\103\x75\163\164\157\x6d\145\162\56\x70\x68\x70";
l2:
DB::update_option("\x6d\157\x5f\x73\x61\155\154\137\150\157\x73\x74\x5f\x6e\141\155\x65", "\x68\x74\x74\x70\x73\72\x2f\x2f\141\165\x74\x68\56\x6d\151\x6e\x69\x6f\x72\141\x6e\x67\x65\56\x63\157\x6d");
if (!(isset($_POST["\x6f\x70\x74\151\x6f\156"]) && !empty($_POST["\x6f\160\164\151\157\x6e"]))) {
    goto SA;
}
if (!($_POST["\x6f\x70\164\151\x6f\x6e"] === "\x6d\157\137\163\141\x6d\154\x5f\143\157\156\164\141\x63\x74\137\165\163")) {
    goto mz;
}
$CG = $_POST["\143\x6f\156\x74\x61\x63\164\x5f\165\163\137\145\x6d\141\151\x6c"];
$Uh = $_POST["\143\157\156\164\x61\143\x74\137\x75\x73\137\x70\150\157\x6e\145"];
$Jx = $_POST["\x63\157\156\x74\x61\x63\x74\x5f\x75\x73\137\x71\165\145\162\171"];
if (mo_saml_check_empty_or_null($CG) || mo_saml_check_empty_or_null($Jx)) {
    goto Jm;
}
if (!filter_var($CG, FILTER_VALIDATE_EMAIL)) {
    goto Sr;
}
$iJ = $Go->submit_contact_us($CG, $Uh, $Jx);
if ($iJ == false) {
    goto p2;
}
DB::update_option("\x6d\157\137\x73\x61\x6d\x6c\137\x6d\x65\x73\x73\x61\147\145", "\x54\x68\x61\156\153\163\x20\146\157\162\40\x67\145\x74\x74\151\x6e\147\x20\151\156\x20\x74\x6f\x75\143\x68\41\x20\127\x65\x20\x73\150\141\x6c\154\40\x67\145\x74\40\142\141\143\153\40\164\157\40\x79\x6f\165\40\x73\x68\157\162\x74\154\171\x2e");
mo_saml_show_success_message();
goto rb;
p2:
DB::update_option("\155\157\137\163\141\155\154\x5f\155\x65\x73\x73\141\x67\x65", "\131\157\x75\x72\x20\161\165\x65\162\x79\40\143\x6f\x75\x6c\144\x20\x6e\157\x74\x20\x62\x65\40\x73\165\x62\155\151\164\x74\x65\144\56\40\x50\154\x65\x61\x73\145\x20\164\x72\171\x20\141\147\x61\x69\156\56");
mo_saml_show_error_message();
rb:
goto iR;
Sr:
DB::update_option("\x6d\157\137\x73\x61\155\x6c\x5f\x6d\x65\163\x73\141\147\145", "\x50\x6c\145\141\x73\x65\x20\145\x6e\x74\145\x72\40\141\x20\166\x61\x6c\151\x64\x20\145\x6d\141\151\x6c\x20\141\x64\144\162\145\163\163\56");
mo_saml_show_error_message();
iR:
goto WC;
Jm:
DB::update_option("\x6d\157\137\163\141\x6d\x6c\137\155\145\163\x73\141\147\145", "\x50\154\x65\x61\163\x65\x20\x66\151\x6c\154\x20\x75\160\40\105\155\141\x69\x6c\x20\141\x6e\x64\40\x51\x75\x65\x72\x79\40\146\x69\x65\154\x64\163\x20\164\x6f\x20\163\165\142\155\151\x74\x20\x79\157\x75\x72\40\x71\x75\x65\162\x79\x2e");
mo_saml_show_error_message();
WC:
mz:
SA:
if (!(isset($_POST["\157\x70\x74\x69\x6f\x6e"]) and $_POST["\x6f\160\164\151\157\x6e"] == "\155\157\x5f\x73\x61\155\x6c\137\x72\145\x67\151\x73\x74\x65\162\x5f\x63\165\x73\x74\x6f\155\145\x72")) {
    goto CP;
}
mo_register_action();
CP:
if (!(isset($_POST["\x6f\x70\x74\151\157\156"]) and $_POST["\157\x70\164\151\157\x6e"] == "\155\x6f\x5f\x73\141\155\x6c\x5f\147\x6f\164\x6f\x5f\x6c\x6f\x67\x69\156")) {
    goto bz;
}
DB::delete_option("\x6d\157\137\163\141\x6d\154\x5f\x6e\145\x77\137\x72\x65\147\x69\x73\164\x72\141\x74\x69\157\156");
DB::update_option("\155\157\x5f\x73\141\x6d\154\137\x76\x65\x72\151\146\171\137\x63\165\x73\164\157\x6d\145\x72", "\x74\x72\x75\x65");
bz:
if (!(isset($_POST["\157\x70\x74\151\x6f\x6e"]) and $_POST["\157\x70\164\x69\157\156"] == "\143\x68\141\156\147\145\137\155\x69\156\151\x6f\x72\x61\156\x67\145")) {
    goto VN;
}
mo_saml_remove_account();
DB::update_option("\155\157\x5f\163\x61\155\x6c\137\147\165\145\x73\164\x5f\x65\156\x61\x62\x6c\x65\144", true);
return;
VN:
if (!(isset($_POST["\x6f\x70\164\x69\157\156"]) and $_POST["\157\160\164\151\x6f\x6e"] == "\x6d\157\x5f\x73\x61\155\154\x5f\x67\x6f\x5f\142\x61\143\x6b")) {
    goto Go;
}
DB::update_option("\155\157\x5f\x73\141\x6d\x6c\x5f\x72\145\x67\x69\x73\x74\162\x61\164\151\x6f\156\137\x73\164\x61\164\x75\x73", '');
DB::update_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\137\x76\145\162\x69\146\171\137\143\x75\163\x74\x6f\155\x65\x72", '');
DB::delete_option("\x6d\157\137\163\141\155\x6c\x5f\x6e\145\x77\x5f\162\145\x67\x69\x73\x74\x72\141\164\x69\x6f\156");
DB::delete_option("\155\x6f\137\163\x61\155\154\x5f\x61\x64\155\x69\x6e\x5f\x65\x6d\x61\x69\x6c");
DB::delete_option("\x6d\x6f\x5f\163\x61\x6d\x6c\x5f\x61\144\x6d\x69\x6e\x5f\160\150\157\156\x65");
Go:
if (!(isset($_POST["\x6f\x70\x74\x69\x6f\x6e"]) and $_POST["\x6f\160\x74\151\157\156"] == "\155\x6f\x5f\x73\141\x6d\x6c\x5f\166\145\162\151\x66\x79\137\x63\x75\163\164\157\155\145\x72")) {
    goto O7;
}
if (mo_saml_is_curl_installed()) {
    goto Xh;
}
DB::update_option("\x6d\157\x5f\x73\141\x6d\154\137\155\x65\163\163\x61\x67\145", "\x45\122\122\x4f\x52\x3a\x20\x3c\x61\x20\x68\162\x65\146\75\x22\150\x74\x74\160\x3a\x2f\x2f\160\x68\x70\56\x6e\145\164\57\155\x61\156\165\141\x6c\x2f\x65\156\x2f\x63\x75\x72\x6c\56\x69\156\163\x74\141\154\154\x61\x74\151\x6f\x6e\x2e\160\x68\160\42\40\164\141\162\147\x65\x74\75\x22\137\x62\x6c\x61\x6e\153\x22\x3e\120\x48\x50\x20\x63\x55\122\x4c\x20\x65\x78\164\145\x6e\163\x69\157\x6e\74\57\x61\x3e\x20\x69\163\40\156\157\164\40\x69\156\x73\x74\141\154\154\145\x64\x20\157\162\40\144\x69\163\x61\x62\x6c\x65\x64\x2e\40\x4c\x6f\x67\x69\x6e\40\x66\141\151\154\145\x64\56");
mo_saml_show_error_message();
return;
Xh:
$CG = '';
$JK = '';
if (mo_saml_check_empty_or_null($_POST["\x65\155\x61\151\154"]) || mo_saml_check_empty_or_null($_POST["\160\141\163\x73\167\157\x72\144"])) {
    goto j2;
}
if (checkPasswordpattern(strip_tags($_POST["\x70\x61\x73\x73\x77\157\x72\144"]))) {
    goto Cr;
}
$CG = $_POST["\145\155\x61\151\x6c"];
$JK = stripslashes(strip_tags($_POST["\x70\141\x73\x73\167\157\x72\144"]));
goto qt;
Cr:
DB::update_option("\155\157\x5f\163\141\x6d\x6c\137\x6d\145\x73\x73\x61\147\145", "\x4d\151\156\151\x6d\x75\155\x20\x36\x20\143\x68\x61\162\141\x63\x74\145\x72\x73\40\x73\150\157\165\154\x64\x20\142\x65\40\160\162\x65\163\145\x6e\164\x2e\x20\115\141\x78\x69\x6d\x75\x6d\x20\61\65\x20\x63\x68\x61\x72\141\x63\x74\x65\x72\163\x20\163\150\x6f\165\x6c\144\40\x62\x65\x20\160\x72\145\163\145\x6e\164\56\40\117\x6e\x6c\171\x20\x66\157\154\154\157\167\151\x6e\x67\40\163\x79\x6d\x62\157\154\x73\x20\50\x21\100\x23\x2e\x24\45\x5e\x26\52\x2d\137\x29\x20\163\x68\157\165\x6c\x64\40\142\x65\40\x70\x72\145\x73\x65\x6e\164\x2e");
mo_saml_show_error_message();
return;
qt:
goto Ue;
j2:
DB::update_option("\x6d\x6f\x5f\163\141\155\x6c\137\x6d\x65\163\163\141\x67\145", "\101\x6c\154\x20\164\150\x65\x20\146\151\145\x6c\144\x73\40\x61\x72\145\40\162\145\x71\x75\x69\x72\145\x64\56\x20\120\x6c\x65\141\163\x65\x20\145\x6e\164\x65\162\x20\x76\141\x6c\x69\x64\x20\x65\x6e\x74\x72\x69\x65\x73\56");
mo_saml_show_error_message();
return;
Ue:
DB::update_option("\155\157\x5f\163\x61\155\154\137\141\x64\x6d\151\x6e\x5f\145\x6d\x61\151\154", $CG);
DB::update_option("\155\x6f\x5f\x73\x61\155\x6c\137\x61\144\155\151\156\x5f\x70\141\x73\163\x77\157\162\144", $JK);
$Go = new Customersaml();
$c0 = $Go->get_customer_key();
$QZ = json_decode($c0, true);
if (json_last_error() == JSON_ERROR_NONE) {
    goto JD;
}
DB::update_option("\x6d\x6f\137\163\141\x6d\x6c\137\155\x65\163\x73\x61\147\x65", "\x49\156\166\x61\154\x69\144\x20\165\163\x65\x72\x6e\141\155\145\x20\157\162\40\x70\141\x73\x73\x77\157\162\x64\x2e\40\120\154\145\x61\163\145\x20\164\162\x79\40\141\147\141\151\x6e\x2e");
mo_saml_show_error_message();
goto Cx;
JD:
DB::update_option("\x6d\157\x5f\163\x61\x6d\154\x5f\x61\x64\x6d\x69\156\x5f\143\x75\163\x74\x6f\155\x65\x72\137\x6b\145\171", $QZ["\x69\144"]);
DB::update_option("\155\157\x5f\163\141\155\154\x5f\x61\x64\x6d\x69\x6e\137\141\160\151\x5f\153\x65\x79", $QZ["\x61\160\151\x4b\145\171"]);
DB::update_option("\x6d\157\137\163\x61\x6d\x6c\x5f\143\x75\163\164\157\x6d\x65\x72\137\x74\157\x6b\145\156", $QZ["\164\x6f\153\x65\x6e"]);
$v0 = DB::get_option("\163\141\155\154\x5f\x78\x35\60\x39\x5f\x63\145\x72\x74\x69\x66\151\x63\141\164\145");
if (!empty($v0)) {
    goto Y6;
}
DB::update_option("\155\157\x5f\163\x61\155\154\x5f\146\162\145\x65\137\166\x65\162\x73\151\157\156", 1);
Y6:
DB::update_option("\x6d\157\137\x73\141\155\x6c\x5f\141\144\x6d\151\x6e\137\160\x61\163\x73\x77\x6f\162\x64", '');
DB::update_option("\x6d\157\137\163\141\x6d\x6c\137\155\x65\x73\x73\x61\147\x65", "\x43\x75\x73\164\x6f\155\145\162\40\162\x65\164\162\x69\145\x76\145\x64\x20\163\165\x63\x63\145\x73\x73\x66\x75\154\x6c\171");
DB::update_option("\155\157\137\x73\141\x6d\154\x5f\x72\145\x67\x69\x73\164\162\x61\x74\151\157\156\137\x73\164\x61\164\x75\163", "\105\x78\x69\163\x74\x69\x6e\x67\40\x55\163\x65\x72");
DB::delete_option("\x6d\x6f\x5f\163\x61\x6d\154\137\x76\x65\x72\x69\146\171\137\143\x75\x73\164\x6f\155\x65\162");
mo_saml_show_success_message();
Cx:
DB::update_option("\x6d\157\137\x73\x61\x6d\154\x5f\141\144\x6d\x69\x6e\x5f\x70\x61\163\163\167\157\x72\x64", '');
O7:
if (!(isset($_POST["\157\x70\x74\151\157\x6e"]) and $_POST["\157\x70\x74\151\x6f\156"] == "\155\157\137\163\141\x6d\x6c\x5f\x63\x6f\156\x74\141\143\x74\137\165\x73\137\x71\165\145\x72\x79\137\x6f\x70\164\151\157\156")) {
    goto Br;
}
if (mo_saml_is_curl_installed()) {
    goto Kr;
}
DB::update_option("\x6d\x6f\x5f\163\x61\x6d\x6c\x5f\x6d\145\163\163\x61\x67\145", "\105\x52\122\x4f\x52\72\x20\74\x61\x20\x68\x72\145\146\75\x22\150\164\x74\x70\72\x2f\57\160\x68\160\56\156\145\x74\57\x6d\x61\x6e\165\141\x6c\57\x65\156\57\x63\165\x72\x6c\x2e\x69\156\163\164\x61\154\154\x61\164\151\x6f\x6e\56\x70\150\x70\x22\x20\164\x61\x72\147\x65\164\x3d\x22\137\x62\154\141\x6e\x6b\x22\76\120\110\120\40\143\x55\x52\x4c\40\145\x78\164\x65\156\163\x69\x6f\156\74\x2f\141\76\40\151\163\x20\156\x6f\164\40\x69\156\x73\x74\x61\x6c\154\145\x64\40\x6f\162\x20\144\151\x73\x61\142\154\x65\144\56\x20\x51\x75\x65\162\171\x20\163\165\x62\155\151\164\40\x66\141\x69\x6c\145\144\x2e");
mo_saml_show_error_message();
return;
Kr:
$CG = $_POST["\155\157\137\x73\141\x6d\154\137\x63\x6f\x6e\x74\x61\143\164\137\x75\163\x5f\x65\x6d\x61\151\x6c"];
$Uh = $_POST["\x6d\157\137\163\x61\155\154\x5f\143\157\156\x74\141\143\164\137\x75\x73\x5f\160\150\157\156\145"];
$Jx = $_POST["\155\x6f\x5f\163\x61\x6d\154\137\x63\157\156\164\141\x63\x74\x5f\x75\x73\137\161\165\x65\x72\171"];
$Go = new CustomerSaml();
if (mo_saml_check_empty_or_null($CG) || mo_saml_check_empty_or_null($Jx)) {
    goto hT;
}
if (!filter_var($CG, FILTER_VALIDATE_EMAIL)) {
    goto j9;
}
$iJ = $Go->submit_contact_us($CG, $Uh, $Jx);
if ($iJ == false) {
    goto WZ;
}
DB::update_option("\x6d\x6f\x5f\x73\141\155\x6c\137\155\x65\163\163\x61\x67\x65", "\124\150\x61\156\x6b\163\40\146\157\x72\x20\147\145\x74\164\x69\156\x67\x20\x69\156\40\x74\157\x75\x63\150\41\x20\127\x65\x20\x73\x68\x61\154\154\x20\x67\145\x74\40\x62\141\143\x6b\40\x74\157\40\x79\x6f\165\x20\163\x68\x6f\x72\164\154\x79\x2e");
mo_saml_show_success_message();
goto iV;
WZ:
DB::update_option("\155\157\x5f\163\x61\155\154\x5f\155\145\x73\163\141\147\x65", "\131\157\165\x72\x20\161\165\x65\162\171\40\x63\x6f\x75\x6c\144\40\x6e\157\x74\40\x62\x65\40\x73\x75\142\x6d\x69\164\x74\145\144\56\x20\120\x6c\x65\x61\x73\x65\40\164\162\x79\x20\x61\x67\x61\x69\156\x2e");
mo_saml_show_error_message();
iV:
goto dq;
j9:
DB::update_option("\155\157\137\163\141\x6d\154\137\155\145\x73\163\141\x67\x65", "\120\x6c\x65\x61\x73\x65\x20\x65\156\x74\x65\162\x20\x61\40\166\x61\x6c\151\x64\x20\x65\x6d\x61\x69\x6c\x20\141\x64\144\162\x65\x73\x73\56");
mo_saml_show_error_message();
dq:
goto q9;
hT:
DB::update_option("\155\157\x5f\163\x61\155\154\137\155\145\163\163\x61\147\x65", "\120\154\x65\141\163\x65\40\x66\x69\154\x6c\x20\165\160\x20\105\x6d\141\x69\x6c\x20\141\156\x64\x20\x51\x75\145\x72\171\x20\x66\151\145\154\x64\x73\x20\x74\157\40\163\x75\x62\155\x69\164\x20\171\157\x75\x72\40\x71\x75\145\162\x79\56");
mo_saml_show_error_message();
q9:
Br:
function mo_register_action()
{
    $CG = $_POST["\145\155\141\x69\154"];
    $JK = stripslashes($_POST["\160\x61\163\x73\x77\x6f\x72\x64"]);
    $Tj = stripslashes($_POST["\x63\x6f\156\146\x69\162\155\x50\141\163\163\x77\157\x72\144"]);
    DB::update_option("\155\157\x5f\x73\141\155\x6c\x5f\141\144\x6d\x69\x6e\137\x65\x6d\141\x69\154", $CG);
    if (strcmp($JK, $Tj) == 0) {
        goto pj;
    }
    $RG["\x73\x74\141\x74\x75\x73"] = "\156\x6f\164\137\155\x61\164\143\x68";
    DB::update_option("\x6d\157\x5f\x73\141\155\154\137\x6d\145\163\x73\141\x67\x65", "\120\x61\x73\x73\x77\157\162\144\163\x20\x64\x6f\40\156\x6f\164\x20\x6d\x61\x74\x63\x68\x2e");
    mo_saml_show_error_message();
    goto eF;
    pj:
    DB::update_option("\155\x6f\137\x73\x61\x6d\x6c\137\141\144\x6d\x69\x6e\137\160\141\x73\x73\167\x6f\162\144", $JK);
    $Go = new CustomerSaml();
    $c0 = json_decode($Go->check_customer(), true);
    if (strcasecmp($c0["\163\x74\141\x74\165\x73"], "\103\125\123\124\x4f\115\105\122\137\x4e\117\124\137\106\x4f\x55\116\x44") == 0) {
        goto PT;
    }
    $RG = get_current_customer();
    goto GP;
    PT:
    $RG = create_customer();
    GP:
    DB::update_option("\155\157\x5f\163\x61\155\154\x5f\x6d\145\x73\x73\141\147\x65", "\114\x6f\x67\147\x65\x64\40\151\156\40\x61\x73\40\107\x75\145\x73\164\56");
    mo_saml_show_success_message();
    eF:
}
function create_customer()
{
    $Go = new CustomerSaml();
    $QZ = json_decode($Go->create_customer(), true);
    $RG = array();
    if (strcasecmp($QZ["\163\164\x61\x74\x75\163"], "\x43\x55\123\x54\x4f\x4d\105\x52\x5f\125\x53\105\x52\116\x41\x4d\x45\137\101\x4c\122\x45\101\104\x59\137\x45\130\x49\x53\x54\123") == 0) {
        goto Ck;
    }
    if (!(strcasecmp($QZ["\x73\164\x61\164\165\x73"], "\123\125\103\x43\x45\123\123") == 0)) {
        goto FC;
    }
    DB::update_option("\155\157\137\x73\141\x6d\x6c\137\141\144\155\x69\x6e\x5f\143\165\163\164\157\155\145\162\x5f\x6b\145\171", $QZ["\x69\144"]);
    DB::update_option("\155\157\137\x73\x61\155\154\x5f\141\144\155\x69\156\137\141\x70\x69\x5f\153\x65\171", $QZ["\x61\x70\151\113\x65\x79"]);
    DB::update_option("\x6d\157\x5f\x73\x61\x6d\x6c\x5f\143\x75\x73\164\157\x6d\x65\162\137\x74\x6f\x6b\145\x6e", $QZ["\164\x6f\x6b\145\x6e"]);
    DB::update_option("\x6d\x6f\137\x73\x61\155\154\137\146\162\x65\145\x5f\166\x65\162\163\x69\x6f\156", 1);
    DB::update_option("\x6d\x6f\x5f\163\141\x6d\154\x5f\141\144\x6d\x69\x6e\x5f\x70\x61\163\163\167\x6f\162\x64", '');
    DB::update_option("\x6d\x6f\x5f\163\141\x6d\154\137\155\x65\x73\x73\x61\147\x65", "\x54\x68\x61\156\x6b\40\171\157\165\40\x66\x6f\x72\40\x72\x65\147\151\163\x74\x65\x72\x69\156\147\40\167\151\x74\150\40\x6d\151\x6e\x69\157\162\x61\x6e\147\x65\56");
    DB::update_option("\155\157\137\x73\x61\x6d\x6c\x5f\162\145\x67\x69\163\164\162\141\x74\151\157\156\137\163\x74\141\164\165\x73", '');
    DB::delete_option("\x6d\x6f\137\x73\x61\x6d\154\137\166\145\x72\151\x66\x79\137\x63\x75\x73\164\x6f\x6d\145\x72");
    DB::delete_option("\x6d\x6f\x5f\163\x61\155\x6c\x5f\156\x65\x77\x5f\162\x65\x67\151\163\164\162\141\164\x69\x6f\156");
    $RG["\163\164\141\x74\165\x73"] = "\163\165\143\143\145\163\163";
    return $RG;
    FC:
    goto At;
    Ck:
    $FC = get_current_customer();
    if ($FC) {
        goto Ig;
    }
    $RG["\x73\164\141\x74\x75\163"] = "\145\162\x72\157\x72";
    goto xf;
    Ig:
    $RG["\x73\x74\141\164\x75\x73"] = "\163\165\143\143\x65\x73\163";
    xf:
    At:
    DB::update_option("\x6d\157\x5f\x73\x61\x6d\154\137\x61\144\x6d\151\156\137\160\141\x73\163\x77\x6f\162\144", '');
    return $RG;
}
function get_current_customer()
{
    $Go = new CustomerSaml();
    $c0 = $Go->get_customer_key();
    $QZ = json_decode($c0, true);
    $RG = array();
    if (json_last_error() == JSON_ERROR_NONE) {
        goto dY;
    }
    DB::update_option("\155\x6f\x5f\x73\141\155\x6c\x5f\155\x65\x73\163\141\x67\x65", "\131\x6f\x75\40\141\x6c\162\x65\x61\x64\171\40\150\x61\166\x65\x20\141\156\x20\141\x63\x63\x6f\165\156\164\x20\x77\x69\164\x68\40\x6d\151\x6e\151\117\x72\x61\156\x67\x65\56\x20\120\154\145\x61\163\x65\40\145\156\x74\145\x72\40\141\x20\x76\141\154\151\144\x20\x70\141\163\163\167\157\162\144\56");
    mo_saml_show_error_message();
    $RG["\x73\164\x61\164\165\163"] = "\x65\162\162\x6f\162";
    return $RG;
    goto RI;
    dY:
    DB::update_option("\155\x6f\x5f\163\x61\155\x6c\x5f\x61\x64\x6d\151\x6e\137\x63\165\x73\x74\x6f\x6d\x65\162\137\x6b\145\x79", $QZ["\151\144"]);
    DB::update_option("\155\157\x5f\x73\x61\x6d\x6c\137\141\144\155\x69\x6e\x5f\x61\x70\151\x5f\153\145\171", $QZ["\141\x70\151\113\145\171"]);
    DB::update_option("\155\x6f\x5f\x73\x61\x6d\154\x5f\143\165\163\x74\157\x6d\145\x72\x5f\164\157\x6b\145\156", $QZ["\164\157\x6b\145\156"]);
    DB::update_option("\155\x6f\x5f\163\x61\155\154\x5f\141\144\155\x69\156\137\x70\141\163\x73\167\x6f\x72\x64", '');
    $v0 = DB::get_option("\163\141\155\x6c\x5f\x78\65\x30\71\x5f\143\x65\x72\164\x69\x66\151\x63\141\164\145");
    if (!empty($v0)) {
        goto iH;
    }
    DB::update_option("\155\157\x5f\x73\x61\x6d\154\x5f\146\162\145\145\x5f\166\145\162\x73\x69\157\x6e", 1);
    iH:
    DB::delete_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\x5f\x76\145\x72\151\146\171\x5f\x63\165\163\164\157\x6d\x65\162");
    DB::delete_option("\x6d\x6f\137\163\x61\x6d\x6c\137\x6e\145\x77\137\162\x65\x67\x69\x73\164\162\141\x74\x69\157\156");
    $RG["\x73\164\141\164\x75\163"] = "\x73\x75\143\x63\145\163\x73";
    return $RG;
    RI:
}
function mo_saml_show_success_message()
{
    echo "\x3c\163\143\162\151\160\164\x3e\xd\xa\40\40\x20\x20\57\x2f\40\x76\141\162\x20\155\145\163\x73\141\x67\x65\40\75\40\144\x6f\143\x75\155\x65\x6e\164\56\x67\145\x74\105\x6c\145\155\x65\x6e\x74\x42\x79\116\x61\155\145\50\x27\x73\141\x6d\154\137\155\145\x73\163\141\x67\145\47\x29\x2e\151\156\x6e\145\x72\110\x54\115\x4c\40\75\x20" . DB::get_option("\x6d\x6f\x5f\163\141\155\154\x5f\155\x65\163\163\141\147\x65") . "\15\xa\40\40\x20\x20\57\x2f\x20\155\x65\163\x73\141\x67\145\56\x63\x6c\141\x73\163\x4c\x69\x73\x74\x2e\141\144\x64\50\x27\x73\165\x63\143\x65\x73\x73\x2d\155\145\163\x73\141\x67\145\47\51\73\15\12\x20\40\40\40\57\57\40\x6d\x65\x73\x73\141\x67\x65\x2e\143\154\141\163\163\x4c\x69\163\164\56\x72\145\x6d\157\x76\x65\50\47\150\x69\x64\x65\55\x6d\145\163\163\141\147\x65\47\51\x3b\xd\12\40\x20\40\40\x2f\x2f\x20\x24\50\x27\43\163\141\155\x6c\137\x6d\x65\163\x73\x61\147\x65\x27\x29\56\150\164\x6d\x6c\x28\47" . DB::get_option("\x6d\x6f\137\163\141\155\x6c\x5f\x6d\145\x73\x73\x61\147\145") . "\x27\x29\73\xd\12\40\x20\x20\x20\x76\x61\162\40\x6d\145\163\x73\x61\147\x65\x20\x3d\x20\144\157\143\165\x6d\x65\156\x74\56\147\145\164\x45\154\145\155\145\156\164\102\x79\x4e\x61\x6d\x65\50\x22\163\x61\x6d\x6c\137\155\x65\163\x73\141\147\x65\x22\51\73\15\xa\x20\x20\40\40\x6d\x65\x73\x73\x61\147\145\x2e\x63\x6c\x61\163\163\114\151\163\x74\x2e\x61\144\x64\50\x22\163\x75\143\143\x65\163\163\x2d\155\145\x73\163\x61\147\x65\x22\x29\73\15\xa\40\x20\40\40\x6d\x65\163\x73\x61\x67\145\56\x69\156\156\x65\162\x54\x65\170\164\x20\75\x20\x22" . DB::get_option("\x6d\157\x5f\163\141\x6d\x6c\137\155\x65\x73\x73\141\x67\145") . "\x22\15\xa\40\x20\x20\x20\x3c\x2f\x73\143\162\x69\160\164\x3e";
}
function mo_saml_show_error_message()
{
    echo "\74\163\x63\162\151\160\x74\x3e\xd\12\40\40\x20\x20\166\x61\x72\x20\155\x65\163\163\141\x67\145\40\x3d\40\144\x6f\143\165\155\145\156\164\x2e\147\145\164\105\154\145\155\x65\156\x74\102\171\x4e\141\155\x65\50\42\x73\x61\x6d\x6c\x5f\x6d\x65\163\163\x61\x67\145\42\51\x3b\15\12\x20\x20\x20\x20\x6d\x65\x73\163\141\x67\x65\56\143\154\x61\163\x73\114\151\163\164\56\x61\x64\x64\x28\x22\x65\162\x72\157\162\x2d\155\145\163\163\x61\147\x65\42\51\73\15\xa\x20\40\x20\40\155\145\163\163\141\147\x65\56\x69\x6e\156\145\162\x54\x65\170\164\40\x3d\x20\42" . DB::get_option("\x6d\x6f\137\x73\141\155\154\x5f\x6d\x65\x73\163\141\x67\x65") . "\42\15\12\x20\40\x20\x20\x3c\57\x73\x63\162\x69\x70\164\x3e";
}
function mo_saml_check_empty_or_null($yY)
{
    if (!(!isset($yY) || empty($yY))) {
        goto KV;
    }
    return true;
    KV:
    return false;
}
function mo_saml_is_customer_registered()
{
    $CG = DB::get_option("\155\x6f\137\163\141\x6d\154\x5f\x61\x64\155\x69\x6e\x5f\x65\x6d\141\151\x6c");
    $QZ = DB::get_option("\x6d\157\x5f\x73\x61\x6d\154\x5f\141\144\155\151\x6e\x5f\143\x75\163\x74\x6f\155\x65\x72\137\153\145\171");
    if (!$CG || !$QZ || !is_numeric(trim($QZ))) {
        goto tH;
    }
    return 1;
    goto yo;
    tH:
    return 0;
    yo:
}
function mo_saml_show_registration_page()
{
    ?>
    
    <form name="f" method="post" action="">
                <input type="hidden" name="option" value="mo_saml_register_customer"/>
                <div class="mo_saml_table_layout" id="registration_div">
                    <h4>Register with miniOrange</h4>
                    <br/>
                    <h6>Why should I register?</h6>

                    <div style="background: aliceblue; padding: 10px 10px 10px 10px; border-radius: 10px;">
                    You should register so that in case you need help, we can help you with step by step
                            instructions. We support all known IdPs - ADFS, Okta, Salesforce, Shibboleth,
                                SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2 etc.
                                <b>You will also need a miniOrange account to upgrade to the premium version of the connector.</b> We do not store any information except the email that you will use to register with us.
                    </div>
                    <br/>
                    <div class="col-lg-8">
                    <table class="mo_saml_settings_table">
                    <tr>
                          <td><b><font color="#FF0000">*</font>Email:</b></td>
                          <td><input class="form-control" type="email" name="email"
                                      required placeholder="person@example.com"
                                      value="<?php 
    echo DB::get_option("\x6d\157\137\163\141\155\x6c\x5f\141\144\155\x69\156\x5f\x65\x6d\x61\151\x6c") == '' ? DB::get_option("\x61\144\155\151\x6e\x5f\x65\x6d\x61\151\x6c") : DB::get_option("\x6d\157\x5f\163\x61\x6d\x6c\137\x61\144\x6d\x69\156\137\x65\x6d\141\x69\x6c");
    ?>
"/>
                          </td>
                      </tr>
                      <tr>&nbsp;</tr>
                      <tr>
                          <td><b><font color="#FF0000">*</font>Password:</b></td>
                          <td><input class="form-control" required type="password"
                                      name="password" placeholder="Choose your password (Min. length 6)"
                                      minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                      title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."
                                      /></td>
                      </tr>
                      <tr>
                          <td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
                          <td><input class="form-control" required type="password"
                                      name="confirmPassword" placeholder="Confirm your password"
                                      minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                      title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."

                                      /></td>
                      </tr>
                      <tr>
                          <td>&nbsp;</td>
                          <td><br><input type="submit" name="submit" value="Register" id="register_action"
                                          class="btn btn-primary"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                          <input type="button" name="mo_saml_goto_login" id="mo_saml_goto_login"
                                      value="Already have an account?" class="btn btn-primary"/>&nbsp;&nbsp;

                          </td>
                      </tr>
                  </table></div>
                </div>
                </div>
                </form>
                <form name="f1" method="post" action="" id="mo_saml_goto_login_form">
                <input type="hidden" name="option" value="mo_saml_goto_login"/>
                </form>
                <form name="f" method="post" action="" id="mo_saml_continue_guest">
                    <input type="hidden" name="option" value="mo_continue_as_guest"/>
                </form>

                <!-- <form name="f2" method="post" action="" id="mo_saml_register_action_form">
                  <input type="hidden" name="option" value="mo_saml_register_action"/>
                </form> -->
                
                <script>
                    jQuery("#mo_saml_goto_login").click(function () {
                        jQuery("#mo_saml_goto_login_form").submit();
                    });
                </script>
              </div>
              <?php 
}
function mo_saml_show_verify_password_page()
{
    ?>
    <form name="f" method="post" action="">
    <input type="hidden" name="option" value="mo_saml_verify_customer"/>
    <div class="mo_saml_table_layout">
  <div id="toggle1" class="panel_toggle">
      <h3>Login with miniOrange</h3>
  </div>
  <div id="panel1">
      <p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email
              and password.<br/> <a target="_blank"
                                    href="https://auth.miniorange.com/moas/idp/resetpassword">Click
                  here if you forgot your password?</a></b></p>
      <br/>
      <div class="col-lg-8">
      <table class="mo_saml_settings_table">
          <tr>
              <td><b><font color="#FF0000">*</font>Email:</b></td>
              <td><input class="form-control" type="email" name="email"
                         required placeholder="person@example.com"
                         value="<?php 
    echo DB::get_option("\x6d\157\137\163\x61\x6d\x6c\x5f\141\x64\155\151\156\x5f\145\155\x61\x69\154");
    ?>
"/></td>
          </tr>
          <tr>
              <td><b><font color="#FF0000">*</font>Password:</b></td>
              <td><input class="form-control" required type="password"
                         name="password" placeholder="Enter your password"
                         minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                         title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."

                         /></td>
          </tr>
          <tr>
              <td>&nbsp;</td>
              <td>
                  <input type="submit" name="submit" value="Login"
                         class="btn btn-primary"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input type="button" name="mo_saml_goback" id="mo_saml_goback" value="Back"
                         class="btn btn-primary"/>
          </tr>
      </table>
      </div>
  </div>
</div>
  </form>

        <form name="f" method="post" action="" id="mo_saml_goback_form">
            <input type="hidden" name="option" value="mo_saml_go_back"/>
        </form>
        <form name="f" method="post" action="" id="mo_saml_forgotpassword_form">
            <input type="hidden" name="option" value="mo_saml_forgot_password_form_option"/>
        </form>
        <script>
            jQuery("#mo_saml_goback").click(function () {
            jQuery("#mo_saml_goback_form").submit();
            });
            jQuery("a[href=\"#mo_saml_forgot_password_link\"]").click(function () {
            jQuery("#mo_saml_forgotpassword_form").submit();
            });
        </script>
        <?php 
}
function mo_saml_show_customer_details()
{
    ?>
     <div class="mo_saml_table_layout" >
                <h2>Thank you for registering with miniOrange.</h2>

                <table border="1"
                   style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
                <tr>
                    <td style="width:45%; padding: 10px;">miniOrange Account Email</td>
                    <td style="width:55%; padding: 10px;"><?php 
    echo DB::get_option("\x6d\157\x5f\163\x61\x6d\154\x5f\x61\x64\x6d\151\156\x5f\145\155\x61\x69\154");
    ?>
</td>
                </tr>
                <tr>
                    <td style="width:45%; padding: 10px;">Customer ID</td>
                    <td style="width:55%; padding: 10px;"><?php 
    echo DB::get_option("\155\157\x5f\163\x61\155\154\x5f\x61\144\x6d\x69\156\137\x63\x75\x73\164\157\x6d\145\162\x5f\153\x65\x79");
    ?>
</td>
                </tr>
                </table>
                <br /><br />

            <table>
            <tr>
            <td>
            <form name="f1" method="post" action="" id="mo_saml_goto_login_form">
                <input type="hidden" value="change_miniorange" name="option"/>
                <input type="submit" value="Change Email Address" class="btn btn-primary"/>
            </form>
            </td><td>
            <a href="#"><input type="button" class="btn btn-primary"  onclick="upgradeform('php_saml_connector_premium_plan')" value="Upgrade to Premium"/></a>
            </td>
            </tr>
            </table>

            <br />

            <form style="display:none;" id="loginform"
                 action="<?php 
    echo DB::get_option("\x6d\x6f\x5f\163\x61\x6d\154\x5f\x68\x6f\163\164\137\156\x61\155\145") . "\x2f\x6d\157\141\x73\x2f\x6c\x6f\x67\151\x6e";
    ?>
"
                 target="_blank" method="post">
                <input type="email" name="username" value="<?php 
    echo DB::get_option("\155\x6f\137\x73\x61\x6d\154\x5f\141\144\155\x69\156\x5f\x65\x6d\141\x69\154");
    ?>
"/>
                <input type="text" name="redirectUrl"
                    value="<?php 
    echo DB::get_option("\x6d\157\x5f\x73\x61\155\x6c\x5f\150\157\x73\x74\x5f\x6e\x61\155\145") . "\x2f\x6d\157\141\163\57\151\156\x69\x74\x69\x61\154\x69\x7a\x65\x70\141\x79\155\145\156\164";
    ?>
"/>
                <input type="text" name="requestOrigin" id="requestOrigin"/>
            </form>
            <script>
                function upgradeform(planType) {
                    jQuery('#requestOrigin').val(planType);
                    if (jQuery('#mo_customer_registered').val()==1)
                        jQuery('#loginform').submit();

                }
            </script>
            </div>
            <?php 
}
function mo_saml_remove_account()
{
    DB::delete_option("\x6d\x6f\x5f\163\x61\x6d\154\x5f\x68\x6f\163\164\x5f\x6e\x61\x6d\145");
    DB::delete_option("\155\x6f\137\163\x61\155\154\x5f\x6e\145\167\137\162\x65\x67\151\163\x74\x72\x61\x74\151\x6f\x6e");
    DB::delete_option("\x6d\x6f\x5f\x73\141\x6d\154\137\x61\x64\155\x69\156\x5f\160\x68\x6f\x6e\145");
    DB::delete_option("\x6d\x6f\x5f\163\141\x6d\154\x5f\141\144\155\x69\x6e\x5f\160\x61\163\163\167\x6f\x72\x64");
    DB::delete_option("\x6d\x6f\x5f\x73\x61\x6d\154\137\x76\x65\162\151\146\x79\137\143\x75\163\x74\x6f\x6d\x65\162");
    DB::delete_option("\155\157\x5f\x73\x61\x6d\x6c\137\x61\144\155\151\156\x5f\x63\165\163\164\157\x6d\145\162\x5f\153\x65\171");
    DB::delete_option("\x6d\x6f\137\x73\x61\x6d\x6c\x5f\141\144\x6d\151\x6e\x5f\x61\160\151\x5f\153\145\x79");
    DB::delete_option("\x6d\157\x5f\163\141\x6d\x6c\137\x63\165\x73\164\x6f\155\145\x72\x5f\x74\157\x6b\145\x6e");
    DB::delete_option("\x6d\x6f\x5f\x73\141\155\x6c\137\x61\x64\155\151\156\x5f\145\x6d\141\x69\154");
    DB::delete_option("\x6d\157\137\163\x61\x6d\x6c\x5f\155\x65\163\x73\x61\x67\x65");
    DB::delete_option("\x6d\x6f\137\163\141\155\x6c\137\162\x65\x67\151\x73\x74\x72\x61\x74\x69\157\156\x5f\163\164\141\164\165\x73");
    DB::delete_option("\155\157\x5f\x73\x61\155\154\x5f\151\x64\x70\x5f\143\157\156\146\x69\147\137\x63\157\155\160\154\x65\x74\x65");
    DB::delete_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\137\x74\x72\x61\x6e\x73\x61\143\164\x69\157\x6e\111\x64");
}
function checkPasswordpattern($JK)
{
    $mG = "\57\136\133\50\x5c\167\51\x2a\x28\x5c\41\134\x40\134\x23\134\44\134\x25\x5c\x5e\x5c\46\134\x2a\x5c\x2e\x5c\55\134\x5f\x29\52\135\x2b\44\57";
    return !preg_match($mG, $JK);
}
function mo_saml_is_curl_installed()
{
    if (in_array("\x63\165\x72\x6c", get_loaded_extensions())) {
        goto AR;
    }
    return 0;
    goto SN;
    AR:
    return 1;
    SN:
}
function mo_saml_is_customer_registered_saml($fY = true)
{
    $CG = DB::get_option("\155\157\x5f\163\x61\x6d\154\x5f\141\x64\155\151\x6e\137\145\155\141\151\x6c");
    $QZ = DB::get_option("\x6d\x6f\137\x73\x61\155\x6c\137\141\144\x6d\151\156\137\143\x75\x73\x74\157\155\145\162\x5f\x6b\x65\171");
    if (!(mo_saml_is_guest_enabled() && $fY)) {
        goto iS;
    }
    return 1;
    iS:
    if (!$CG || !$QZ || !is_numeric(trim($QZ))) {
        goto JH;
    }
    return 1;
    goto oC;
    JH:
    return 0;
    oC:
}
function mo_saml_is_guest_enabled()
{
    $Tb = DB::get_option("\155\157\x5f\x73\x61\155\x6c\137\x67\165\145\163\x74\137\x65\156\x61\x62\x6c\145\144");
    return $Tb;
}
function saml_get_current_page_url()
{
    $Lb = $_SERVER["\110\124\124\x50\x5f\110\x4f\123\124"];
    if (!(substr($Lb, -1) == "\57")) {
        goto nX;
    }
    $Lb = substr($Lb, 0, -1);
    nX:
    $gN = $_SERVER["\122\x45\121\125\105\123\124\x5f\125\122\x49"];
    if (!(substr($gN, 0, 1) == "\x2f")) {
        goto kl;
    }
    $gN = substr($gN, 1);
    kl:
    $ay = isset($_SERVER["\x48\124\124\x50\x53"]) && strcasecmp($_SERVER["\110\124\124\120\x53"], "\157\x6e") == 0;
    $g4 = "\150\x74\x74\x70" . ($ay ? "\x73" : '') . "\72\57\x2f" . $Lb . "\x2f" . $gN;
    return $g4;
}
?>
