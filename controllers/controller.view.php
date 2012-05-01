<?php

/*
WWW Framework
MVC Controller class

View class is used by index.php to load view from /views/ folder and output it to the requesting 
user agent. View generates typical HTML header, includes various resource files and loads a view 
that WWW_controller_url detected being the proper URL that is loaded. WWW_controller_view also 
loads translations file from /resources/{language-code}.translations.php that can be used for 
different languages for the same views. This class also loads Javascript and jQuery libraries 
that include useful user interface development functionality.

* This file can be edited to be suitable based on project
* This view controller includes majority of required settings for a modern website header
* Loads view from /views/ subfolder

Author and support: Kristo Vaher - kristo@waher.net
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_controller_view extends WWW_Factory {

	// This is called by index.php gateway when initializing view
	public function load($input){
	
		// Unsetting input data that was used only by API
		unset($input['www-command']);
		unset($input['www-return-type']);
		unset($input['www-cache-timeout']);
	
		// Getting view information
		$viewData=$this->getState('view-data');
		$systemRoot=$this->getState('system-root');
	
		// Getting current view and creating view object
		$viewObject=$this->getView($viewData['view']);
		
		// Getting author and copyright, if set in configuration
		$author=$this->getState('author');
		$copyright=$this->getState('copyright');
		
		// Meta title can be set in Sitemap and can also be different based on 
		if(isset($viewData['meta-title'])){
			// Here you should add your custom meta title loading, if necessary
			if(method_exists($viewObject,'getTitle')){
				$prependTitle=$viewObject->getTitle($viewData);
				// Title is only appended if it exists and is not empty
				if($prependTitle && $prependTitle!=''){
					$viewData['meta-title'].=$prependTitle.' - '.$viewData['meta-title'];
				}
			}
		} else {
			// Project title is an empty string, if it was not defined
			$viewData['meta-title']='';
		}
		
		// Project title will be added to the end, if it is set
		if($viewData['project-title']!=''){
			if($viewData['meta-title']!=''){
				$viewData['meta-title'].=' - '.$viewData['project-title'];
			} else {
				$viewData['meta-title']=$viewData['project-title'];
			}
		}
		
		// List of CSS Stylesheets to load from resources folder
		// These scripts will all be unified to a single CSS
		$coreStyleSheet=array();
		$coreStyleSheet[]='reset.css';
		$coreStyleSheet[]='style.css';
		
		// Module-specific Stylesheets is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$viewData['view'].'.style.css')){
			$moduleStylesheet=$viewData['view'].'.style.css';
		}
		
		// List of JavaScript to load from resources folder
		// These scripts will all be unified to a single JavaScript
		$coreJavaScript=array();
		$coreJavaScript[]='jquery.js';
		$coreJavaScript[]='script.js';
		
		// Module-specific JavaScript is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$viewData['view'].'.script.js')){
			$moduleJavaScript=$viewData['view'].'.script.js';
		}
		
		// HTML frame is generated with meta data and resource files
		?>
			<!DOCTYPE html>
			<html lang="<?=$this->getState('language')?>">
				<head>
					<title><?=$viewData['meta-title']?></title>
					<!-- Base address for relative links -->
					<base href="<?=$this->getState('web-root')?>"/>
					<!-- UTF-8 -->
					<meta charset="utf-8">
					<!-- Useful for mobile applications -->
					<meta name="viewport" content="width=device-width"/> 
					<?php if($viewData['robots']!=''){ ?>
						<!-- Robots -->
						<meta content="<?=$viewData['robots']?>" name="robots"/>
					<?php } ?>
					<!-- Content information -->
					<?php if(isset($viewData['meta-keywords'])){ ?>
						<meta name="Keywords" content="<?=$viewData['meta-keywords']?>"/>
					<?php } ?>
					<?php if(isset($viewData['meta-description'])){ ?>
						<meta name="Description" content="<?=$viewData['meta-description']?>"/>
					<?php } ?>
					<?php if($author){ ?>
						<meta name="Author" content="<?=$author?>"/>
					<?php } ?>
					<?php if($copyright){ ?>
						<meta name="Copyright" content="<?=$copyright?>"/>
					<?php } ?>
					<!-- Stylesheets -->
					<link type="text/css" href="resources/<?=implode('&',$coreStyleSheet)?>" rel="stylesheet" media="all"/>
					<?php if(isset($moduleStylesheet)){ ?>
						<link type="text/css" href="resources/<?=$moduleStylesheet?>" rel="stylesheet" media="all"/>
					<?php } ?>
					<!-- Favicons -->
					<link rel="icon" href="favicon.ico" type="image/x-icon"/>
					<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon"/>
					<!-- JavaScript -->
					<script type="text/javascript" src="engine/class.www-wrapper.js"></script>
					<script type="text/javascript" src="resources/<?=implode('&',$coreJavaScript)?>"></script>
					<?php if(isset($moduleJavaScript)){ ?>
						<script type="text/javascript" src="resources/<?=$moduleJavaScript?>"></script>
					<?php } ?>
				</head>
				<body>
				<?php
					// View object is rendered
					$viewObject->render($input);
				?>
				</body>
			</html>
		<?php
		
		// Processing complete
		return true;
	
	}
	
}
	
?>