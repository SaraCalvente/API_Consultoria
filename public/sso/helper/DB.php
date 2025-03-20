<?php


class DB
{
    public static function get_option($w5)
    {
        $zV = self::get_options();
        if (!empty($zV)) {
            goto p1;
        }
        return false;
        goto Ce;
        p1:
        if (array_key_exists($w5, $zV)) {
            goto m9;
        }
        return false;
        goto Rp;
        m9:
        return $zV[$w5];
        Rp:
        Ce:
    }
    public static function update_option($w5, $yY)
    {
        $ee = self::get_options();
        if (!empty($ee)) {
            goto KS;
        }
        $ee = array();
        KS:
        $nh = array($w5 => $yY);
        $IY = array_merge($ee, $nh);
        $Lx = self::getOptionsFilePath();
        $ic = json_encode($IY, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($Lx, $ic);
    }
    public static function delete_option($w5)
    {
        $zV = self::get_options();
        unset($zV[$w5]);
        $Lx = self::getOptionsFilePath();
        $ic = json_encode($zV, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($Lx, $ic);
    }
    private static function get_options()
    {
        $Qc = '';
        if (!file_exists(self::getOptionsFilePath())) {
            goto Sa;
        }
        $Qc = file_get_contents(self::getOptionsFilePath());
        Sa:
        $SF = json_decode($Qc, true);
        return $SF;
    }
    public static function getOptionsFilePath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "\x64\141\164\x61" . DIRECTORY_SEPARATOR . "\157\x70\164\x69\157\156\x73\56\152\163\157\x6e";
    }
}
?>
