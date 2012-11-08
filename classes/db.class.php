<?php
/**
* db access wrapper for mysql databases.
* A basic DB connection class returning resultset objects following an iterator pattern.
*
* @author: Elias Müller, d11wtq
* @version: 0.1
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/
require_once("classes/db_result.class.php");
require_once("classes/error.class.php");

class DB {
    /**
    * connection parameters 
    */
    private static $config = array(
        "host"=>"",
        "database"=>"",
        "user"=>"",
        "password"=>""
    );
    
    /**
    * An instance of a singleton
    * @var object DB
    */
    private static $instance = null;
    
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
    * @var string "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore error, but spit a warning)
    */
    private $halt_on_error = "report";
    
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
    * Used for retreiving an instance of a singleton if wanted
    * @return object DB
    */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    /** 
    * Connect to database
    *
    * @param string $Database db name if no config param loaded
    * @param string $Host db host if no config param loaded
    * @param string $User db usernmae if no config param loaded
    * @param string $Password db password if no config param loaded
    * @return bool|int 0 if connection error, else the link id
    */
    public function connect($host = "", $database = "", $user = "", $password = "") {
        // Handle defaults
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
        $this->errno = mysql_errno();
        $this->error = mysql_error();
        
        if (!$this->query_id)
            $this->halt("Invalid SQL: ".$query_string."<br><br>");
 
        $tokens = explode(' ', strtolower($query_string));

        if (count($tokens) > 0) {
            if (!in_array(trim($tokens[0]), array('update', 'delete', 'insert'))) {
                // return result object if succeded and no INSERT / DELETE / UPDATE query
                return new DbResult($this->query_id, $this->link_id);
            } elseif (trim($tokens[0]) == 'insert') {
                // return inserted id if INSERT query executed
                return mysql_insert_id($this->link_id);
            }
        }

        // return false if query failed, true if successful UPDATE / DELETE
        return $this->query_id;
    }
    
    /**
    * Escape a string to make it safe for mysql
    * @return string escaped output
    */
    public function escape($string) {
		if (is_array($string)) {
			foreach($string AS $k => $s) {
				$string[$k] = $this->escape($s);
			}
			return $string;
		} elseif(is_string($string)) {
			return mysql_real_escape_string($string, $this->link_id);
		} else {
			return $string;
		}
    }
    
    /**
    * discard the query result.
    * Free all memory associated with the result identifier. 
    * Only needs to be called if you are concerned about how much memory is being used 
    * for queries that return large result sets.
    */
    public function free() {
        @mysql_free_result($this->query_id);
        $this->query_id = 0;
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
    * Retreive info about the server
    * @return string info  
    */
    public function info() {
		if (!is_resource($this->link_id))
			$this->connect();
        return mysql_get_server_info($this->link_id);
    }
    
    /**
    * Get details about the current system status
    * @return array details
    */
    public function status() {
        return explode('  ', mysql_stat($this->link_id));
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
    * @param string $table the database table to analyse
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
        $h = @mysql_query("SHOW TABLES", $this->link_id);
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
        Error::addError("<strong>Database error:</strong> $msg<br/>\n<strong>MySQL Error</strong>: $this->errno ($this->error)<br/>\n", true);
        
        // skip the whole programm
        if ($this->halt_on_error != "report")
            die("Session halted.");
    }
}
?>