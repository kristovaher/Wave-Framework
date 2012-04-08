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
	
		// Getting current view and creating view object
		$view=$input['www-view-data']['view'];
		$viewObject=$this->getView($view);
		
		// Current language identifier
		$language=$input['www-view-data']['language'];
		
		// Root addresses for file and link handling
		$webRoot=$input['www-view-data']['web-root'];
		$systemRoot=$input['www-view-data']['system-root'];
		
		// Getting author and copyright, if set in configuration
		$author=$this->getState('author');
		$copyright=$this->getState('copyright');
		
		// Meta title can be set in Sitemap and can also be different based on 
		if(isset($input['www-view-data']['meta-title'])){
			$metaTitle=$input['www-view-data']['meta-title'];
			// Here you should add your custom meta title loading, if necessary
			if(method_exists($viewObject,'getTitle')){
				$appendTitle=$viewObject->getTitle($input['www-view-data']);
				// Title is only appended if it exists and is not empty
				if($appendTitle && $appendTitle!=''){
					$metaTitle.=$appendTitle.' - '.$metaTitle;
				}
			}
		} else {
			// Project title is an empty string, if it was not defined
			$metaTitle='';
		}
		
		// Project title will be added to the end, if it is set
		if($input['www-view-data']['project-title']!=''){
			if($metaTitle!=''){
				$metaTitle.=' - '.$input['www-view-data']['project-title'];
			} else {
				$metaTitle=$input['www-view-data']['project-title'];
			}
		}
		
		// Current translations are loaded
		$translations=$input['www-view-data']['translations'];
		
		// List of CSS Stylesheets to load from resources folder
		// These scripts will all be unified to a single CSS
		$coreStyleSheet=array();
		$coreStyleSheet[]='reset.css';
		$coreStyleSheet[]='style.css';
		
		// Module-specific Stylesheets is can also be loaded
		$moduleStylesheet=array();
		// Module specific stylesheets can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$view.'.style.css')){
			$moduleStylesheet[]=$view.'.style.css';
		}
		
		// List of JavaScript to load from resources folder
		// These scripts will all be unified to a single JavaScript
		$coreJavaScript=array();
		$coreJavaScript[]='jquery.js';
		$coreJavaScript[]='script.js';
		
		// Module-specific JavaScript is can also be loaded
		$moduleJavaScript=array();
		// Module specific translations are also possible
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$view.'.script.js')){
			$moduleJavaScript[]=$view.'.script.js';
		}
		// It is possible to also load JavaScript-specific translations
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.translations.js')){
			$moduleJavaScript[]=$language.'.translations.js';
		}
		// Translations could also be related to the current view
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.'.$view.'.translations.js')){
			$moduleJavaScript[]=$language.'.'.$view.'.translations.js';
		}
		
		// HTML frame is generated with meta data and resource files
		?>
			<!DOCTYPE html>
			<html lang="<?=$language?>">
				<head>
					<title><?=$metaTitle?></title>
					<!-- UTF-8 -->
					<meta charset="utf-8">
					<!-- Useful for mobile applications -->
					<meta name="viewport" content="width=device-width"/> 
					<?php if($input['www-view-data']['robots']!=''){ ?>
						<!-- Robots -->
						<meta content="<?=$input['www-view-data']['robots']?>" name="robots"/>
					<?php } ?>
					<!-- Content information -->
					<?php if(isset($input['www-view-data']['meta-keywords'])){ ?>
						<meta name="Keywords" content="<?=$input['www-view-data']['meta-keywords']?>"/>
					<?php } ?>
					<?php if(isset($input['www-view-data']['meta-description'])){ ?>
						<meta name="Description" content="<?=$input['www-view-data']['meta-description']?>"/>
					<?php } ?>
					<?php if($author){ ?>
						<meta name="Author" content="<?=$author?>"/>
					<?php } ?>
					<?php if($copyright){ ?>
						<meta name="Copyright" content="<?=$copyright?>"/>
					<?php } ?>
					<!-- Stylesheets -->
					<link type="text/css" href="<?=$webRoot?>resources/<?=implode('&',$coreStyleSheet)?>" rel="stylesheet" media="all"/>
					<?php if(!empty($moduleStylesheet)){ ?>
						<link type="text/css" href="<?=$webRoot?>resources/<?=implode('&',$moduleStylesheet)?>" rel="stylesheet" media="all"/>
					<?php } ?>
					<!-- System state -->
					<script type="text/javascript">
						var WWW=new Object();
						WWW['web-root']='<?=$webRoot?>';
						WWW['language']='<?=$language?>';
						// If translations are used then they are stored in this object
						var translations=new Object();
					</script>
					<!-- JavaScript -->
					<script type="text/javascript" src="<?=$webRoot?>engine/class.www-wrapper.js"></script>
					<script type="text/javascript" src="<?=$webRoot?>resources/<?=implode('&',$coreJavaScript)?>"></script>
					<?php if(!empty($moduleJavaScript)){ ?>
						<script type="text/javascript" src="<?=$webRoot?>resources/<?=implode('&',$moduleJavaScript)?>"></script>
					<?php } ?>
					<!-- Favicons -->
					<link rel="icon" href="<?=$webRoot?>favicon.ico" type="image/x-icon"/>
					<link rel="icon" href="<?=$webRoot?>favicon.ico" type="image/vnd.microsoft.icon"/>
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