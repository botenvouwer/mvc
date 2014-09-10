<?php

	class blogReaction{
		
		private $id = 0;
		
		function __construct($object){
			foreach($object as $key => $value){
				$this->$key = $value;
			}
		}
		
		function getID(){
			return $this->id;
		}
		
		function getHTML(){
			return $this->createReaction();
		}
		
		protected function createReaction(){
			return '
				<article class="blogReaction">
  					'.$this->resolveJudgement().'
  					<footer>
						<ul class="info">
							<li>'.$this->author.'</li>
							<li>'.$this->date.'</li>
							<li>'.$this->time.'</li>
						</ul>
						<ul class="meld">
							<li><a href="" title="Meld deze reactie omdat hij in strijd is met onze huisregels.'."\n".'Misbruik van deze functie leid tot perma ban.">Goddeloos!</a></li>
						</ul>
					</footer>
  				</article>
			';
		}
		
		protected function resolveJudgement(){
			
			if($this->judgement == 1){
				return $this->content;
			}
			else if($this->judgement == 2){
				return 'Vortuwarruk!';
			}
			else if($this->judgement == 3){
				return 'Vortuwarruk en opgerot!';
			}
			else{
				return 'Reactie verwijdert!';
			}
			
		}
		
	}

?>