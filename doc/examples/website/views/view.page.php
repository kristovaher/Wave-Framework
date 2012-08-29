<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Tutorial Page View
 *
 * It is recommended to extend View classes from WWW_Factory in order to 
 * provide various useful functions and API access for the view.
 *
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    Unrestricted
 * @tutorial   /doc/pages/tutorial_website.htm
 * @since      1.0.0
 * @version    3.1.3
 */

class WWW_view_page extends WWW_Factory {

	/**
	 * View Controller calls this function as output for page content.
	 * 
	 * This method returns null by default because the API will load the 
	 * result from output buffer, if the API call echoes/prints any output. 
	 * It is recommended for View methods not to return any variable data.
	 *
	 * @param array [$input] input array from View Controller
	 * @return null
	 */
	public function render($input){
	
		// Loading translations
		$translations=$this->getTranslations();
		// Loading sitemap
		$sitemap=$this->getSitemap();
		// Loading view data
		$view=$this->getState('view');
		?>
			<div id="header">
				<a href="<?=$sitemap['page/contact']['url']?>"><?=$sitemap['page/contact']['meta-title']?></a>
				<a href="<?=$sitemap['page/about']['url']?>"><?=$sitemap['page/about']['meta-title']?></a>
				<a href="<?=$sitemap['add']['url']?>"><?=$sitemap['add']['meta-title']?></a>
				<a href="<?=$sitemap['list']['url']?>"><?=$sitemap['list']['meta-title']?></a>
				<a href="<?=$sitemap['home']['url']?>"><?=$sitemap['home']['meta-title']?></a>
			</div>
			<div id="body">
				<?php
					switch($view['subview']){
						case 'about':
							echo '<p>'.$translations['about-content'].'</p>';
							break;
						case 'contact':
							echo '<p>'.$translations['contact-content'].'</p>';
							break;
					}
				?>
			</div>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>