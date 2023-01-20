<?php

/**
 * Website Tutorial <http://github.com/kristovaher/Wave-Framework>
 * Tutorial Movie List View
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
 * @version    1.0.0
 */

class WWW_view_list extends WWW_Factory {

	/**
	 * View Controller calls this function as output for page content.
	 * 
	 * This method returns null by default because the API will load the 
	 * result from output buffer, if the API call echoes/prints any output. 
	 * It is recommended for View methods not to return any variable data.
	 *
	 * @param array $input input array from View Controller
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
					$movies=$this->api('movies-all');
					if(!isset($movies['error'])){
						echo '<table id="movies">';
						echo '<tr>';
							echo '<th>'.$translations['title'].'</th>';
							echo '<th>'.$translations['year'].'</th>';
						echo '</tr>';
						foreach($movies as $m){
							echo '<tr>';
								echo '<td><a href="'.str_replace(':0:',$m['id'],$sitemap['movie']['url']).'">'.$m['title'].'</a></td>';
								echo '<td>'.$m['year'].'</td>';
							echo '</tr>';
						}
						echo '</table>';
					} else {
						echo '<p>'.$translations['cannot-find-movies'].'</p>';
					}
					// If this is set, then movie was added
					if(isset($input['ok-notification'])){
						echo '<p><b>'.$translations['movie-added'].'</b></p>';
					}
				?>		
			</div>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>