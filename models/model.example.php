<?php

/**
 * MyProjectNameHere <http://www.example.com>
 * Example Model
 *
 * It is recommended to extend View classes from WWW_Factory in order to 
 * provide various useful functions and API access for the view.
 *
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    3.1.4
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
	 * @param integer [$id] identifier of the object loaded
	 * @return boolean
	 */
	public function load($id){
		// $data=$this->dbSingle('SELECT * FROM table WHERE id=?',array($id));
		// if($data){ 
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
			'id' => $this->id,
			'name'=>$this->name
		);
	}

	/**
	 * This function is intended to save data to database
	 *
	 * @return boolean
	 */
	public function save(){
		// $update=$this->dbCommand('UPDATE table SET name=? WHERE id=?',array($this->name,$this->id));
		// if($update){
			return true;
		// } else { 
			// return false;
		// }
	}

}
	
?>