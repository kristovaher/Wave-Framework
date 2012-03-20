<?php

/*
WWW - PHP micro-framework
Database class

Simple database class that has presently only support for MySQL. It is not required to use 
WWW_Database with WWW framework at all, but should it be required, it is available. This 
class uses PDO database class to simplify some of the database commands. It includes
functions for returning data from database as a single row or multiple rows as associative 
arrays as well as get information about last inserted ID and number of rows affected by 
last query.

* This currently supports MySQL, but other PDO drivers can be easily supported

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Database {

	// PDO class is used for database commands
	public $pdo=false;
	
	// Flag for checking whether database connection is active or not
	private $connected=0;
	
	// All database access related variables
	public $type='mysql';
	public $username='';
	public $password='';
	public $host='';
	public $database='';
	
	// Flag for checking whether database connection is active or not
	public $showErrors=0;
	
	// We count the amount of queries in this variable
	public $queryCounter=0;
	
	// Error reporting flag is set during class creation
	public function __construct(){
		if(error_reporting()!=0){
			$this->showErrors=true;
		} else {
			$this->showErrors=false;
		}
	}

	// Database connection is closed if this object is not used anymore
	public function __destruct(){
		$this->disconnect();
	}

	// Connects to database based on set configuration
	// Throws errors if database connection fails
	public function connect(){
		// Actions based on database type
		switch($this->type){
		
			case 'mysql':
				// This mode can only be used if PDO MySQL is loaded as PHP extension
				if(extension_loaded('pdo_mysql')){
					$this->pdo=new PDO('mysql:host='.$this->host.';dbname='.$this->database,$this->username,$this->password,array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\''));
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
					$this->pdo=new PDO('sqlite:'.$this->database,$this->username,$this->password);
					if($this->pdo){
						$this->connected=1;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MySQL extension not enabled',E_USER_ERROR);
				}
				break;
				
			case 'postgresql':
				// This mode can only be used if PDO PostgreSQL is loaded as PHP extension
				if(extension_loaded('pdo_pgsql')){
					$this->pdo=new PDO('pgsql:host='.$this->host.';dbname='.$this->database,$this->username,$this->password);
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=1;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MySQL extension not enabled',E_USER_ERROR);
				}
				break;
				
			case 'oracle':
				// This mode can only be used if PDO Oracle is loaded as PHP extension
				if(extension_loaded('pdo_oci')){
					$this->pdo=new PDO('oci:dbname='.$this->database.';charset=AL32UTF8',$this->username,$this->password);
					if($this->pdo){
						$this->connected=1;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MySQL extension not enabled',E_USER_ERROR);
				}
				break;
				
			case 'mssql':
				// This mode can only be used if PDO MSSQL is loaded as PHP extension
				if(extension_loaded('pdo_mssql')){
					$this->pdo=new PDO('dblib:host='.$this->host.';dbname='.$this->database,$this->username,$this->password);
					if($this->pdo){
						$this->pdo->exec('SET NAMES \'UTF8\'');
						$this->connected=1;
					} else {
						trigger_error('Cannot connect to database',E_USER_ERROR);
					}
				} else {
					trigger_error('PDO MySQL extension not enabled',E_USER_ERROR);
				}
				break;
				
			default:
				// Error is triggered for all other database types
				trigger_error('This database type is not supported',E_USER_ERROR);

		}
	}
	
	// Disconnects from database, if connected
	// Returns false if no connection was present
	public function disconnect(){
		// This is only executed if existing connection is detected
		if($this->connected==1 && $this->key!='' && !$this->persistent){
		
			// Resetting the query counter
			$this->queryCounter=0;
			
			// Closing the database
			$this->pdo=null;
			$this->connected=0;
			
		} else {
			return false;
		}
	}

	// Sends query to database and returns associative array of all results
	// Meant for SELECT queries
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the result in an array or returns false when query failed
	public function multiple($query,$variables=array()){
	
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;

			// Preparing a query and executing it
			$query=$this->pdo->prepare($query);
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
				$this->checkError($query);
				return false;
				
			}
					
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}

	// Sends query to database and returns associative array of first returned row
	// Meant for SELECT queries
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the result in an array or returns false when query failed
	public function single($query,$variables=array()){
	
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;

			// Preparing a query and executing it
			$query=$this->pdo->prepare($query);
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
				$this->checkError($query);
				return false;
				
			}

		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}

	// Executes a specific database query 
	// Meant for INSERT, UPDATE, DELETE queries
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns the amount of rows that were affected by the query
	public function command($query,$variables=array()){
	
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;

			// Preparing a query and executing it
			$query=$this->pdo->prepare($query);
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
				$this->checkError($query);
				return false;
				
			}
					
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
	}
	
	// Triggers a PHP error if MySQL encounters error in the query
	// * query - query object from PDO
	// Triggers an error if there was an error
	private function checkError($query){
	
		if($this->connected==1 && $this->showErrors==1){
		
			// Checking if there is error information stored for this request
			$errors=$query->errorInfo();
			if($errors && !empty($errors)){
			
				// PDO errorInfo carries verbose error as third in the index
				trigger_error('Query failed: '.$errors[2],E_USER_ERROR);
				
			}
			
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
		
		// Since error was not triggered, it simply returns true
		return true;
		
	}
	
	// Used to get the last inserted ID from database
	// Returns last ID if found, returns false if last ID was not found
	public function lastId(){
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
	// Always returns true
	public function beginTransaction(){
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;
			
			// Begins transaction
			$this->pdo->beginTransaction();
			
			// Always returns true
			return true;
			
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
	}
	
	// Commits transaction if transactions are supported
	// Always returns true
	public function commitTransaction(){
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;
			
			// Commits transaction
			$this->pdo->commit();
			
			// Always returns true
			return true;
			
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
	}
	
	// Rolls back the changes from transaction
	// Always returns true
	public function rollbackTransaction(){
		if($this->connected==1){
		
			// Query total is being counted for performance review
			$this->queryCounter++;
			
			// Rolls back transaction
			$this->pdo->rollBack();
			
			// Always returns true
			return true;
			
		} else {
			trigger_error('Database not connected',E_USER_ERROR);
		}
	}

}

?>