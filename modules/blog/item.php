<?php

	class blogItem{
		
		private $id = 0;
		private $typeFunction = '';
		
		function __construct($object){
			foreach($object as $key => $value){
				$this->$key = $value;
			}
		}
		
		function getHTML(){
			return $this->createBlogItem();
		}
		
		function getReactionsHTML(){
			
			$header = '<header class="reactionMain"><h1>Reacties</h1></header>';
			$content = '<span class="emtyMessage">Er zijn nog geen reacties aangemaakt voor dit item!</span>';
			
			if($this->countReactions() > 0){
				$content = $this->createReactions();
			}
			
			$form = $this->getReactionForm();
			
			return $header.$content.$form;
		}
		
		function getID(){
			return $this->id;
		}
		
		function getURL(){
			return $GLOBALS['url'].'/blog/item/'.$this->id.'/'.nice_url($this->title);
		}
		
		protected function createBlogItem(){
			return '
				<article>
  					<header>
  						<h1>'.$this->title.'</h1>
  					</header>
  					'.$this->resolveCreateFunction().'
  					'.$this->getReadOn().'
  					<footer>
						<ul class="info">
							<li>'.$this->author.'</li>
							<li>'.$this->date.'</li>
							<li>'.$this->time.'</li>
							<li><a href="'.$this->getURL().'">'.$this->countReactions().' reacties</a></li>
						</ul>
						<ul class="socialShit">
							<li>
								<a title="Deel dit item op Twitter" target="_blank" href="https://twitter.com/intent/tweet/?text=@OnsUrkerland: '.$this->title.' - '.$this->getURL().'">
									<img src="'.$GLOBALS['url'].'/style/image/blog/twitter.png" alt="Facebook">		
								</a>
							</li>
							<li>
								<a title="Deel dit item op Facebook" target="_blank" href="http://www.facebook.com/sharer.php?u='.$this->getURL().'">
									<img src="'.$GLOBALS['url'].'/style/image/blog/facebook.png" alt="Facebook">
								</a>
							</li>
							<li>
								<a title="Deel dit item op Google+" target="_blank" href="https://plus.google.com/share?url='.$this->getURL().'">
									<img src="'.$GLOBALS['url'].'/style/image/blog/google.png" alt="Google+">
								</a>
							</li>
						</ul>
					</footer>
  				</article>
			';
		}
		
		protected function getReadOn(){
			if($this->readon){
				return '<a href="'.$this->getURL().'" target="_blank">Lees verder...</a>';
			}
			else{
				return '';
			}
		}
		
		private function resolveCreateFunction(){
			if(method_exists($this, $this->typeFunction)){
				$function = $this->typeFunction;
				return $this->$function();
			}
			else{
				throw new Exception("Blog item type '$this->type' bestaat niet");
			}
		}
		
		protected function createDefaultBlogItem(){
			return '<p><img class="thumbnail" src="'.$GLOBALS['url'].'/getfile/'.$this->param.'">'.$this->content.'</p>';
		}
		
		protected function createBigPictureBlogItem(){
			return'
				<img class="blogImage" src="'.$GLOBALS['url'].'/getfile/'.$this->param.'">
				<p>'.$this->content.'</p>
			';
		}
		
		protected function createYoutubeBlogItem(){
			return '
				<iframe class="youtube" frameborder="0" allowfullscreen="" src="http://www.youtube.com/embed/'.$this->param.'" /></iframe>
				<p>'.$this->content.'</p>
			';
		}
		
		protected function createTweetBlogItem(){
			return '
				'.$this->param.'
				<p>'.$this->content.'</p>
			';
		}
		
		public function countReactions(){
			return $GLOBALS['db']->one("SELECT COUNT(*) FROM `web_blog_reactions` WHERE `blog_item` = :id", ':id', $this->id);
		}
		
		protected function createReactions(){
			
			include_once('reaction.php');
			$reactions = $GLOBALS['db']->query("
				SELECT 
					r.`content`,
					r.`judgement`,
					u.`username` AS `author`,
					DATE_FORMAT(r.`created`, '%d-%m-%Y') AS `date`, 
					DATE_FORMAT(r.`created`, '%H:%i') AS `time`
				FROM `web_blog_reactions` r
				INNER JOIN `mvc_users` u ON r.`author` = u.`id`
				WHERE `blog_item` = :id
			", ':id', $this->id);
			
			$reactionHTML = '';
			while($reaction = $reactions->fetch()){
				$reaction = new blogReaction($reaction);
				$reactionHTML .= $reaction->getHTML();
			}
			
			return $reactionHTML;
			
		}
		
		protected function getReactionForm(){
			
			$header = '<header class="reactionMain"><h1>Schrijf reactie</h1></header>';
			
			//todo: maak reactie formulier op
			if($_SESSION['user']){
				$form = '';
			}
			else{
				$form = '<article class="blogReactionForm"><p>Je bent niet ingelogt! <br> <a class="authorize">Log in</a> of <a href="'.$GLOBALS['url'].'/user/register">maak een account</a> aan.</p></article>';
			}
			
			return $header.$form;
			
		}
		
	}

?>