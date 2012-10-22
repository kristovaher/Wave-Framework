<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Tools Class
 *
 * This class holds various functionality that also the /tools/ folder has itself. This allows 
 * the system to implement some of the tools functionality within the actual website itself, such 
 * as things like clearing cache in bulk, or returning an index of files from a folder.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/tools.htm
 * @since      3.4.2
 * @version    3.4.2
 */

class WWW_Tools {

	/**
	 * This is the filesystem directory of the project.
	 */
	public $filesystemDirectory=false;
	
	/**
	 * This creates the Tools object. If the filesystem is not sent during the object creation 
	 * then it attempts to find one by default based on requesting script folder. 
	 *
	 * @param string $filesystemDirectory optional filesystem directory
	 * @return object
	 */
	public function __construct($filesystemDirectory=false){
		if(!$filesystemDirectory){
			if(!defined('__ROOT__')){
				define('__ROOT__',__DIR__.DIRECTORY_SEPARATOR);
			}
			$this->filesystemDirectory=__ROOT__.'filesystem'.DIRECTORY_SEPARATOR;
		} else {
			$this->filesystemDirectory=$filesystemDirectory;
		}
	}

		
	/**
	 * This method cleans certain folders in filesystem. A cut-off timestamp can be set, which 
	 * tells the cleaner not to delete certain files if their modification timestamp is newer.
	 *
	 * @param string|array $mode keywords for folders that will be cleaned
	 * @param integer $cutOff timestamp of last-modified on the files, after which the file will not be deleted
	 * @return array as a log
	 */
	public function cleaner($mode='maintenance',$cutoff=false){
	
		// This log array will be returned as a response
		$log=array();
	
		// If cutoff timestamp is not set, then every date is considered valid for cutoff
		if(!$cutoff){
			$cutoff=$_SERVER['REQUEST_TIME'];
		}
		
		// Mode can also be sent with an array
		if(!is_array($mode)){
			$mode=explode(',',$mode);
		}
		
		// Clears /filesystem/cache/output/
		if(in_array('all',$mode) || in_array('output',$mode) || in_array('cache',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears images cache
		if(in_array('all',$mode) || in_array('images',$mode) || in_array('cache',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('all',$mode) || in_array('resources',$mode) || in_array('cache',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('all',$mode) || in_array('custom',$mode) || in_array('cache',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'cache'.DIRECTORY_SEPARATOR.'custom'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears cache tags
		if(in_array('all',$mode) || in_array('tags',$mode) || in_array('cache',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'cache'.DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears user sessions
		if(in_array('all',$mode) || in_array('sessions',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'sessions'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears API session tokens
		if(in_array('all',$mode) || in_array('tokens',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'tokens'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('all',$mode) || in_array('messenger',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'messenger'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears request data of user agent IP's
		if(in_array('all',$mode) || in_array('errors',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'errors'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears system log
		if(in_array('all',$mode) || in_array('logs',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'logs'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears folder from everything that might be stored here
		if(in_array('all',$mode) || in_array('tmp',$mode) || in_array('maintenance',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'tmp'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears request data of user agent IP's
		if(in_array('all',$mode) || in_array('limiter',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'limiter'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears backups
		if(in_array('all',$mode) || in_array('backups',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'backups'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears update archive
		if(in_array('all',$mode) || in_array('updates',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'updates'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears database folder
		if(in_array('all',$mode) || in_array('data',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'data'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears custom user data folder
		if(in_array('all',$mode) || in_array('userdata',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'userdata'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears certificate and key folder
		if(in_array('all',$mode) || in_array('keys',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'keys'.DIRECTORY_SEPARATOR,$cutoff));
		}

		// Clears static files from filesystem
		if(in_array('all',$mode) || in_array('static',$mode)){
			$log=array_merge($log,$this->directoryCleaner($this->filesystemDirectory.'static'.DIRECTORY_SEPARATOR,$cutoff));
		}
		
		// Returning the actions
		return $log;
		
	}
	
	/**
	 * This function returns an index of files, folders or both recursively from the 
	 * requested folder.
	 * 
	 * @param string $directory the folder to browse through
	 * @param string $mode the mode that is used
	 * @return array as a list of files and folders found
	 */
	public function indexer($directory,$mode='both'){
	
		// File names are stored in this array
		$index=array();
		
		// Scanning the current directory
		$files=scandir($directory);
		
		// This will loop over all the files if files were found in this directory
		if(!empty($files)){
			foreach($files as $f){
				// As long as the current file is not the current or parent directory
				if($f!='.' && $f!='..'){
					// If file is another directory then this is parsed recursively
					if(is_dir($directory.$f)){
						// File data from recursive parsing is merged with current files list
						$index=array_merge($index,$this->indexer($directory.$f.DIRECTORY_SEPARATOR,$mode));
						// Adding directory to index, if supported
						if($mode=='both' || $mode=='folders'){
							$index[]=$directory.$f.DIRECTORY_SEPARATOR;
						}
					} else {
						// Adding file to index, if supported
						if($mode=='both' || $mode=='files'){
							$index[]=$directory.$f;
						}
					}
				}
			}
		}
		
		// Index is returned
		return $index;
		
	}
	
	/**
	 * This function calculates the total file size in a folder and all of its subfolders.
	 *
	 * @param string $directory the file directory to check
	 * @return integer total size in bytes
	 */
	public function sizer($directory){
		
		// This variable will hold the total size
		$size=0;
		
		// Making sure that the directory exists
		if(is_dir($directory)){
			// Getting a list of all files in the folder
			$files=$this->indexer($directory,'files');
			if(!empty($files)){
				// Adding each file size to the total
				foreach($files as $file){
					$size+=filesize($file);
				}
			}
		} else {
			// Throwing a warning
			trigger_error('This folder does not exist: '.$directory,E_USER_WARNING);
		}
		
		// Returning the final file size in bytes
		return $size;
		
	}
	
	/**
	 * This is an internal function used by cleaner() method to remove files and folders
	 * and return a log of the result.
	 *
	 * @param string $directory that will be cleaned
	 * @param integer $cutoff the cut-off timestamp
	 * @return array of all the files and what it did to those files
	 */
	private function directoryCleaner($directory,$cutoff=0){
	
		// Log will be stored in this array
		$log=array();
		// Scanning the directory for files
		$files=$this->indexer($directory,'files');
		// Scanning the directory for files
		$folders=$this->indexer($directory,'folders');
		
		// This will loop over all the files if files were found in this directory
		if($files && !empty($files)){
			foreach($files as $file){
				// Testing if file modification date is older than the $cutoff timestamp 
				if(filemtime($file)<=$cutoff){
					// Attempting to remove the file
					if(unlink($file)){
						$log[]='DELETED '.$file;
					} else {
						$log[]='FAILED '.$file;
					}
				} else {
					$log[]='KEPT '.$file;
				}
			}
		}
		
		// This will loop over all the folders if folders were found in this directory
		if($folders && !empty($folders)){
			foreach($folders as $folder){
				// Testing if the directory is empty
				$contents=scandir($folder);
				if(count($contents)<=2){
					// Attempting to remove the folder
					if(rmdir($folder)){
						$log[]='DELETED '.$folder;
					} else {
						$log[]='FAILED '.$folder;
					}
				} else {
					$log[]='NOT EMPTY '.$folder;
				}
			}
		}
		
		// Log is returned
		return $log;
		
	}
	
}
	
?>