<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * View Controller
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
 * @version    3.5.0
 */

class WWW_controller_view extends WWW_Factory {

	/**
	 * This method is called by Data Handler to render View contents based on values from 
	 * URL Controller.
	 *
	 * @param array $input input data sent to controller
	 * @input mixed input data from URL Controller output
	 * @return null
	 */
	public function load($input){
	
		// Unsetting input data that are only used by API and are accessible elsewhere by the user
		unset($input['www-command'],$input['www-return-type'],$input['www-cache-timeout'],$input['www-cache-load-timeout'],$input['www-request'],$input['www-session'],$input['www-cache-tags']);
	
		// Getting view information
		$view=$this->viewData();
		$systemRoot=$this->getState('directory-system');
		
		// If PHP libraries are set to be loaded, then loading them through Factory
		if(isset($view['additional-php']) && $view['additional-php']!=''){
			// Libraries are in a comma-separated list
			$libraries=explode(',',$view['additional-php']);
			foreach($libraries as $l){
				if(file_exists($systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$l.'.php')){
					// Requiring override file
					require($systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$l.'.php');
				} else {
					// Requiring original file
					require($systemRoot.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$l.'.php');
				}
			}
		}
		
		// Checking for view-specific PHP script
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$view['view'].'.script.php')){
			if(file_exists($systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$view['view'].'.script.php')){
				// Requiring override file
				require($systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$view['view'].'.script.php');
			} else {
				// Requiring original file
				require($systemRoot.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$view['view'].'.script.php');
			}
		}
	
		// Getting current view and creating view object
		$viewObject=$this->getView($view['view']);
		
		// Getting author and copyright, if set in configuration
		$author=$this->getState('project-author');
		$copyright=$this->getState('project-copyright');
		
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
		
		// If Sitemap file has defined additional external CSS stylesheets
		if(isset($view['external-css']) && $view['external-css']!=''){
			$externalStylesheet=explode(',',$view['external-css']);
		}
		
		// If Sitemap file has defined additional CSS files
		if(isset($view['additional-css']) && $view['additional-css']!=''){
			$additionalStylesheet=explode(',',$view['additional-css']);
			foreach($additionalStylesheet as $key=>$file){
				$additionalStylesheet[$key]=$file.'.css';
			}
		}
		
		// Module-specific Stylesheets is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.'styles'.DIRECTORY_SEPARATOR.$view['view'].'.style.css')){
			$moduleStylesheet=$view['view'].'.style.css';
		}
		
		// List of JavaScript to load from resources folder
		// These scripts will all be unified to a single JavaScript
		$coreJavaScript=array();
		$coreJavaScript[]='jquery.js';
		$coreJavaScript[]='script.js';
		// These files do not have to be in /resources/ folder and are loaded from engine folder instead
		$coreJavaScript[]='class.www-wrapper.js';
		$coreJavaScript[]='class.www-factory.js';
		
		// If Sitemap file has defined additional external JavaScript libraries
		if(isset($view['external-js']) && $view['external-js']!=''){
			$externalJavaScript=explode(',',$view['external-js']);
		}
		
		// If Sitemap file has defined additional JavaScript libraries
		if(isset($view['additional-js']) && $view['additional-js']!=''){
			$additionalJavaScript=explode(',',$view['additional-js']);
			foreach($additionalJavaScript as $key=>$library){
				$additionalJavaScript[$key]=$library.'.js';
			}
		}
		
		// Module-specific JavaScript is can also be loaded
		if(file_exists($systemRoot.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$view['view'].'.script.js')){
			$moduleJavaScript=$view['view'].'.script.js';
		}
		
		// Rendering the view object and capturing the output
		ob_start();
		$viewObject->$view['view-method']($input);
		$viewContent=ob_get_clean();
		
		// Header collector data for view
		$viewHeaders=$this->getState('view-headers');
		
		// HTML frame is generated with meta data and resource files
		?>
			<!DOCTYPE html>
			<html lang="<?=$this->getState('language')?>" <?=(isset($view['appcache']) && $view['appcache']==1)?'manifest="'.$this->getState('url-base').'manifest.appcache"':''?>>
				<head>
					<title><?=$view['meta-title']?></title>
					<!-- Base address for relative links -->
					<base href="<?=$this->getState('url-base')?>"/>
					<!-- UTF-8 -->
					<meta charset="utf-8">
					<!-- Useful for mobile applications -->
					<meta name="viewport" content="width=device-width"/> 
					<?php if($view['robots']!=''){ ?>
						<!-- Robots -->
						<meta name="robots" content="<?=$view['robots']?>"/>
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
					<link type="text/css" href="resources/styles/<?=implode('&',$coreStyleSheet)?>" rel="stylesheet" media="all"/>
					<?php if(isset($externalStylesheet)){ ?>
						<?php foreach($externalStylesheet as $style){ ?>
							<link type="text/css" href="<?=implode('&',$style)?>" rel="stylesheet" media="all"/>
						<?php } ?>
					<?php } ?>
					<?php if(isset($additionalStylesheet)){ ?>
						<link type="text/css" href="resources/styles/<?=implode('&',$additionalStylesheet)?>" rel="stylesheet" media="all"/>
					<?php } ?>
					<?php if(isset($moduleStylesheet)){ ?>
						<link type="text/css" href="resources/styles/<?=$moduleStylesheet?>" rel="stylesheet" media="all"/>
					<?php } ?>
					<?php if(isset($viewHeaders['style'])){ ?>
						<link type="text/css" href="resources/styles/<?=implode('&',$viewHeaders['style'])?>" rel="stylesheet" media="all"/>
					<?php } ?>
					<!-- Favicons -->
					<link rel="icon" href="favicon.ico" type="image/x-icon"/>
					<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon"/>
					<!-- JavaScript -->
					<script type="text/javascript" src="resources/scripts/<?=implode('&',$coreJavaScript)?>"></script>
					<?php if(isset($externalJavaScript)){ ?>
						<?php foreach($externalJavaScript as $script){ ?>
							<script type="text/javascript" src="<?=$script?>"></script>
						<?php } ?>
					<?php } ?>
					<?php if(isset($viewHeaders['library'])){ ?>
						<?php foreach($viewHeaders['library'] as $library){ ?>
							<script type="text/javascript" src="resources/libraries/<?=$library?>"></script>
						<?php } ?>
					<?php } ?>
					<?php if(isset($additionalJavaScript)){ ?>
						<script type="text/javascript" src="resources/scripts/<?=implode('&',$additionalJavaScript)?>"></script>
					<?php } ?>
					<?php if(isset($moduleJavaScript)){ ?>
						<script type="text/javascript" src="resources/scripts/<?=$moduleJavaScript?>"></script>
					<?php } ?>
					<?php if(isset($viewHeaders['script'])){ ?>
						<script type="text/javascript" src="resources/scripts/<?=implode('&',$viewHeaders['script'])?>"></script>
					<?php } ?>
					<?php if(isset($viewHeaders['other'])){ ?>
						<?php foreach($viewHeaders['other'] as $other){ ?>
							<?=$other?>
						<?php } ?>
					<?php } ?>
				</head>
				<body>
					<?php
						// View content is rendered
						echo $viewContent;
					?>
				</body>
			</html>
		<?php
		
		// API Will load result data from output buffer
		return null;
	
	}
	
}

?>