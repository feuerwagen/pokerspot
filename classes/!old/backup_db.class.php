<?php
/**
* db access wrapper for mysql databases.
* This class manages the access to a MySQL database. It provides functions for sending db querys,
* getting the result datasets and gaining various information about the db and the requests.
*
* @author: Elias Müller
* @version: 0.1
* @since: chispa 0.1
* @package: core
* @subpackage: general
*/
class DB {
    /**
    * connection parameters 
    */
    static private $config = array(
        "host"=>"",
        "database"=>"",
        "user"=>"",
        "password"=>""
    );
    
    /**
    * config: set to 1 for automatic mysql_free_result()
    * @var bool
    */
    private $auto_free = 0;
    
    /**
    * config: set to 1 for debugging messages
    * @var bool
    * @todo replace by systemwide debugging setting
    */
    private $debug = 0;
    
    /**
    * config: set the error handling method
    * @var string "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
    */
    private $halt_on_error = "yes";
    
    /**
    * the table, where the next ids (primary keys) for every other table are stored
    */
    private $seq_table = "sequence";
    
    /**
    * result array
    */
    private $record = array();
    
    /**
    * current row number 
    */
    private $row;
    
    /**
    * current error number 
    */
    public $errno = 0;
    
    /**
    * current error text 
    */
    public $error = "";
    
    /**
    * link and query handles 
    */
    private $link_id = 0;
    private $query_id = 0;
    
    /**
    * load config values, if availiable
    */
    public function __construct() {
        // Load config-Params from config-class, if availiable (only once -> static)
        if(class_exists("cBootstrap") && empty(self::$config["host"])) {
            $loader = cBootstrap::getInstance();
            self::$config = $loader->getConfig("db");
        }	
    }

    /**
    * some trivial reporting 
    *
    * @return int connection id (the database link)
    */
    public function linkId() {
        return $this->link_id;
    }
    
    /**
    * more trivial reporting
    *
    * @return bool 1 if last db query succeded, 0 if not
    */
    public function queryId() {
        return $this->query_id;
    }

    /** 
    * connection management
    *
    * @param string $Database db name if no config param loaded
    * @param string $Host db host if no config param loaded
    * @param string $User db usernmae if no config param loaded
    * @param string $Password db password if no config param loaded
    * @return bool|int 0 if connection error, else the link id
    */
    public function connect($host = "", $database = "", $user = "", $password = "") {
        /* Handle defaults */
        if ("" == $host)
            $host = self::$config["host"];
        if ("" == $database)
            $database = self::$config["database"];
        if ("" == $user)
            $user = self::$config["user"];
        if ("" == $password)
            $password = self::$config["password"];
        
        // establish connection, select database
        if (0 == $this->link_id) {
            $this->link_id = mysql_connect($host, $user, $password);
        	mysql_query("SET NAMES 'utf8'");
        	//mysql_query("SET CHARACTER SET 'utf8'");
        	
            if (!$this->link_id) {
                $this->halt("connect($host, $user, \$password) failed.");
                return 0;
            }
        
            if (!@mysql_select_db($database, $this->link_id)) {
                $this->halt("cannot use database ".$Database);
                return 0;
            }
        }
        return $this->link_id;
    }

    /**
    * discard the query result 
    */
    public function free() {
        @mysql_free_result($this->query_id);
        $this->query_id = 0;
    }

    /**
    * perform a query 
    *
    * @param string $query_string the query string
    * @return int the query id or 0 if query failed
    */
    public function query($query_string) {
        if (!$this->connect())
            return 0; // we already complained in connect() about that.
        
        // New query, discard previous result.
        if ($this->query_id)
            $this->free();
        
        // print query, if debugging is turned on
        if ($this->debug)
            printf("Debug: query = %s<br>\n", $query_string);
        
        $this->query_id = @mysql_query($query_string, $this->link_id);
        $this->row   = 0;
        $this->errno = mysql_errno();
        $this->error = mysql_error();
        
        if (!$this->query_id)
            $this->halt("Invalid SQL: ".$query_string."<br><br>");
        
        // Will return nada if it fails. That's fine.
        return $this->query_id;
    }

