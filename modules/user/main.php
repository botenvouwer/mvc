<?php

	class userMain{
		
		function __construct(){
			$this->db = $GLOBALS['db'];
		}
		
		public function userExist($id){
			return $this->db->one("SELECT EXISTS(SELECT * FROM `mvc_users` WHERE `id` = :id)", ':id', $id);
		}
		
	}

?>