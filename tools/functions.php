<?php

/*
WWW Framework
Maintenance function library

This script includes various general-use functions that are used by developer tools in WWW Framework 
that are stored in /tools/ folder. This includes scripts such as directory cleaner and mass-file mover 
through FTP.

* Directory cleaner function
* File index creation function
* FTP file mover
* System backup archive creator
* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// This function clears a folder and all of its subfolders and is run recursively
// * directory - Directory of the file to be moved
// Returns plain text log
function dirCleaner($directory){

	// Log will be stored in this array
	$log=array();
	// Scanning the directory for files
	$files=fileIndex($directory,'files');
	// Scanning the directory for files
	$folders=fileIndex($directory,'folders');
	
	// This will loop over all the files if files were found in this directory
	if($files && !empty($files)){
		foreach($files as $file){
			// Attempting to remove the file
			if(unlink($file)){
				$log[]='DELETED '.$file;
			} else {
				$log[]='FAILED '.$file;
			}
		}
	}
	
	// This will loop over all the folders if folders were found in this directory
	if($folders && !empty($folders)){
		foreach($folders as $folder){
			// Attempting to remove the folder
			if(rmdir($folder)){
				$log[]='DELETED '.$folder;
			} else {
				$log[]='FAILED '.$folder;
			}
		}
	}
	
	// Log is returned
	return $log;
	
}

// This function lists all files and directories in a specific directory
// * directory - Basepath of all files
// * type - What type of index this is, can be 'all', 'files' or 'folders'
// Returns an array with all file and folder addresses
function fileIndex($directory,$type='all',$files=false){

	// File names are stored in this array
	$index=array();
	// Scanning the current directory
	if(!$files){
		$files=scandir($directory);
	}
	
	// This will loop over all the files if files were found in this directory
	if(!empty($files)){
		foreach($files as $f){
			// As long as the current file is not the current or parent directory
			if($f!='.' && $f!='..'){
				// If file is another directory then this is parsed recursively
				if(is_dir($directory.$f)){
					// File data from recursive parsing is merged with current files list
					$index=array_merge($index,fileIndex($directory.$f.DIRECTORY_SEPARATOR,$type));
					// Adding directory to index, if supported
					if($type=='all' || $type=='folders'){
						$index[]=$directory.$f.DIRECTORY_SEPARATOR;
					}
				} else {
					// Adding file to index, if supported
					if($type=='all' || $type=='files'){
						$index[]=$directory.$f;
					}
				}
			}
		}
	}
	
	// Index is returned
	return $index;
	
}

// This function clears a folder and all of its subfolders and is run recursively
// * ftp - FTP connection link
// * from - FTP folder to move files from
// * to - FTP folder to move files to
// Returns plain text log
function ftpFileMover($ftp,$from,$to){

	// Log will be stored in this array
	$log=array();
	// Getting list of files to move
	$files=ftp_nlist($ftp,$from);
	
	// If files exist that have to be moved
	if(!empty($files)){
		// Getting information about the target folder
		$target=ftp_nlist($ftp,$to);
		// Processing each file individually
		foreach($files as $f){
			// As long as the file is not a current or parent directory
			if($f!='.' && $f!='..'){
				// FTP does not have an option to check for directory, so system assumes it based on file size
				if(ftp_size($ftp,$from.$f)>=0){
					// FTP moves the file through rename function
					if(ftp_rename($ftp,$from.$f,$to.$f)){
						// General permissions assigned to file
						ftp_chmod($ftp,0644,$to.$f);
						$log[]='SUCCESS '.$to.$f;
					} else {
						$log[]='FAILED '.$to.$f;
					}
				} else {
					// Checking if the file does not already exist in the folder
					if(!in_array($f,$target)){
						// Creating a new folder
						if(ftp_mkdir($ftp,$to.$f.'/')){
							// General permissions assigned to folder
							ftp_chmod($ftp,0755,$to.$f.'/');
							$log[]='SUCCESS '.$to.$f.'/';
						} else {
							$log[]='FAILED '.$to.$f.'/';
						}
					} else {
						$log[]='UNCHANGED '.$to.$f.'/';
					}
					// Subfolder will be parsed separately
					$log=array_merge($log,ftpFileMover($ftp,$from.$f.'/',$to.$f.'/'));
				}
			}
		}
	}
	
	// Log is returned
	return $log;

}

// This function creates a *.zip archive of all the core files (everything except /filesystem/)
// * source - This is the root folder to create archive from
// * target - This is the target directory where to store the backup
// Returns true if successful
function systemBackup($source,$target,$filesystemBackup=false){

	// This is to find absolute path of source directory
	$root=realpath($source).DIRECTORY_SEPARATOR;
	
	// Default framework file system
	$files=array(
		'controllers',
		'engine',
		'filesystem',
		'models',
		'overrides',
		'resources',
		'tools',
		'views',
		'.htaccess',
		'.version',
		'config.ini',
		'favicon.ico',
		'index.php',
		'license.txt',
		'nginx.conf',
		'readme.txt',
	);
	
	// This returns all absolute paths of all files in $source directory
	$files=fileIndex($root,'files',$files);
	
	// If files exist
	if(!empty($files)){
		// Creating Zip archive
		$zip=new ZipArchive;
		if($zip->open($target,ZipArchive::CREATE)){
			// Archive comment notes the creation date and time
			$zip->setArchiveComment('WWW Framework backup created at '.date('d.m.Y H:i:s').' by script run at '.$_SERVER['REQUEST_URI']);
			// Each file is added to archive
			foreach($files as $f){
				if(is_readable($f)){
					//This is the path it will be stored as in archive
					$archivePath=str_replace($root,'',$f);
					// Checking for directory filtering
					$dir=explode(DIRECTORY_SEPARATOR,$archivePath);
					// Backup ignores filesystem scripts entirely if it is not enabled
					if($filesystemBackup || $dir[0]!='filesystem'){
						// Each file is added individually to archive
						if(!$zip->addFile($f,$archivePath)){
							// Error is thrown when one file cannot be added
							trigger_error('Failed to archive '.$f,E_USER_ERROR);
							// Archive is closed and function returns false
							$zip->close();
							// Removing incomplete archive
							unlink($target);
							// Archive creation failed
							return false;
						}
					}
				}
			}
			// Closing the archive
			$zip->close();
			// Processing complete
			return true;
		} else {
			// Script returns false since archive was not created
			return false;
		}
	} else {
		// Script returns false since archive was not created
		return false;
	}
	
}

?>