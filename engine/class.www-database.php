<?php

/*
WWW Framework
Database class

Simple database class that has presently only support for MySQL. It is not required to use 
WWW_Database with WWW framework at all, but should it be required, it is available. This 
class uses PDO database class to simplify some of the database commands. It includes
functions for returning data from database as a single row or multiple rows as associative 
arrays as well as get information about last inserted ID and number of rows affected by 
last query.

* This currently supports MySQL, but other PDO drivers can be easily supported
* MySQL, SQLite, PostgreSQL, Oracle, MS SQL supported

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Database {

	// PDO class is used for database commands
	public $pdo=false;
	
	// Flag for checking whether database connection is active or not
	private $connected=0;
	
	// All database authentication and connection related variables
	public $type='mysql';
	public $username='';
	public $password='';
	public $host='localhost';
	public $database='';
	public $persistent=false;
	
	// Flag for whether errors are thrown or not
	public $showErrors=false;
	
	// We count the amount of queries in this variable
	public $queryCounter=0;

	// These parameters are set during object construction
	public function __construct($type='mysql',$host='localhost',$database='',$username='',$password='',$showErrors=false,$persistent=false){
		// Assigning construct elements to object parameters
		$this->type=$type;
		$this->host=$host;
		$this->database=$database;
		$this->username=$username;
		$this->password=$password;
		$this->showErrors=$showErrors;
		$this->persistent=$persistent;
	}
	
	// Database connection is closed if this object is not used anymore
	public function __destruct(){
		$this->dbDisconnect();
	}

	// Connects to database based on set configuration
	// * persistent - Whether database connection is persistent or not
	// Throws errors if database connection fails
	public function dbConnect($persistent=false){
	
		// Persistent or not
		if($persistent==true){
			$this->persistent=true;
		}
	
		// Actions based on database type
		switch($this->type){
			case 'mysql':
				// This mode can only be used if PDO MySQL is loaded as PHP extension
				if(extension_loaded('pdo_mysql')){
					$this->pdo=new PDO('mysql:host='.$this->host.';dbname='.$this->database,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent,PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\''));
					if($this->pdo){
						$this->connected=1;
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
					$this->pdo=new PDO('sqlite:'.$this->database,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->connected=1;
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
					$this->pdo=new PDO('pgsql:host='.$this->host.';dbname='.$this->database,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=1;
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
					$this->pdo=new PDO('oci:dbname='.$this->database.';charset=AL32UTF8',$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->connected=1;
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
					$this->pdo=new PDO('dblib:host='.$this->host.';dbname='.$this->database,$this->username,$this->password,array(PDO::ATTR_PERSISTENT=>$this->persistent));
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=1;
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
	}
	
	// Disconnects from database, if connected
	// Returns false if no connection was present
	public function dbDisconnect($resetQueryCounter=false){
	
		// This is only executed if existing connection is detected
		if($this->connected==1 && !$this->persistent){
			// Resetting the query counter
			if($resetQueryCounter){
				$this->queryCounter=0;
			}
			// Closing the database
			$this->pdo=null;
			$this->connected=0;
		} else {
			return false;
		}
		
	}

	// Sends query to database and returns associative array of all results
	// Meant for SELECT queries
	// * queryString - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the result in an array or returns false when query failed
	public function dbMultiple($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if($this->connected!=1){
			$this->dbConnect($this->persistent);
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result){
			// All data is returned as associative array
			$return=$query->fetchAll(PDO::FETCH_ASSOC);
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// Associative array is returned
			return $return;
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString);
			return false;
		}
		
	}

	// Sends query to database and returns associative array of first returned row
	// Meant for SELECT queries
	// * queryString - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the result in an array or returns false when query failed
	public function dbSingle($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if($this->connected!=1){
			$this->dbConnect($this->persistent);
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result){
			// All data is returned as associative array
			$return=$query->fetch(PDO::FETCH_ASSOC);
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// Associative array is returned
			return $return;
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString);
			return false;
		}
		
	}

	// Executes a specific database query 
	// Meant for INSERT, UPDATE, DELETE queries
	// * queryString - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the amount of rows that were affected by the query
	public function dbCommand($queryString,$variables=array()){
	
		// Attempting to connect to database, if not connected
		if($this->connected!=1){
			$this->dbConnect($this->persistent);
		}
		
		// Query total is being counted for performance review
		$this->queryCounter++;

		// Preparing a query and executing it
		$query=$this->pdo->prepare($queryString);
		$result=$query->execute($variables);
		
		// If there is a result then it is fetched and returned
		if($result){
			// Result of this query is the amount of rows affected by it
			$rowCount=$query->rowCount();
			// Closing the resource
			$query->closeCursor();
			unset($query);
			// If, for some reason, the amount of affected rows was not returned, system simply returns true
			if($rowCount){
				return $rowCount;
			} else {
				return true;
			}
		} else {
			// Checking for an error, if there was one
			$this->dbErrorCheck($query,$queryString);
			return false;
		}
		
	}
	
	// Triggers a PHP error if MySQL encounters error in the query
	// * query - query object from PDO
	// * queryString - query string
	// Triggers an error if there was an error
	private function dbErrorCheck($query,$queryString=false){
	
		// This method requires database to be connected
		if($this->connected==1){
			if($this->showErrors==1){
				// Checking if there is error information stored for this request
				$errors=$query->errorInfo();
				if($errors && !empty($errors)){
					// PDO errorInfo carries verbose error as third in the index
					if($queryString){
						trigger_error('QUERY:'."\n".$queryString."\n".'FAILED:'."\n".$errors[2],E_USER_WARNING);
					} else {
						trigger_error('QUERY FAILED:'."\n".$errors[2],E_USER_WARNING);
					}
				}
			}
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
		// Since error was not triggered, it simply returns true
		return true;
		
	}
	
	// Used to get the last inserted ID from database
	// Returns last ID if found, returns false if last ID was not found
	public function dbLastId(){
	
		// This method requires database to be connected
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;

			// Checks for last existing inserted row's unique ID
			$lastId=$this->pdo->lastInsertId();
			// Last ID is found, it is returned, otherwise it returns false
			if(!$lastId && $lastId!=0){
				return $lastId;
			} else {
				return false;
			}

		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}
	
	// Begins transaction if transactions are supported
	// Returns true if transaction is started
	public function dbTransaction(){
	
		// Attempting to connect to database, if not connected
		if($this->connected!=1){
			$this->dbConnect($this->persistent);
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
	
	// Commits transaction if transactions are supported
	// Returns true if transaction is commited
	public function dbCommit(){
	
		// This method requires database to be connected
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;
			
			// Commits transaction
			if($this->pdo->commit()){
				return true;
			} else {
				return false;
			}
			
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
	}
	
	// Rolls back the changes from transaction
	// Returns true if transaction is rolled back
	public function dbRollback(){
	
		// This method requires database to be connected
		if($this->connected==1){
		
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
	
	// This function escapes a string for use in query
	// * value - Input value
	// * type - Method of quoting, either 'escape', 'integer', 'latin', 'field' or 'like'
	// * stripQuotes - Whether the resulting quotes will be stripped from the string, if they get set
	public function dbQuote($value,$type='escape',$stripQuotes=false){
	
		// Filtering is done based on filter type
		switch($type){
			case 'escape':
				if($stripQuotes){
					return substr($this->pdo->quote($value,PDO::PARAM_STR),1,-1);
				} else {
					return $this->pdo->quote($value,PDO::PARAM_STR);
				}
				break;
			case 'integer':
				return (int)$value;
				break;
			case 'float':
				return (float)$value;
				break;
			case 'latin':
				return preg_replace('/[^a-z]/i','',$value);
				break;
			case 'field':
				return preg_replace('/[^a-z\-\_]/i','',$value);
				break;
			case 'like':
				if($stripQuotes){
					return str_replace(array('%','_'),array('\%','\_'),$this->pdo->quote($value,PDO::PARAM_STR));
				} else {
					return str_replace(array('%','_'),array('\%','\_'),$this->pdo->quote($value,PDO::PARAM_STR));
				}
				break;
			default:
				return $value;
				break;
		}
		
	}

}

?>