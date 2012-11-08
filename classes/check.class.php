<?php
/**
* sanatize and validate form input vars
* 
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once('classes/date.class.php');

class Check { 
    /**
    * occured error messages
    */
    private $errorMessages = array();
    
    /**
    * marks for input fields with errors
    */
    private $errorFields = array();
    
    /**
    * the checks to perform
	* format: 
	* - type of the check = name of the method
	* - message to display if check failes
	* - name of the var to check
	* - value(s) of the var to check
    */
    private $checks = array();
    
    /**
    * the submitted vars 
    */ 
    private $vars = array();
        
    /**
    * read vars / rules
	*
	* @param $vars 		array the post/get vars submitted by the form
	* @param $rules 	array the rules to check against
	* @param $messages 	array the messages to display if a check failes
    */
    public function __construct($vars, $rules, $messages) {
        $checks = array();
        $this->vars = $vars;

        foreach ($rules AS $field => $rule) {
			$mandantory = false;
            foreach ($rule AS $type => $check) {
                switch ($type) {
                    case "mandantory":
                        if ($check === true) {
                            $checks[] = array(
								"type"=>'mandantory', 
								"message"=>$messages[$field][0], 
								"field"=>$field, 
								"vars"=>$field
							);
							$mandantory = true;
						}
                        break;
                    case "format":
						$v = ($check == 'password' && $mandantory === true) ? array($field, true) : $field;
                        $checks[] = array(
							"type"=>$check,
							"message"=>$messages[$field][1], 
							"field"=>$field, 
							"vars"=>$v
						);
                        break;
                    case "check":
                        $checks[] = $this->resolveCheck($field, $check, $messages[$field][2]);
                        break;
                }
            }
        }
        
        $this->checks = $checks;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
    /**
    * Resolve the check command to get a method call
    */
    private function resolveCheck($field, $check, $message) {
        $parts = explode(':', $check);
        switch ($parts[0]) {
            case 'unique':
                $check = array(
					"type"=>'unique',
					"message"=>$message, 
					"field"=>$field, 
					"vars"=>array($field, $parts[1], $field)); // value, db table, db field
                break;
            case 'equal':
			case 'both':
                $check = array(
					"type"=>$parts[0], 
					"message"=>$message, 
					"field"=>$field, 
					"vars"=>array($field, $parts[1]));
                break;
			case 'min':
				$vars = array($field);
				for ($x=1; $x<count($parts); $x++)
					$vars[] = $parts[$x];
				$check = array(
					"type"=>'min',
					"message"=>$message,
					"field"=>$field,
					"vars"=>$vars);
				break;
			case 'compare':
				$check = array(
					"type"=>'compare', 
					"message"=>$message, 
					"field"=>$field, 
					"vars"=>array($field, $parts[1]));
                break;
        }
        return $check;
    }
    
    /**
    * validate / check data against given rules
    */
    public function run($escape = true) {
        foreach ($this->checks AS $check) {
			$failed = array();
            // check field only, if previous tests succeded
            if ($failed[$check["field"]] !== true) {
                if ($this->$check["type"]($check["vars"]) == false) {
                    // error occured, so populate message and field arrays
					$this->errorMessages[] = (empty($check["message"])) ? 'Unbekannter Fehler: Test '.$check["type"].' bei '.$check["field"] : $check["message"];
                    $this->errorFields[] = $check["field"];
                    // skip any following tests (i.g.: mandantory > format > check)
                    $failed[$check["field"]] = true; 
                }
            }
        }
        if (count($this->errorFields) > 0)
            return false;

		return true;
    }
    
    /**
    * is $var empty?
    */
    public function mandantory($var) {
		return !empty($this->vars[$var]);
	}

    public function int($var) {
		if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $v) {
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_INT) === false)
					return false;
			}
		} elseif (!empty($this->vars[$var]))
        	return filter_var($this->vars[$var], FILTER_VALIDATE_INT);
		return true;
    }
    
    public function float($var) {
		if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $k => $v) {
				$v = str_replace(',', '.', $v);
				$this->vars[$var][$k] = $v;
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_FLOAT) === false)
					return false;
			}
		} else {
			$this->vars[$var] = str_replace(',', '.', $this->vars[$var]);
	        if (!empty($this->vars[$var]))
				return filter_var($this->vars[$var], FILTER_VALIDATE_FLOAT);
		}
		return true;
    }
    
    public function bool($var) {
		if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $v) {
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_BOOLEAN) === false)
					return false;
			}
		} elseif (!empty($this->vars[$var]))
			return filter_var($this->vars[$var], FILTER_VALIDATE_BOOLEAN);
		return true;
    }
    
    public function string($var) {
		if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $k => $v)
				$this->vars[$var][$k] = filter_var($v, FILTER_SANITIZE_STRING);
		} else
			$this->vars[$var] = filter_var($this->vars[$var], FILTER_SANITIZE_STRING);
        return true;
    }
    
    public function ip($var) {
        if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $v) {
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_IP) === false)
					return false;
			}
		} elseif (!empty($this->vars[$var]))
			return filter_var($this->vars[$var], FILTER_VALIDATE_IP);
		return true;
    }
    
    public function email($var) {
        if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $k => $v) {
				$this->vars[$var][$k] = filter_var($v, FILTER_SANITIZE_EMAIL);
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_EMAIL) === false)
					return false;
			}
		} else {
			$this->vars[$var] = filter_var($this->vars[$var], FILTER_SANITIZE_EMAIL);
	        if (!empty($this->vars[$var]))
				return filter_var($this->vars[$var], FILTER_VALIDATE_EMAIL);
		}
		return true;
    }
    
    public function url(&$var) {
        if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $k => $v) {
				$this->vars[$var][$k] = filter_var($v, FILTER_SANITIZE_URL);
				if (!empty($v) && filter_var($v, FILTER_VALIDATE_URL) === false)
					return false;
			}
		} else {
			$this->vars[$var] = filter_var($this->vars[$var], FILTER_SANITIZE_URL);
	        if (!empty($this->vars[$var]))
				return filter_var($this->vars[$var], FILTER_VALIDATE_URL);
		}
		return true;
    }
        
    /**
    * only alphabetical values (without spaces)
    */
    public function alpha($var) {
        if (is_array($this->vars[$var])) {
			foreach($this->vars[$var] AS $v) {
				if (preg_match("/^[a-zA-Z]+$/", $this->vars[$var]) === false)
					return false;
			}
		} else
			return preg_match("/^[a-zA-Z]+$/", $this->vars[$var]);
    }
    
    /**
    * like a string, but without spaces, tabs and lf/cr, minimum six characters
    */
    public function password($vars) {
		if (is_array($vars))
        	return preg_match('/^\S{6,}$/', $this->vars[$vars[0]]);
		elseif (!empty($this->vars[$vars]))
			return preg_match('/^\S{6,}$/', $this->vars[$vars]);
		return true;
    }
    
    /**
    * only alphabetical values, spaces, . and -
    */
    public function name($var) {
        return preg_match("/^[a-zA-ZäÄüÜöÖß\-. ]+$/", $this->vars[$var]);
    }
    
	/**
    * only alphanumeric (with underscore)
    */
    public function urlstring($var) {
        return preg_match("/^[a-zA-Z_0-9]+$/", $this->vars[$var]);
    }

	/**
    * only letters, underscores and one colon
    */
    public function rightstring($var) {
        return preg_match("/^[a-zA-Z_]+:[a-zA-Z_]+$/", $this->vars[$var]);
    }

    /**
    * valid date string?
    */
    public function date($var) {
		if (!empty($this->vars[$var])) {
			$date = new Date($this->vars[$var], 'd.m.Y');
			return $date->validateDate();
		}
		return true;
    }

	/**
	* one var set at least (checkboxes)?
	*/
	public function one($var) {
        return is_array($this->vars[$var]);
	}
	
	/**
	* one var set at least (normal input)?
	*/
	public function min($vars) {
		foreach ($vars AS $var) {
			if (!empty($this->vars[$var]))
				return true;
		}
        return false;
	}
    
    /**
	* both fields have to be equal
	*/
    public function equal($vars) {
        return ($this->vars[$vars[0]] === $this->vars[$vars[1]]);
	}
	
	/**
	* either both fields filled or none of them
	*/
	public function both($vars) {
		if (is_array($this->vars[$vars[0]]) && is_array($this->vars[$vars[1]]) && count($this->vars[$vars[0]]) == count($this->vars[$vars[1]])) {
			foreach ($this->vars[$vars[0]] AS $k => $v) {
				if (!(empty($v) XOR empty($this->vars[$vars[1]][$k])) === false)
					return false;
			}
			return true;
		} else
        	return !(empty($this->vars[$vars[0]]) XOR empty($this->vars[$vars[1]]));
	}

	/**
	* does this var just exists once in the database?
	*/
	public function unique($vars) {
        $db = DB::getInstance();
        $sql = "SELECT *
                  FROM $vars[1]
                 WHERE $vars[2] = '".$this->vars[$vars[0]]."'";
        $result = $db->query($sql);
        
        if ($result->length() != 0)
            return false;
        else
            return true; 
	}
	
	/**
	* compare dates: is $vars[1] before $vars[0]?
	*/
	public function compare($vars) {
		if (!empty($vars[0])) {
			$start = new Date($vars[1], 'd.m.Y');
			$end = new Date($vars[0], 'd.m.Y');
			if ($end->compareDates($start) == 1)
				return false;
		}
		return true;
    }
}