    /**
    * walk the result set 
    * 
    * @return bool 
    */
    public function nextRecord() {
        if (!$this->query_id) {
            $this->halt("nextRecord called with no query pending.");
            return 0;
        }
        
        $this->record = @mysql_fetch_array($this->query_id);
        $this->row   += 1;
        $this->errno  = mysql_errno();
        $this->error  = mysql_error();
        
        $stat = is_array($this->record);
        if (!$stat && $this->auto_free) {
            $this->free();
        }
        return $stat;
    }

    /**
    * change the position in the result set 
    *
    * @param int $pos the new position
    * @return bool false if $pos is out of result range 
    */
    public function seek($pos = 0) {
        $status = @mysql_data_seek($this->Query_ID, $pos);
        if ($status) {
            $this->row = $pos;
        } else {
            $this->halt("seek($pos) failed: result has ".$this->numRows()." rows.");
            return 0;
        }
        
        return 1;
    }

    /**
    * table locking (types: "read", "read local", "write", "low priority write")
    *
    * @param string|array $table either name of the table to lock or associative array with $mode => $table entries
    * @param string $mode if $table is one table name, $mode is the lock type for it
    */
    public function lock($table, $mode = "write") {
        $query = "LOCK TABLES ";
        if (is_array($table)) {
            while (list($key,$value) = each($table)) {
                if (!is_int($key)) {
            	    // texts key are "read", "read local", "write", "low priority write"
                    $query .= "$value $key, ";
                } else {
                    $query .= "$value $mode, ";
                }
            }
            $query = substr($query,0,-2);
        } else {
            $query .= "$table $mode";
        }
        $res = $this->query($query);
        if (!$res) {
            $this->halt("lock() failed.");
            return 0;
        }
        return $res;
    }

    /**
    * unlock all tables
    *
    * @return bool false if unlock failed
    */
    public function unlock() {
        $res = $this->query("UNLOCK TABLES");
        if (!$res) {
            $this->halt("unlock() failed.");
            return 0;
        }
        return $res;
    }

    /**
    * evaluate the result: number of db entries involved
    * 
    * @return int
    */
    public function affectedRows() {
        return @mysql_affected_rows($this->link_id);
    }
    
    /**
    * evaluate the result: number of returned datasets
    * 
    * @return int
    */
    public function numRows() {
        return @mysql_num_rows($this->query_id);
    }
    
    /**
    * evaluate the result: number of returned data fields
    * 
    * @return int
    */
    public function numFields() {
        return @mysql_num_fields($this->query_id);
    }

    /** 
    * shorthand notation for numRows() 
    *
    * @return int
    */
    public function nf() {
        return $this->numRows();
    }
    
    /** 
    * shorthand notation: print numRows() result 
    */
    public function np() {
        print $this->numRows();
    }

    /** 
    * shorthand notation: return a field out of the current record
    *
    * @return mixed 
    */
    public function f($name) {
        if (isset($this->record[$name]))
            return $this->record[$name];
    }
    
    /** 
    * shorthand notation: print a field out of the current record
    */
    public function p($name) {
        if (isset($this->record[$name]))
            print $this->record[$name];
    }

