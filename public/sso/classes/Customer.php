<?php


if (class_exists("\104\x42")) {
    goto c1;
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x68\145\154\160\145\162" . DIRECTORY_SEPARATOR . "\x44\102\x2e\160\x68\160";
c1:
class CustomerSaml
{
    public $email;
    function create_customer()
    {
        $eb = DB::get_option("\x6d\x6f\x5f\163\x61\155\x6c\137\x68\157\163\x74\x5f\156\141\155\145") . "\57\155\x6f\141\x73\x2f\162\145\x73\164\57\143\165\x73\x74\157\x6d\145\x72\x2f\x61\x64\x64";
        $Ac = curl_init($eb);
        $this->email = DB::get_option("\x6d\157\137\x73\x61\155\x6c\x5f\141\x64\x6d\x69\x6e\137\x65\x6d\141\151\154");
        $JK = DB::get_option("\155\x6f\137\163\x61\x6d\x6c\137\141\x64\x6d\x69\x6e\x5f\x70\x61\163\163\x77\157\162\x64");
        $nt = array("\x63\x6f\155\x70\141\156\x79\x4e\141\155\145" => $_SERVER["\x53\105\x52\x56\x45\x52\x5f\x4e\x41\115\x45"], "\x61\x72\x65\x61\117\x66\111\x6e\x74\145\x72\x65\163\164" => "\x50\110\120\x20\x53\101\x4d\x4c\40\x32\56\60\x20\103\x6f\x6e\x6e\145\143\x74\x6f\162", "\x65\155\x61\151\x6c" => $this->email, "\160\141\x73\x73\x77\157\162\x64" => $JK);
        $Nt = json_encode($nt);
        curl_setopt($Ac, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ac, CURLOPT_ENCODING, '');
        curl_setopt($Ac, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Ac, CURLOPT_AUTOREFERER, true);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($Ac, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Ac, CURLOPT_HTTPHEADER, array("\103\157\x6e\x74\145\156\164\55\x54\171\x70\x65\72\40\141\160\x70\x6c\151\x63\x61\164\x69\x6f\x6e\x2f\x6a\163\157\x6e", "\143\x68\141\162\163\x65\x74\x3a\40\x55\x54\106\x20\55\40\x38", "\101\x75\x74\x68\157\162\151\x7a\x61\164\151\x6f\156\x3a\40\x42\141\x73\x69\x63"));
        curl_setopt($Ac, CURLOPT_POST, true);
        curl_setopt($Ac, CURLOPT_POSTFIELDS, $Nt);
        $c0 = curl_exec($Ac);
        if (!curl_errno($Ac)) {
            goto jj;
        }
        echo "\x52\x65\161\165\x65\x73\x74\40\105\162\x72\157\162\72" . curl_error($Ac);
        die;
        jj:
        curl_close($Ac);
        return $c0;
    }
    function submit_contact_us($CG, $Uh, $Jx)
    {
        $Jx = "\x5b\x50\110\x50\40\x53\x41\115\114\40\x32\56\60\40\x43\157\156\156\x65\x63\164\x6f\x72\x5d\40" . $Jx;
        $nt = array("\143\157\155\160\141\156\x79" => $_SERVER["\123\x45\122\x56\105\x52\137\x4e\x41\x4d\105"], "\x65\x6d\141\151\154" => $CG, "\160\x68\x6f\156\x65" => $Uh, "\161\165\145\162\171" => $Jx);
        $Nt = json_encode($nt);
        $eb = DB::get_option("\155\x6f\x5f\163\141\x6d\154\x5f\x68\157\x73\164\x5f\156\x61\155\x65") . "\x2f\155\157\x61\x73\57\x72\145\x73\x74\x2f\x63\165\163\164\157\155\x65\x72\57\x63\157\156\164\x61\x63\x74\55\x75\x73";
        $Ac = curl_init($eb);
        curl_setopt($Ac, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ac, CURLOPT_ENCODING, '');
        curl_setopt($Ac, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Ac, CURLOPT_AUTOREFERER, true);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($Ac, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Ac, CURLOPT_HTTPHEADER, array("\103\157\156\x74\x65\156\x74\x2d\x54\171\160\145\72\40\x61\x70\160\154\x69\x63\x61\x74\x69\x6f\156\x2f\x6a\x73\x6f\156", "\143\x68\141\162\x73\x65\164\72\40\x55\x54\x46\x2d\70", "\101\165\164\x68\x6f\x72\151\x7a\x61\164\151\157\156\x3a\x20\x42\x61\163\x69\143"));
        curl_setopt($Ac, CURLOPT_POST, true);
        curl_setopt($Ac, CURLOPT_POSTFIELDS, $Nt);
        $c0 = curl_exec($Ac);
        if (!curl_errno($Ac)) {
            goto TX;
        }
        echo "\122\145\x71\165\x65\163\164\x20\x45\x72\162\157\162\x3a" . curl_error($Ac);
        return false;
        TX:
        curl_close($Ac);
        return true;
    }
    function check_customer()
    {
        $eb = DB::get_option("\155\x6f\137\163\141\155\x6c\x5f\150\x6f\x73\164\137\x6e\x61\155\x65") . "\57\x6d\157\x61\163\57\162\145\x73\164\x2f\143\x75\x73\x74\x6f\155\145\x72\x2f\143\x68\145\143\153\x2d\x69\x66\55\145\x78\151\x73\164\x73";
        $Ac = curl_init($eb);
        $CG = DB::get_option("\x6d\157\137\163\141\155\x6c\137\141\144\155\x69\x6e\x5f\x65\x6d\141\x69\154");
        $nt = array("\145\155\x61\x69\x6c" => $CG);
        $Nt = json_encode($nt);
        curl_setopt($Ac, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ac, CURLOPT_ENCODING, '');
        curl_setopt($Ac, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Ac, CURLOPT_AUTOREFERER, true);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($Ac, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Ac, CURLOPT_HTTPHEADER, array("\103\157\x6e\164\x65\x6e\x74\55\x54\171\x70\145\72\40\141\x70\160\x6c\151\143\x61\164\151\x6f\x6e\x2f\x6a\163\157\156", "\143\x68\x61\x72\x73\x65\164\x3a\x20\x55\124\x46\40\55\40\70", "\101\165\x74\x68\157\x72\x69\172\x61\164\151\x6f\x6e\x3a\x20\102\x61\163\x69\143"));
        curl_setopt($Ac, CURLOPT_POST, true);
        curl_setopt($Ac, CURLOPT_POSTFIELDS, $Nt);
        $c0 = curl_exec($Ac);
        if (!curl_errno($Ac)) {
            goto JI;
        }
        echo "\x45\162\x72\157\x72\40\x69\156\x20\163\x65\x6e\x64\x69\x6e\147\40\x63\165\162\154\40\x52\145\161\x75\145\x73\164";
        die;
        JI:
        curl_close($Ac);
        return $c0;
    }
    function get_customer_key()
    {
        $eb = DB::get_option("\x6d\x6f\137\163\141\155\x6c\x5f\x68\157\x73\164\137\156\x61\x6d\145") . "\57\x6d\157\x61\x73\57\162\145\x73\164\x2f\x63\x75\163\164\x6f\x6d\145\162\x2f\153\145\x79";
        $Ac = curl_init($eb);
        $CG = DB::get_option("\155\157\137\163\x61\x6d\x6c\x5f\x61\144\155\x69\x6e\137\145\x6d\x61\151\x6c");
        $JK = DB::get_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\137\141\x64\155\x69\x6e\137\160\141\x73\x73\x77\157\162\144");
        $nt = array("\x65\155\141\151\x6c" => $CG, "\x70\x61\x73\x73\x77\157\162\x64" => $JK);
        $Nt = json_encode($nt);
        curl_setopt($Ac, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ac, CURLOPT_ENCODING, '');
        curl_setopt($Ac, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Ac, CURLOPT_AUTOREFERER, true);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Ac, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($Ac, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Ac, CURLOPT_HTTPHEADER, array("\x43\157\x6e\x74\x65\156\164\55\124\x79\x70\x65\x3a\x20\x61\160\x70\154\x69\x63\141\164\x69\x6f\156\57\152\163\157\156", "\x63\x68\141\162\x73\x65\x74\x3a\x20\125\x54\x46\x20\x2d\40\x38", "\x41\x75\164\150\x6f\x72\151\172\x61\x74\151\157\x6e\72\40\102\x61\163\151\143"));
        curl_setopt($Ac, CURLOPT_POST, true);
        curl_setopt($Ac, CURLOPT_POSTFIELDS, $Nt);
        $c0 = curl_exec($Ac);
        if (!curl_errno($Ac)) {
            goto Ly;
        }
        echo "\105\x72\x72\x6f\x72\40\151\x6e\40\163\x65\x6e\144\x69\x6e\x67\x20\143\x75\162\154\x20\x52\x65\161\165\145\163\164";
        die;
        Ly:
        curl_close($Ac);
        return $c0;
    }
}
?>
