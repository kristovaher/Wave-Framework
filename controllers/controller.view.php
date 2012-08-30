<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * URL Controller
 *
 * Wave Framework comes with a View Controller and a translations system that is used to build 
 * a website on Wave Framework. This View Controller is entirely optional and can be removed 
 * from a system if you plan to implement your own View Controller or simply use Wave Framework 
 * for API, without a website.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/guide_view.htm
 * @since      1.0.0
 * @version    3.1.4
 */

class WWW_controller_view extends WWW_Factory {

	/**
	 * This method is called by Data Handler to render View contents based on values from 
	 * URL Controller.
	 *
	 * @param array [$input] input data sent to controller
	 * @input mixed input data from URL Controller output
	 * @return null
	 */
	public function load($input){
	
		// Unsetting input data that are only used by API and are accessible elsewhere by the user
		unset($input['www-command'],$input['www-return-type'],$input['www-cache-timeout'],$input['www-request'],$input['www-cookie'],$input['www-session'],$input['www-cache-tags']);
	
		// Getting view information
		$view=$this->getState('view');
		$systemRoot=$this->getState('system-root');
	
		// Getting current view and creating view object
		$viewObject=$this->getView($view['view']);
		
		// Getting author and copyright, if set in configuration
		$author=$this->getState('author');
		$copyright=$this->getState('copyright');
		
		// Meta title can be set in Sitemap and can also be different based on 
		if(isset($view['meta-title'])){
			// Here you should add your custom meta title loading, if necessary
			if(method_exists($viewObject,'getTitle')){
				$prependTitle=$viewObject->getTitle($view);
				// Title is only appended if it exists and is not empty
				if($prependTitle && $prependTitle!=''){
					$view['meta-title'].=$prependTitle.' - '.$view['meta-title'];
				}
			}
		} else {
			// Project title is an empty string, if it was not defined
			$view['meta-title']='';
		}
		
		// Project title will be added to the end, if it is set
		if($view['project-title']!=''){
			if($view['meta-title']!=''){
				$view['meta-title'].=' - '.$view['project-title'];
			} else {
				$view['meta-title']=$view['project-title'];
			}
		}
		
		// List of CSS Stylesheets to load from resources folder
		// These scripts will all be unified to a single CSS
		$coreStyleSheet=array();
		$coreStyleSheet[]='reset.css';
		$coreStyleSheet[]='style.css';
		
		// Module-specific Stylesheets is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$view['view'].'.style.css')){
			$moduleStylesheet=$view['view'].'.style.css';
		}
		
		// List of JavaScript to load from resources folder
		// These scripts will all be unified to a single JavaScript
		$coreJavaScript=array();
		$coreJavaScript[]='jquery.js';
		$coreJavaScript[]='script.js';
		
		// Module-specific JavaScript is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.$view['view'].'.script.js')){
			$moduleJavaScript=$view['view'].'.script.js';
		}
		
		// HTML frame is generated with meta data and resource files
		?>
			<!DOCTYPE html>
			<html lang="<?=$this->getState('language')?>">
				<head>
					<title><?=$view['meta-title']?></title>
					<!-- Base address for relative links -->
					<base href="<?=$this->getState('base-url')?>"/>
					<!-- UTF-8 -->
					<meta charset="utf-8">
					<!-- Useful for mobile applications -->
					<meta name="viewport" content="width=device-width"/> 
					<?php if($view['robots']!=''){ ?>
						<!-- Robots -->
						<meta content="<?=$view['robots']?>" name="robots"/>
					<?php } ?>
					<!-- Content information -->
					<?php if(isset($view['meta-keywords'])){ ?>
						<meta name="Keywords" content="<?=$view['meta-keywords']?>"/>
					<?php } ?>
					<?php if(isset($view['meta-description'])){ ?>
						<meta name="Description" content="<?=$view['meta-description']?>"/>
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
					$viewObject->$view['view-method']($input);
				?>
				</body>
			</html>
		<?php
		
		// API Will load result data from output buffer
		return null;
	
	}
	
}
	
?>