<?php
/**
* DB_Result class. Provides an iterator wrapper for working with a MySQL result.
*
* @author: Elias Müller, d11wtq
* @version: 0.1
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/

class DbResult {        
    /**
    * The size of the resultset
    * @var int length (num rows)
    */
    private $length = 0;
    
    /**
    * The result itself
    * @var result result
    */
    private $result;
    
    /**
    * The connection handler
    * @var resource
    */
    private $conn;
    
    /**
    * The row at our current position in the resultset
    * @var array row
    */
    private $currentRow = array();
    
    /**
    * Current position in the resultset
    * @var int position
    */
    private $position = 0;
    
    /**
    * The last position we were at when we read from the resultset
    * @var int last position
    */
    private $lastPosition = 0;
    
    /**
    * If we have pulled out any rows or not yet
    * @var boolean Got rows
    */
    private $gotResult = false;
    
    /**
    * The affected number of rows from the query
    * @var int num rows
    */
    private $affectedRows = -1;
        
    /**
    * Constructor
    *
    * @param result result
    * @param resource connection
    */
    public function __construct(&$result, &$conn) {
        $this->result = $result;
        $this->conn = $conn;
        
        // query corrrect and result not empty? -> get basic infos
        if ((@mysql_num_rows($this->result) >= 0 && $this->result !== false)) {
            $this->length = (int) @mysql_num_rows($this->result);
            $this->affectedRows = mysql_affected_rows($conn);
        }
    }
        
    /**
    * Magic overloaded method.
    * Returns data from the resultset
    *
    * @param string column
    */
    public function __get($field) {
        // new position or no result -> get new row
        if (($this->lastPosition != $this->position || !$this->gotResult) && $this->length > 0) {
            mysql_data_seek($this->result, $this->position);
            $this->currentRow = mysql_fetch_assoc($this->result);
            $this->lastPosition = $this->position;
            $this->gotResult = true;
        }
        return $this->currentRow[$field];
    }
        
    /**
    * Go to the first row of the resultset
    * @return boolean
    */
    public function first() {
        if ($this->length > 0) {
            $this->goto(0);
            return true;
        } else 
            return false;
    }
        
    /**
    * Go to the last row of the resultset
    * @return boolean
    */
    public function last() {
        return $this->goto($this->length-1);
    }
        
    /**
    * Check if we’ve reched the end of the resultset
    * @return boolean
    */
    public function end() {
        if ($this->position >= $this->length)
            return true;
        else 
            return false;
    }
        
    /**
    * Check if we’re at the start of the resultset
    * @return boolean
    */
    public function start() {
        return ($this->position < 0);
    }
    
    /**
    * Move to the next row of the resultset
    * @return boolean
    */
    public function next() {
        return $this->gotoRow($this->position+1);
    }
    
    /**
    * Move to the previous row in the resultset
    * @return boolean
    */
    public function prev() {
            return $this->gotoRow($this->position-1);
    }
    
    /**
    * Go to a specified row in the resultset
    * Row numbering starts at zero
    * @param int row
    * @return boolean
    */
    public function gotoRow($position)
    {
        if ($position < -1 || $position >= $this->length) 
            return false;
        else {
            $this->position = $position;
            return true;
        }
    }
    
    /**
    * Size of the resultset
    */
    public function length() {
        return $this->length;
    }
    
    /**
    * Get the affected number of rows
    */
    public function affectedRows()
    {
        return $this->affectedRows;
    }

    /**
    * Get the current position
    */
    public function position()
    {
        return $this->position;
    }
}
?>