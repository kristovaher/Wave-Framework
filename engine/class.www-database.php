<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Database Class
 *
 * Simple database class that acts as a wrapper to PDO. It is not required to use WWW_Database 
 * with Wave Framework at all, but should it be required, it is available. This class uses PDO 
 * database class to simplify some of the database commands. It includes functions for returning 
 * data from database as a single row or multiple rows as associative arrays as well as get 
 * information about last inserted ID and number of rows affected by last query.
 *
 * @package    Database
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/database.htm
 * @since      1.1.2
 * @version    3.7.0
 */

class WWW_Database {

	/**
	 * PDO object is stored in this variable, since Database class acts as a wrapper for PDO.
	 */
	public $pdo=false;
	
	/**
	 * Flag for checking whether database connection is active or not
	 */
	private $connected=false;
	
	/**
	 * This is currently used database type. It can be 'mysql', 'sqlite', 'postgresql', 
	 * 'oracle' or 'mssql'.
	 */
	public $type='mysql';
	
	/**
	 * This is the username that is used to connect to database.
	 */
	public $username='';
	
	/**
	 * This is the password that is used to connect to database.
	 */
	public $password='';
	
	/**
	 * This is the host address of the database. In case of SQLite, this should be the location 
	 * of SQLite database file.
	 */
	public $host='localhost';
	
	/**
	 * This is the database name that will be connected to.
	 */
	public $database='';
	
	/**
	 * This is the flag that determines if database connection should be considered persistent 
	 * or not. Persistent connections are shared across requests, but are generally not recommended 
	 * to be used.
	 */
	public $persistent=false;
	
	/**
	 * If this value is set to true, then Database class will trigger a PHP error if it encounters 
	 * a database error.
	 */
	public $showErrors=false;
	
	/**
	 * This is used for logging or otherwise performance tracking. This variable is a simple counter 
	 * about the amount of requests made during this database connection.
	 */
	public $queryCounter=0;

	/**
	 * This constructor method returns new Database object as well as includes options to quickly 
	 * assign database credentials in the same request.
	 *
	 * @param string $type database type, can be 'mysql', 'sqlite', 'postgresql', 'oracle' or 'mssql'
	 * @param string $host database server address or SQLite database file location
	 * @param string $database database name
	 * @param string $username username
	 * @param string $password password
	 * @param boolean $showErrors whether database errors trigger PHP errors or not
	 * @param boolean $persistent whether database connection is persistent or not (usually not recommended)
	 * @return WWW_Database
	 */
	public function __construct($type='mysql',$host='localhost',$database='',$username='',$password='',$showErrors=true,$persistent=false){
	
		// Assigning construct elements to object parameters
		$this->type=$type;
		$this->host=$host;
		$this->database=$database;
		$this->username=$username;
		$this->password=$password;
		$this->showErrors=$showErrors;
		$this->persistent=$persistent;
		
	}
	
	/**
	 * When object is not used anymore, it automatically calls the method that closes existing 
	 * database connection.
	 *
	 * @return null
	 */
	public function __destruct(){
		$this->dbDisconnect();
	}

