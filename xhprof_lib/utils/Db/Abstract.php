<?php
require_once XHPROF_LIB_ROOT.'/utils/Db/Interface.php';
abstract class Db_Abstract implements Db_Interface
{
    protected $config;
    public $linkID;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
}