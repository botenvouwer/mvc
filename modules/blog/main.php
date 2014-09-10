<?php

	class blogMain{
		
		function __construct(){
			$this->db = $GLOBALS['db'];
			include_once('item.php');
		}
		
		protected function itemExist($id){
			return $this->db->one("SELECT EXISTS(SELECT * FROM `web_blog_items` WHERE `id` = :id)", ':id', $id);
		}
		
		protected function createNewReaction($id, $reaction){
			
			$this->db->query("INSERT (`id` = NULL, `blog_item` = :id, ) INTO `web_blog_reactions`");
			
		}
		
		protected function countPublishedItems(){
			return $this->db->one("SELECT COUNT(*) FROM `web_blog_items` WHERE `publish_date` < NOW()");
		}
		
	}

?>