	/**
	 * This creates database connection based on database type. If database connection fails, 
	 * then it throws a PHP error regardless if $showErrors is turned on or not.
	 *
	 * @return boolean
	 */
	public function dbConnect(){
	
		// This is a connection command for PDO
		$connectLine='';
		
		// Connection command is built based on database settings
		if($this->host){
			// Looking for port information
			if(strpos($this->host,':')!==false){
				$bits=explode(':',$this->host);
				$port=array_pop($bits);
				// If port is assumed to be included
				if(is_numeric($port)){
					$connectLine.='host='.implode(':',$bits).';port='.$port.';';
				} else {
					$connectLine.='host='.$this->host.';';
				}
			} else {
				$connectLine.='host='.$this->host.';';
			}
		}
		
		// If database name is set
		if($this->database){
			$connectLine.='dbname='.$this->database.';';
		}
	
		// Actions based on database type
		switch($this->type){
			case 'mysql':
				// This mode can only be used if PDO MySQL is loaded as PHP extension
				if(extension_loaded('pdo_mysql')){
					$this->pdo=new PDO('mysql:'.$connectLine,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent,PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\''));
					if($this->pdo){
						$this->connected=true;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MySQL extension not enabled',E_USER_ERROR);
				}
				break;
			case 'sqlite':
				// This mode can only be used if PDO SQLite is loaded as PHP extension
				if(extension_loaded('pdo_sqlite')){
					$this->pdo=new PDO('sqlite:'.$connectLine,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->connected=true;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO SQLite extension not enabled',E_USER_ERROR);
				}
				break;
			case 'postgresql':
				// This mode can only be used if PDO PostgreSQL is loaded as PHP extension
				if(extension_loaded('pdo_pgsql')){
					$this->pdo=new PDO('pgsql:'.$connectLine,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=true;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO PostgreSQL extension not enabled',E_USER_ERROR);
				}
				break;
			case 'oracle':
				// This mode can only be used if PDO Oracle is loaded as PHP extension
				if(extension_loaded('pdo_oci')){
					$this->pdo=new PDO('oci:'.$connectLine.';charset=AL32UTF8',$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->connected=true;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO Oracle extension not enabled',E_USER_ERROR);
				}
				break;
			case 'mssql':
				// This mode can only be used if PDO MSSQL is loaded as PHP extension
				if(extension_loaded('pdo_mssql')){
					$this->pdo=new PDO('dblib:'.$connectLine,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=true;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MSSQL extension not enabled',E_USER_ERROR);
				}
				break;
			default:
				// Error is triggered for all other database types
				trigger_error('This database type is not supported',E_USER_ERROR);
				break;
		}
		
		// Connected
		return true;
		
	}
	
	/**
	 * This disconnects current database connection and resets query counter, if 
	 * $resetQueryCounter is set to true. Returns false if no connection was present.
	 *
	 * @param boolean $resetQueryCounter whether to reset the query counter
	 * @return boolean
	 */
	public function dbDisconnect($resetQueryCounter=false){
	
		// This is only executed if existing connection is detected
		if($this->connected && !$this->persistent){
			// Resetting the query counter
			if($resetQueryCounter){
				$this->queryCounter=0;
			}
			// Closing the database
			$this->pdo=null;
			$this->connected=false;
			return true;
		} else {
			return false;
		}
		
	}

	/**
	 * This sends query information to PDO. $queryString is the query string and $variables 
	 * is an array of variables sent with the request. Question marks (?) in $queryString 
	 * will be replaced by values from $variables array for PDO prepared statements. This 
	 * method returns an array where each key is one returned row from the database, or it 
	 * returns false, if the query failed. This method is mostly meant for SELECT queries 
	 * that return multiple rows.
	 *
	 * @param string $queryString query string, a statement to prepare with PDO
	 * @param array $variables array of variables to use in prepared statement
	 * @return array or false if failed
	 */
	public function dbMultiple($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result!==false){
			// All data is returned as associative array
			$return=$query->fetchAll(PDO::FETCH_ASSOC);
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// Associative array is returned
			return $return;
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString,$variables);
			return false;
		}
		
	}

	/**
	 * This sends query information to PDO. $queryString is the query string and $variables 
	 * is an array of variables sent with the request. Question marks (?) in $queryString 
	 * will be replaced by values from $variables array for PDO prepared statements. This 
	 * method returns the first row of the matching result, or it returns false, if the query 
	 * failed. This method is mostly meant for SELECT queries that return a single row.
	 *
	 * @param string $queryString query string, a statement to prepare with PDO
	 * @param array $variables array of variables to use in prepared statement
	 * @return array or false if failed
	 */
	public function dbSingle($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result!==false){
			// All data is returned as associative array
			$return=$query->fetch(PDO::FETCH_ASSOC);
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// Associative array is returned
			return $return;
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString,$variables);
			return false;
		}
		
	}

