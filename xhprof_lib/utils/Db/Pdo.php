<?php

/**
 * When setting the `id` column, consider the length of the prefix you're specifying in $this->prefix
 *
 *
 CREATE TABLE `details` (
 `id` char(17) NOT NULL,
 `url` varchar(255) default NULL,
 `c_url` varchar(255) default NULL,
 `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 `server name` varchar(64) default NULL,
 `perfdata` MEDIUMBLOB,
 `type` tinyint(4) default NULL,
 `cookie` BLOB,
 `post` BLOB,
 `get` BLOB,
 `pmu` int(11) unsigned default NULL,
 `wt` int(11) unsigned default NULL,
 `cpu` int(11) unsigned default NULL,
 `server_id` char(3) NOT NULL default 't11',
 `aggregateCalls_include` varchar(255) DEFAULT NULL,
 PRIMARY KEY  (`id`),
 KEY `url` (`url`),
 KEY `c_url` (`c_url`),
 KEY `cpu` (`cpu`),
 KEY `wt` (`wt`),
 KEY `pmu` (`pmu`),
 KEY `timestamp` (`timestamp`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 */

require_once XHPROF_LIB_ROOT.'/utils/Db/Abstract.php';
class Db_Pdo extends Db_Abstract
{
    protected $curStmt;
    protected $db;
    
    public function connect()
    {
        $connectionString = $this->config['dbtype'] . ':host=' . $this->config['dbhost'] . ';dbname=' . $this->config['dbname'];
        $db = new PDO($connectionString, $this->config['dbuser'], $this->config['dbpass']);
        if ($db === FALSE)
        {
            xhprof_error("Could not connect to db");
            $run_desc = "could not connect to db";
            throw new Exception("Unable to connect to database");
            return false;
        }
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
    public function query($sql)
    {
        $this->curStmt = $this->db->query($sql);
        return $this->curStmt;
    }
    
    public static function getNextAssoc($resultSet)
    {
        return $resultSet->fetch();
    }
    
    public function escape($str)
    {
        $str = $this->db->quote($str);
        //Dirty trick, PDO::quote add quote around values (you're beautiful => 'you\'re beautiful')
        // which are already added in xhprof_runs.php
        $str = substr($str, 0, -1);
        $str = substr($str, 1);
        return $str;
    }

    public function escapeBinary($data)
    {
        return $this->escape($data);
    }

    public function unescapeBinary($data)
    {
        // did not have any binary unescaping before introducing this method to Db_Abstract, needed?
        return $data;
    }

    public function affectedRows($resultSet)
    {
        // NOTE: PDO does not need $resultSet, unused on purpose
        if ($this->curStmt === false) {
            return 0;
        }
        return $this->curStmt->rowCount();
    }

    public function quote($identifier)
    {
        // @TODO how should we handle this in PDO? https://bugs.php.net/bug.php?id=38196
        return "`$identifier`";
    }
    
    public function unixTimestamp($field)
    {
        return 'UNIX_TIMESTAMP('.$field.')';
    }
    
    public function fromUnixTimestamp($field)
    {
        // @TODO is this correct syntax?
        return 'FROM_UNIXTIME(' . $field . ')';
    }

    public function dateSub($days)
    {
        return 'DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)';
    }
}