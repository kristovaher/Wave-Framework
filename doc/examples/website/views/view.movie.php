<?php

class WWW_view_movie extends WWW_Factory {

	// View Controller calls this function as output for page content
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
		
	}

}
	
?>