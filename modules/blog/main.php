<?php

	class blogMain{
		
		protected $currentItem = null;
		
		private function __construct(){
			
		}
		
		protected function createDefaultBlogItem(){
			$content = '<p><img class="thumbnail" src="'.$this->url.'/getfile/'.$this->currentItem->param.'">'.$this->currentItem->content.'</p>';
			return $this->createBlogItem($content);
		}
		
		protected function createBigPictureBlogItem(){
			$content = '
				<img class="blogImage" src="'.$this->url.'/getfile/'.$this->currentItem->param.'">
				<p>'.$this->currentItem->content.'</p>
			';
			return $this->createBlogItem($content);
		}
		
		protected function createYoutubeBlogItem(){
			
		}
		
		protected function createBigImageBlogItem(){
			
		}
		
		protected function createBlogItem($content){
			return '
				<article>
  					<header>
  						<h1>'.$this->currentItem->title.'</h1>
  					</header>
  					'.$content.'
  					<footer>
						<ul>
							<li>'.$this->currentItem->author.'</li>
							<li>'.$this->currentItem->date.'</li>
							<li>'.$this->currentItem->time.'</li>
							<li><a>'.$this->countItemReactions().' reacties</a></li>
						</ul>
					</footer>
  				</article>
			';
		}
		
		protected function countItemReactions(){
			return $this->db->one("SELECT COUNT(*) FROM `web_blog_reactions` WHERE `blog_item` = :id", ':id', $this->currentItem->id);
		}
		
		protected function countItems(){
			return $this->db->one("SELECT COUNT(*) FROM `web_blog_items`");
		}
		
	}

?>