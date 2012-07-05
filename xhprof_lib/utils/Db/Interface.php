<?php
interface Db_Interface
{
    public function connect();
    public function query($sql);
    public static function getNextAssoc($resultSet);
    public function escape($str);
    public function affectedRows();
    public static function unixTimestamp($field);
    public static function dateSub($days);
}