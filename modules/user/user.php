<?php

	class user extends userMain{
		
		function __construct(){
			
		}
		
		function main(){
			if(!$_SESSION['user']){
				$this->view->content = 'U bent niet ingelogd! U kunt <a href="'.$GLOBALS['url'].'/user/register">hier</a> een account aanmaken.';
			}
			else{
				$user = $this->db->query("SELECT `username`, `email`, `name`, `sirname` FROM `mvc_users` WHERE `id` = :id", ':id', $_SESSION['user']);
				$user = $user->fetch();
				$this->view->content = 'Ingelogd als: ';
				$this->view->content .= '<br>'.$user->username;
				$this->view->content .= '<br>'.$user->email;
			}
		}
		
		function login(){
			
			$returnTo = $GLOBALS['url'];
			if(isset($_COOKIE['returnURL'])){
				$returnTo = $_COOKIE['returnURL'];
			}
			
			if(isset($_REQUEST['login'])){
				//formulier valideren en inlogen
			}
			
			//formulier opmaken
			
			$this->view->content = 'login and return to '.$returnTo;
		}
		
		function passreset($arg = array()){
			
			if(count($arg)){
				
			}
			else{
				
			}
			
		}
		
		function activate($arg = array()){
			
			if(count($arg)){
				
			}
			else{
				
			}
		}
		
		function register(){
			$this->view->content = 'Registreer';
		}
		
	}

?>