	/**
	 * This sends query information to PDO. $queryString is the query string and $variables 
	 * is an array of variables sent with the request. Question marks (?) in $queryString 
	 * will be replaced by values from $variables array for PDO prepared statements. This 
	 * method only returns the number of rows affected or true or false, depending whether 
	 * the query was successful or not. This method is mostly meant for INSERT, UPDATE and 
	 * DELETE type of queries.
	 *
	 * @param string $queryString query string, a statement to prepare with PDO
	 * @param array $variables array of variables to use in prepared statement
	 * @return boolean or integer of affected rows
	 */
	public function dbCommand($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result!==false){
			// Result of this query is the amount of rows affected by it
			$rowCount=$query->rowCount();
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// If, for some reason, the amount of affected rows was not returned, system simply returns true, since the query was a success
			if($rowCount===false){
				return true;
			} else {
				return $rowCount;
			}
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString,$variables);
			return false;
		}
		
	}
	
	/**
	 * This is a database helper method, that simply creates an array of database result array 
	 * (like the one returned by dbMultiple). It takes the database result array and collects 
	 * all $key values from that array into a new, separate array. If $unique is set, then it 
	 * only returns unique keys.
	 *
	 * @param array $array array to filter from
	 * @param string $key key to return
	 * @param boolean $unique if returned array should only have only unique values
	 * @return array or mixed if source is not an array
	 */
	public function dbArray($array,$key,$unique=false){
	
		// Result will be an array
		$result=array();
		
		// This method only works on an array
		if(is_array($array)){
			foreach($array as $ar){
				if(is_array($ar)){
					// If the key is set, then adds to the array
					if(isset($ar[$key])){
						$result[]=$ar[$key];
					}
				} else {
					// This is used in case the array sent to dbArray is from dbSingle
					if(isset($array[$key])){
						$result[]=$array[$key];
					}
					break;
				}
			}
			// If only unique values are accepted
			if($unique){
				$result=array_unique($result);
			}
		}
		
		// Returning all the column values as an array
		return $result;
		
	}
	
	/**
	 * This method attempts to simulate the query that PDO builds when it makes a request to 
	 * database. $query should be the query string and $variables should be the array of 
	 * variables, like the ones sent to dbSingle(), dbMultiple() and dbCommand() requests. 
	 * It returns a prepared query string.
	 *
	 * @param string $query query string
	 * @param array $variables values sent to PDO
	 * @return string
	 */
	public function dbDebug($query,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		
		// Attempt to simulate PDO query-building
		$keys=array();
		$values=array();
		
		// Type is either 1 for positioned variables or 2 for name based tokens, 0 means that it is undefined
		$type=0;
		
		// Making sure that variables are not empty
		if(!empty($variables)){
		
			// Looping over each of the input variables
			foreach($variables as $key=>$value){
				// Finding out the prepared statement type
				if($type==0){
					if(is_string($key)){
						$type=2;
					} else {
						$type=1;
					}
				}
				// Keys are detected based on type
				if($type==2){
					$keys[]='/:'.$key.'/';
				} else {
					$keys[]='/[?]/';
				}
				// Casting the PDO quote function on the variable
				$values[]=$this->pdo->quote($value);
			}
			if($type==2){
				return preg_replace($keys,$values,$query);
			} else {
				return preg_replace($keys,$values,$query,1);
			}
		} else {
			return $query;
		}
		
	}
	
	/**
	 * This method attempts to return the last ID (a primary key) that was inserted to the 
	 * database. If one was not found, then the method returns false.
	 * 
	 * @return integer or false if not found
	 */
	public function dbLastId(){
	
		// This method requires database to be connected
		if($this->connected){
			// Query total is being counted for performance review
			$this->queryCounter++;
			// Checks for last existing inserted row's unique ID
			$lastId=$this->pdo->lastInsertId();
			// Last ID is found, it is returned, otherwise it returns false
			if($lastId){
				return $lastId;
			} else {
				return false;
			}
		} else {
			// Cannot find last ID if not connected
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}
	
	/**
	 * This method begins a database transaction, if the database type supports transactions. 
	 * If this process fails, then method returns false, otherwise it returns true.
	 *
	 * @return boolean
	 */
	public function dbTransaction(){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		// Query total is being counted for performance review
		$this->queryCounter++;
		// Begins transaction
		if($this->pdo->beginTransaction()){
			return true;
		} else {
			return false;
		}
			
	}
	
	/**
	 * This commits all the queries sent to database after transactions were started. If 
	 * commit fails, then it returns false, otherwise this method returns true.
	 *
	 * @return boolean
	 */
	public function dbCommit(){
	
		// This method requires database to be connected
		if($this->connected){
			// Query total is being counted for performance review
			$this->queryCounter++;
			// Commits transaction
			if($this->pdo->commit()){
				return true;
			} else {
				return false;
			}
		} else {
			// Cannot find last ID if not connected
			trigger_error('Database not connected',E_USER_ERROR);
		}
	}
	
	/**
	 * This method cancels all the queries that have been sent since the transaction was 
	 * started with dbTransaction(). If rollback is successful, then method returns true, 
	 * otherwise returns false.
	 *
	 * @return boolean
	 */
	public function dbRollback(){
	
		// This method requires database to be connected
		if($this->connected){
			// Query total is being counted for performance review
			$this->queryCounter++;
			// Rolls back transaction
			if($this->pdo->rollBack()){
				return true;
			} else {
				return false;
			}
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}
	
	/**
	 * This is a database helper function that can be used to escape specific variables if 
	 * that variable is not sent with as part of variables array and is written in the 
	 * query string instead. $value is the variable value that will be escaped or modified. 
	 * $type is the type of escaping and modifying that takes place. This can be 'escape' 
	 * (which just applies regular PDO quote to the variable) or 'like', which does the same
	 * as escape, but also escapes wildcard characters '_' and '%'.
	 *
	 * @param string $value input value
	 * @param string $type Mmthod of quoting, either 'escape' or 'like'
	 * @param boolean $stripQuotes whether the resulting quotes will be stripped from the string, if they get set
	 * @return string
	 */
	public function dbQuote($value,$type='escape',$stripQuotes=false){
	
		// Attempting to connect to database, if not connected
		if(!$this->connected){
			$this->dbConnect();
		}
		
		// Filtering is done based on filter type
		switch($type){
			case 'escape':
				if($stripQuotes){
					return substr($this->pdo->quote($value,PDO::PARAM_STR),1,-1);
				} else {
					return $this->pdo->quote($value,PDO::PARAM_STR);
				}
				break;
			case 'like':
				if($stripQuotes){
					return substr(str_replace(array('%','_'),array('\%','\_'),$this->pdo->quote($value,PDO::PARAM_STR)),1,-1);
				} else {
					return str_replace(array('%','_'),array('\%','\_'),$this->pdo->quote($value,PDO::PARAM_STR));
				}
				break;
			default:
				return $value;
				break;
		}
		
	}
	
	/**
	 * This is a private method that is called after every query that is sent to database. This 
	 * checks whether database error was encountered and if $showErrors was turned on, then also 
	 * throws a PHP warning about it. It also accepts $query, which is the PDO resource for the 
	 * query to be checked and $queryString with $variables for debugging purposes, as it attempts 
	 * to rebuild the query sent to PDO using dbDebug() method.
	 *
	 * @param object $query query object from PDO
	 * @param boolean|string $queryString query string
	 * @param array $variables variables sent to query
	 * @return boolean or throws error
	 */
	private function dbErrorCheck($query,$queryString=false,$variables=array()){
	
		// This method requires database to be connected
		if($this->connected){
			if($this->showErrors){
				// Checking if there is error information stored for this request
				$errors=$query->errorInfo();
				if($errors && !empty($errors)){
					// PDO errorInfo carries verbose error as third in the index
					if(!empty($variables)){
						trigger_error('QUERY:'."\n".$this->dbDebug($queryString,$variables)."\n".'FAILED:'."\n".$errors[2],E_USER_WARNING);
					} else {
						trigger_error('QUERY:'."\n".$queryString."\n".'FAILED:'."\n".$errors[2],E_USER_WARNING);
					}
				}
			}
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
		// Since error was not triggered, it simply returns true
		return true;
		
	}

}

?>