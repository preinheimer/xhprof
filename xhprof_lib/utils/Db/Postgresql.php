<?php

/*
NOTES:
 - for PostgreSQL 9.1+ creating the table as UNLOGGED makes a lot of sense for performance reasons if you are ok with
   loosing the data on a server crash and not streaming the data to any existing slaves.

 - unlike say MySQL, varchar() is just a length constrained TEXT datatype in Postgres. The id field is made longer here
   than in the other DB classes and there would be nothing stopping you from just using TEXT instead.

 - make sure to create your xhprof database with UTF-8 encoding.

CREATE TABLE details (
  id varchar(64) NOT NULL PRIMARY KEY,
  url text,
  c_url text,
  timestamp timestamp NOT NULL default NOW(),
  "server name" varchar(64),
  perfdata bytea,
  type smallint,
  cookie bytea,
  post bytea,
  get bytea,
  pmu bigint,
  wt bigint,
  cpu bigint,
  server_id TEXT NOT NULL default 't11',
  "aggregateCalls_include" varchar(255)
);

CREATE INDEX details_url_idx ON details(url);
CREATE INDEX details_c_url_idx ON details(c_url);
CREATE INDEX details_cpu_idx ON details(cpu);
CREATE INDEX details_wt_idx ON details(wt);
CREATE INDEX details_pmu_idx ON details(pmu);
CREATE INDEX details_timestamp_idx ON details(timestamp);
 */


require_once XHPROF_LIB_ROOT.'/utils/Db/Abstract.php';
class Db_Postgresql extends Db_Abstract
{

    public function connect()
    {
        $connectionString = sprintf(
            'host=%s dbname=%s user=%s', $this->config['dbhost'], $this->config['dbname'], $this->config['dbuser']
        );

        // @TODO migrate dbport to the other abstractions too!
        if (isset($this->config['dbport'])) {
            $connectionString .= sprintf(' port=%s', $this->config['dbport']);
        }

        // allow passwordless login, typical in dev
        if (strlen($this->config['dbpass'])) {
            $connectionString .= sprintf(' password=%s', $this->config['dbpass']);
        }

        $linkid = pg_connect($connectionString);
        if ($linkid === FALSE)
        {
            xhprof_error("Could not connect to db");
            throw new Exception("Unable to connect to database");
        }
        $this->linkID = $linkid;
        pg_set_client_encoding($this->linkID, "UTF8");
    }

    public function query($sql)
    {
        return pg_query($this->linkID, $sql);
    }

    public static function getNextAssoc($resultSet)
    {
        return pg_fetch_assoc($resultSet);
    }

    public function escape($str)
    {
        return pg_escape_string($this->linkID, $str);
    }

    public function escapeBinary($data)
    {
        // @see http://www.php.net/manual/en/function.pg-escape-bytea.php#89036
        return str_replace(array("\\\\", "''"), array("\\", "'"), pg_escape_bytea($this->linkID, $data));
    }

    public function unescapeBinary($data)
    {
        return pg_unescape_bytea($data);
    }

    public function affectedRows($resultSet)
    {
        return pg_affected_rows($resultSet);
    }

    public function quote($identifier)
    {
        return '"' . $identifier . '"';
    }

    public function unixTimestamp($field)
    {
        return "extract(epoch from $field)";
    }

    public function fromUnixTimestamp($field)
    {
        return "to_timestamp($field)";
    }

    public function dateSub($days)
    {
        return "(current_date - '$days days'::interval)";
    }

}