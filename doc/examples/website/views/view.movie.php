<?php

/**
 * Website Tutorial <http://www.waveframework.com>
 * Tutorial Single Movie View
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
 * @version    3.1.4
 */

class WWW_view_movie extends WWW_Factory {

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
				<p><?=$translations['movie-info']?></p>
				<?php
					$movie=$this->api('movies-get',array('id'=>$view['dynamic-url'][0]));
					if(!isset($movie['error'])){
						?>
						<p><?=$translations['title']?>: <?=$movie['title']?></p>
						<p><?=$translations['year']?>: <?=$movie['year']?></p>
						<?php
					} else {
						$this->setHeader('HTTP/1.1 404 Not Found');
						echo '<p>'.$translations['cannot-find-movie'].'</p>';
					}
				?>
				<a href="<?=$sitemap['list']['url']?>"><?=$translations['back-to-list']?></a>
			</div>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>