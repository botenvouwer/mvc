<?php

	class user extends userMain{
		
		function __construct(){
			
		}
		
		function main(){
			if(!$_SESSION['user']){
				$this->view->content = 'U bent niet ingelogd! U kunt hier een account aanmaken.';
			}
			else{
				$user = $this->db->query("SELECT `username`, `email`, `name`, `sirname` FROM `mvc_users` WHERE `id` = :id", ':id', $_SESSION['user']);
				$user = $user->fetch();
				$this->view->content = 'Ingelogd als: ';
				$this->view->content .= '<br>'.$user->username;
				$this->view->content .= '<br>'.$user->email;
			}
		}
		
		function login($request){
			$this->view->content = 'test login and redirect to-> '. $request;
		}
		
		function register(){
			$this->view->content = 'Registreer';
		}
		
	}

?>