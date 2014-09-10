<?php

	class user{
		
		function __construct(){
			
		}
		
		function main(){
			$user = $this->db->query("SELECT `username`, `email`, `name`, `sirname` FROM `mvc_users` WHERE `id` = :id", ':id', $this->id);
			$this->view->content = 'Ingelogd als';
		}
		
		function login($request){
			$this->view->content = 'test login and redirect to-> '. $request;
		}
		
	}

?>