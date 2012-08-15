<?php

class WWW_controller_movies extends WWW_Factory {

	// Data to be returned is stored in this variable
	public $returnData=array();
	
	// This function adds a movie in database
	public function add($input){
		if(!isset($input['title']) || $input['title']==''){
			$this->returnData['error']='Title is missing';
		} else if(!isset($input['year']) || $input['year']==''){
			$this->returnData['error']='Year is missing';
		} else {
			// This loads the model object from the class we created
			$movie=$this->getModel('movie');
			$movie->setTitle($input['title']);
			$movie->setYear($input['year']);
			if($movie->saveMovie()){
				$this->returnData['success']='Movie saved!';
			} else {
				$this->returnData['error']='Could not save movie!';
			}
		}
		return $this->returnData;
	}
	
	// This returns data about a movie based on ID
	public function get($input){
		if(!isset($input['id']) || $input['id']=='' || $input['id']==0){
			$this->returnData['error']='ID is incorrect!';
		} else {
			$movie=$this->getModel('movie');
			$movie=$movie->loadMovie($input['id']);
			if($movie){
				$this->returnData=$movie;
			} else {
				$this->returnData['error']='Cannot find movie with this ID!';
			}
		}
		return $this->returnData;
	}
	
	// This loads all listed movies from database
	public function all(){
		$movies=$this->getModel('movie');
		$movies=$movies->loadAllMovies();
		if($movies){
			$this->returnData=$movies;
		} else {
			$this->returnData['error']='Cannot find movies!';
		}
		return $this->returnData;
	}
	
	// This deletes a movie from database
	public function delete($input){
		if(!isset($input['id']) || $input['id']=='' || $input['id']==0){
			$this->returnData['error']='ID is incorrect!';
		} else {
			$movie=$this->getModel('movie');
			$movie=$movie->deleteMovie($input['id']);
			if($movie){
				$this->returnData['success']='Movie deleted!';
			} else {
				$this->returnData['error']='Cannot find movie with this ID!';
			}
		}
		return $this->returnData;
	}

}
	
?>