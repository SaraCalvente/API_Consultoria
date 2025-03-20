<?php


define("\115\x53\123\x50\x5f\126\x45\122\123\x49\117\x4e", "\x31\56\60\x2e\x30");
define("\x4d\123\123\120\x5f\116\101\115\105", basename(__DIR__));
define("\115\x53\x53\x50\x5f\104\x49\122", __DIR__);
define("\115\x53\123\120\x5f\x54\105\x53\x54\x5f\x4d\x4f\x44\x45", FALSE);
class SplClassLoader
{
    private $_fileExtension = "\x2e\x70\150\x70";
    private $_namespace;
    private $_includePath;
    private $_namespaceSeparator = "\134";
    public function __construct($hF = null, $tK = null)
    {
        $this->_namespace = $hF;
        $this->_includePath = $tK;
    }
    public function register()
    {
        spl_autoload_register(array($this, "\x6c\157\141\x64\103\154\141\x73\163"));
    }
    public function unregister()
    {
        spl_autoload_unregister(array($this, "\154\157\141\144\103\154\141\163\x73"));
    }
    public function loadClass($ok)
    {
        if (!(null === $this->_namespace || $this->_namespace . $this->_namespaceSeparator === substr($ok, 0, strlen($this->_namespace . $this->_namespaceSeparator)))) {
            goto jg;
        }
        $ns = '';
        $xW = '';
        if (!(false !== ($xj = strripos($ok, $this->_namespaceSeparator)))) {
            goto Ex;
        }
        $xW = strtolower(substr($ok, 0, $xj));
        $ok = substr($ok, $xj + 1);
        $ns = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $xW) . DIRECTORY_SEPARATOR;
        Ex:
        $ns .= str_replace("\137", DIRECTORY_SEPARATOR, $ok) . $this->_fileExtension;
        $ns = str_replace("\x6d\151\x6e\x69\157\162\x61\156\x67\145", MSSP_NAME, $ns);
        require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $ns;
        jg:
    }
}
$KY = new SplClassLoader("\x4d\x69\x6e\x69\117\162\x61\x6e\147\x65", realpath(__DIR__ . DIRECTORY_SEPARATOR . "\56\56"));
$KY->register();
