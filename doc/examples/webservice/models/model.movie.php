<?php

/**
 * Web Service Tutorial <http://www.waveframework.com>
 * Tutorial Movie Model
 *
 * It is recommended to extend View classes from WWW_Factory in order to 
 * provide various useful functions and API access for the view.
 *
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    Unrestricted
 * @tutorial   /doc/pages/tutorial_webservice.htm
 * @since      1.0.0
 * @version    1.0.0
 */

class WWW_model_movie extends WWW_Factory {

	/**
	 * It is recommended to define all data variables here. Usually the 
	 * data variables have the same names as the column names of database 
	 * rows from a table.
	 */
	public $id=0;
	public $title;
	public $year;
	
	/**
	 * This function simply sets the title of the movie
	 *
	 * @param string $title movie title
	 * @return boolean
	 */
	public function setTitle($title){
		$this->title=$title;
		return true;
	}
	
	/**
	 * This function simply sets the year of the movie
	 *
	 * @param string $year movie year
	 * @return boolean
	 */
	public function setYear($year){
		$this->year=$year;
		return true;
	}
	
	/**
	 * This loads a movie based on its ID
	 *
	 * This example uses simple serialized database in filesystem, but it 
	 * could load data from MySQL or other databases
	 * 
	 * @param integer $id movie ID
	 * @return array if success, boolean if fails
	 */
	public function loadMovie($id=0){
		if($id!=0){
			// Database location
			$dbLoc=$this->getState('data-root').'movies.db';
			// Making sure that current database exists
			if(file_exists($dbLoc)){
				$curDb=unserialize(file_get_contents($dbLoc));
			} else {
				return false;
			}
			// If this movie exists in the database, we assign its values to 
			// current object.
			if(isset($curDb[$id])){
				$this->id=$id;
				$this->title=$curDb[$id]['title'];
				$this->year=$curDb[$id]['year'];
				return array(
					'id'=>$id,
					'title'=>$curDb[$id]['title'],
					'year'=>$curDb[$id]['year']
					);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This loads all movies from database into returned value
	 *
	 * This example uses simple serialized database in filesystem, but it 
	 * could load data from MySQL or other databases
	 *
	 * @return array
	 */
	public function loadAllMovies(){
		// Database location
		$dbLoc=$this->getState('data-root').'movies.db';
		// Making sure that current database exists
		if(file_exists($dbLoc)){
			$curDb=unserialize(file_get_contents($dbLoc));
		} else {
			// Since database does not exist (and thus movies don't exist) we return empty array instead of false
			return array();
		}
		// We store all movie data in a separate array that will be returned
		$allMovies=array();
		foreach($curDb as $id=>$data){
			$movie=array();
			$movie['id']=$id;
			$movie['title']=$data['title'];
			$movie['year']=$data['year'];
			$allMovies[]=$movie;
		}
		return $allMovies;
	}

	/**
	 * This saves the current movie in database
	 *
	 * This example uses simple serialized database in filesystem, but it 
	 * could load data from MySQL or other databases
	 *
	 * @return integer for movie ID if success, false if fails
	 */
	public function saveMovie(){
		// Making sure that title and year are both set
		if($this->title!='' && $this->year!=''){
			// Database location
			$dbLoc=$this->getState('data-root').'movies.db';
			// If database file already exists, we simply load the database and unserialize its data to add a new movie to it
			if(file_exists($dbLoc)){
				$curDb=unserialize(file_get_contents($dbLoc));
				// ID's are indexes in the stored array, so we seek the highest index in that array
				$nextId=max(array_keys($curDb))+1;
			} else {
				// Since database did not exist, an array is created for new database
				$curDb=array();
				$nextId=1;
			}
			// Creating data node of current movie
			$movie=array();
			$movie['title']=$this->title;
			$movie['year']=$this->year;
			// Adding the new node into database array
			$curDb[$nextId]=$movie;
			// We overwrite the old database with the updated database with a new movie
			if(file_put_contents($dbLoc,serialize($curDb))){
				return $nextId;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This deletes movie with a specific ID from database
	 *
	 * This example uses simple serialized database in filesystem, but it 
	 * could load data from MySQL or other databases
	 * 
	 * @param string $id movie ID
	 * @return boolean
	 */
	public function deleteMovie($id=0){
		// This function, if defined with an ID, deletes specific ID, otherwise it deletes currently active ID
		if($id!=0){
			$deleteId=$id;
		} else if($this->id!=0){
			$deleteId=$this->id;
		} else {
			// No ID was set in here nor in the current object
			return false;
		}
		// Database location
		$dbLoc=$this->getState('data-root').'movies.db';
		// If database does not exist then we have no movies to delete
		if(file_exists($dbLoc)){
			$curDb=unserialize(file_get_contents($dbLoc));
		} else {
			return false;
		}
		// If such an ID exists in database, it is simply unset
		if(isset($curDb[$deleteId])){
			unset($curDb[$deleteId]);
		} else {
			return false;
		}
		// We overwrite the old database with the updated database
		if(file_put_contents($dbLoc,serialize($curDb))){
			return true;
		} else {
			return false;
		}
	}

}
	
?>