<?php

/**
 * MyProjectNameHere <http://www.example.com>
 * Model Class
 *
 * It is recommended to extend Model classes from WWW_Factory in order to 
 * provide various useful functions and API access for the Model.
 *
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    1.0.0
 */

class WWW_model_example extends WWW_Factory {

	/**
	 * It is recommended to define all data variables here. Usually the 
	 * data variables have the same names as the column names of database 
	 * rows from a table.
	 */
	public $id=0;
	public $name='';

	/**
	 * Alternative to __construct()
	 *
	 * WWW_Factory does not allow to overwrite __construct() method, so 
	 * this __initialize() is used instead and loaded automatically when 
	 * object is created.
	 *
	 * @return boolean
	 */
	public function __initialize(){
	
		// Do something here
		return true;
		
	}

	/**
	 * This is intended to load data from database
	 *
	 * @param integer $id identifier of the object loaded
	 * @return boolean
	 */
	public function load($id){
	
		// Attempting to find the table row
		// $data=$this->dbSingle('SELECT * FROM table WHERE id=?',array($id));
		// if($data){ 
			// Assigning data to object parameters
			// $this->id=$data['id'];
			// $this->name=$data['name'];
			
			// This below is just an example without database, used in some tutorials
			$this->id=$id;
			$this->name='Lorem Ipsum #'.rand(1,1000); // This is used for simply testing cache
			return true;
			
		// } else {
			// return false; 
		// }
		
	}

	/**
	 * This returns all of the data of currently open object
	 *
	 * @return array
	 */
	public function get(){
	
		// Current Data is returned as an array
		return array(
			'id'=>$this->id,
			'name'=>$this->name
		);

	}
	
	/**
	 * This returns multiple objects from database as array entries, similar to regular 
	 * get() method call. It also supports filtering.
	 *
	 * @param $config array of configuration data for the call, filters and so on
	 * @return array
	 */
	public function all($config){
	
		// If specific fields are requested
		if(isset($config['fields'])){
			$this->filter($config['fields'],'alphanumeric','_*');
		} else {
			$fields='*';
		}
	
		// Total row calculation is not necessary, but can be sometimes useful
		$query='SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM table ';
		
		// Filtering data
		$filters=array();
		$filterData=array();
		
		// Filtering with a field, if set
		if(isset($config['filter-name']) && trim($config['filter-name'])!=''){ $filters[]='table.name LIKE ?'; $filterData[]='%'.str_replace(array('%','_'),array('\\%','\\_'),$config['filter-name']).'%'; }
		
		// Building filtered query, if filters are set
		if(!empty($filters)){
			foreach($filters as $key=>$filter){
				if($key==0){
					$query.=' WHERE '.$filter;
				} else {
					$query.=' AND '.$filter;
				}
			}
		}

		// If ordering settings are used
		if(isset($config['order-by'])){
			if(!isset($config['order'])){ $config['order']='DESC'; }
			$query.=' ORDER BY '.$this->filter($config['order-by'],'alphanumeric','_').' '.$this->filter($config['order'],'alphanumeric','_');
		}
		
		// If only certain amount of rows are requested
		if(isset($config['limit'])){
			// Pre-setting limit from value if it was not sent
			if(!isset($config['limit-from'])){ $config['limit-from']='0'; }
			$query.=' LIMIT '.$this->filter($config['limit-from'],'integer').','.$this->filter($config['limit'],'integer');
		}
		
		// Making the request
		$rows=$this->dbMultiple($query,$filterData);
		if($rows){
			// Finding total rows
			$totalRows=$this->dbSingle('SELECT FOUND_ROWS() as rows;');
			// Returning entries and the total row count
			return array('entries'=>$rows,'total'=>$totalRows['rows']);
		} else {
			// Returning empty data
			return array('entries'=>array(),'total'=>0);
		}
		
	}

	/**
	 * This function is intended to save data to database
	 *
	 * @return integer|boolean
	 */
	public function save(){
	
		// These variables hold the data for prepared statement
		$query=array();
		$data=array();
		
		// Preparing query command and data value for prepared statement
		$query[]='name=?';
		$data[]=$this->name;
		
		// Update if ID exists, otherwise insert
		if($this->id){
			$data[]=$this->id;
			$save=$this->dbCommand('UPDATE table SET '.implode(',',$query).' WHERE id=?;',$data);
		} else {
			$save=$this->dbCommand('INSERT INTO table SET '.implode(',',$query).';',$data);
			$this->id=$this->dbLastId();
		}
		
		// Returning the ID if the adding was a success
		if($save){
			// Returning the ID
			return $this->id;
		} else {
			// Database command failed
			return false;
		}
		
	}
	
	/**
	 * This function is intended to save data to database
	 *
	 * @return boolean
	 */
	public function delete($id){
	
		// Attempting to delete the row
		if($this->dbCommand('DELETE FROM table WHERE id=?',array($id))){
			// Returning the ID of deleted row
			return $id;
		} else {
			// Database command failed
			return false;
		}
		
	}

}
	
?>