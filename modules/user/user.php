<?php

	class user{
		
		function main(){
			echo 'userProfile';
		}
		
		function login($request){
			$this->view->content = 'test login and redirect to-> '. $request;
		}
		
	}

?>