    /**
    * get the next id (primary key) for a table
    *
    * @param string $table_name
    * @return int the next id for the table or 0 if an error occured
    */
    public function nextid($table_name) {
        $this->connect();
       
        if ($this->lock($this->seq_table)) {
            // get sequence number (locked) and increment
            $q = sprintf("SELECT nextid FROM %s WHERE seq_name = '%s'", $this->seq_table, $table_name);
            $id = @mysql_query($q, $this->link_id);
            $res = @mysql_fetch_array($id);
        
            // No current value, make one 
            if (!is_array($res)) {
                $currentid = 0;
                $q = sprintf("INSERT INTO %s VALUES('%s', %s)", $this->seq_table, $table_name, $currentid);
                $id = @mysql_query($q, $this->link_id);
            } else {
                $currentid = $res["nextid"];
            }
            $nextid = $currentid + 1;
            $q = sprintf("UPDATE %s SET nextid = '%s' WHERE seq_name = '%s'", $this->seq_table, $nextid, $table_name);
            $id = @mysql_query($q, $this->link_id);
            $this->unlock();
        } else {
            $this->halt("cannot lock ".$this->seq_table." - has it been created?");
            return 0;
        }
        return $nextid;
    }
    /**
    * return table metadata.
    * If no $table specified, assume that we are working with a query result.
    * Depending on $full, metadata returns the following values:
    *
    * - full is false (default):
    * $result[]:
    *   [0]["table"]  table name
    *   [0]["name"]   field name
    *   [0]["type"]   field type
    *   [0]["len"]    field length
    *   [0]["flags"]  field flags
    *
    * - full is true
    * $result[]:
    *   ["num_fields"] number of metadata records
    *   [0]["table"]  table name
    *   [0]["name"]   field name
    *   [0]["type"]   field type
    *   [0]["len"]    field length
    *   [0]["flags"]  field flags
    *   ["meta"][field name]  index of field named "field name"
    *   This last one could be used if you have a field name, but no index.
    *   Test:  if (isset($result['meta']['myfield'])) { ...
    *
    * @param string $table the database table to analyze
    * @param bool $full set the output type (see description)
    */
    public function metadata($table = "", $full = false) {
        $count = 0;
        $id    = 0;
        $res   = array();
        
        // if no $table specified, assume that we are working with a query result
        if ($table) {
            $this->connect();
            $id = @mysql_list_fields(self::$config["database"], $table);
            if (!$id) {
                $this->halt("Metadata query failed.");
                return false;
            }
        } else {
            $id = $this->query_id;
            if (!$id) {
                $this->halt("No query specified.");
                return false;
            }
        }
        
        $count = @mysql_num_fields($id);

        if (!$full) {
            for ($i=0; $i<$count; $i++) {
                $res[$i]["table"] = @mysql_field_table ($id, $i);
                $res[$i]["name"]  = @mysql_field_name  ($id, $i);
                $res[$i]["type"]  = @mysql_field_type  ($id, $i);
                $res[$i]["len"]   = @mysql_field_len   ($id, $i);
                $res[$i]["flags"] = @mysql_field_flags ($id, $i);
            }
        } else { // full
            $res["num_fields"]= $count;
            for ($i=0; $i<$count; $i++) {
                $res[$i]["table"] = @mysql_field_table ($id, $i);
                $res[$i]["name"]  = @mysql_field_name  ($id, $i);
                $res[$i]["type"]  = @mysql_field_type  ($id, $i);
                $res[$i]["len"]   = @mysql_field_len   ($id, $i);
                $res[$i]["flags"] = @mysql_field_flags ($id, $i);
                $res["meta"][$res[$i]["name"]] = $i;
            }
        }
        
        // free the result only if we were called on a table
        if ($table) {
            @mysql_free_result($id);
        }
        return $res;
    }

    /**
    * find available table names
    *
    * @return array the availiable table names
    */
    public function tableNames() {
        $this->connect();
        $h = @mysql_query("show tables", $this->link_id);
        $i = 0;
        while ($info = @mysql_fetch_row($h)) {
            $return[$i]["table_name"]      = $info[0];
            $return[$i]["tablespace_name"] = self::$config["database"];
            $return[$i]["database"]        = self::$config["database"];
            $i++;
        }
        
        @mysql_free_result($h);
        return $return;
    }

    /**
    * error handling 
    *
    * @param string $msg the error message 
    */
    private function halt($msg) {
        // get the errors
        $this->error = @mysql_error($this->link_id);
        $this->errno = @mysql_errno($this->link_id);
        
        // should we ignore the error?
        if ($this->halt_on_error == "no")
            return;
        
        // print the error message
        $this->haltmsg($msg);
        
        // skip the whole programm
        if ($this->halt_on_error != "report")
            die("Session halted.");
    }
    
    /**
    * print the error message
    *
    * @param string $msg the error message 
    */
    private function haltmsg($msg) {
        printf("<strong>Database error:</strong> %s<br/>\n", $msg);
        printf("<strong>MySQL Error</strong>: %s (%s)<br/>\n", $this->errno, $this->error);
    }
}
?>