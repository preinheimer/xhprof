<?php
abstract class Db_Abstract
{
    protected $config;
    public $linkID;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    abstract public function connect();
    abstract public function query($sql);
    abstract public function escape($str);
    abstract public function escapeBinary($data);
    abstract public function unescapeBinary($data);
    abstract public function affectedRows($resultSet);

    public function quote($identifier)
    {
        throw new RuntimeException("Method '".get_called_class()."::".__FUNCTION__."' not implemented");
    }

    public function unixTimestamp($field)
    {
        throw new RuntimeException("Method '".get_called_class()."::".__FUNCTION__."' not implemented");
    }

    public function fromUnixTimestamp($field)
    {
        throw new RuntimeException("Method '".get_called_class()."::".__FUNCTION__."' not implemented");
    }

    public function dateSub($days)
    {
        throw new RuntimeException("Method '".get_called_class()."::".__FUNCTION__."' not implemented");
    }

    public static function getNextAssoc($resultSet)
    {
        throw new RuntimeException("Method '".get_called_class()."::".__FUNCTION__."' not implemented");
    }
    
    
}
