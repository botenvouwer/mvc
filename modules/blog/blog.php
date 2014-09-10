<?php

	class blog extends blogMain{
		
		function main($step = 1){
			
			$count = $this->countPublishedItems();
			
			if($count == 0){
				$this->view->content = '<span class="emtyMessage">Geen blog items gevonden</span>';
				return;
			}
			
			$stepSize = 10;
			$maxSteps = ceil($count / $stepSize);
			$stepLocation = ($step - 1) * $stepSize;
			$pre = $step -1;
			$next = $step +1;
			
			$blogItems = $this->db->query("
				SELECT 	i.`id`, 
						i.`title`, 
						i.`pre_content` AS `content`, 
						i.`param`,
						IF(i.`content` = '', 0, 1) as `readon`, 
						a.`username`  AS `author`,
						DATE_FORMAT(i.`publish_date`, '%d-%m-%Y') AS `date`, 
						DATE_FORMAT(i.`publish_date`, '%H:%i') AS `time`,
						u.`username` AS `updated_by`,
						DATE_FORMAT(i.`updated`, '%d-%m-%Y %H:%i') AS `updated`,
						st.`name` AS `type`,
						st.`function` AS `typeFunction`
				FROM `web_blog_items` i
				INNER JOIN `web_blog_show_types` st ON i.`show_type` = st.`id`
				INNER JOIN `mvc_users` a ON i.`author` = a.`id`
				INNER JOIN `mvc_users` u ON i.`updated_by` = u.`id`
				WHERE i.`publish_date` < NOW()
				ORDER BY i.`publish_date` DESC
				LIMIT $stepLocation, $stepSize
			");
			
			if($pre > 0){
					$pre = "<div><a href='$this->url/blog/$pre' >Vorige</a></div>";
			}
			else{
				$pre = '';
			}
			
			if($next <= $maxSteps){
				$next = "<div><a href='$this->url/blog/$next' >Volgende</a></div>";
			}
			else{
				$next = '';
			}
			
			$navigation = '';
			if($next || $pre){
				$navigation = '<div id="blogNavigation">'.$pre.$next.'</div>';
			}
			
			$items = '';
			while($blogItem = $blogItems->fetch()){
				$item = new blogItem($blogItem);
				$items .= $item->getHTML();
			}
			
			
			$this->view->content = $items.$navigation;
		}
		
		function item($id = 0, $niceUrl = ''){
			
			if($this->itemExist($id)){
				$blogItem = $this->db->query("
					SELECT 	i.`id`, 
					        i.`title`, 
					        i.`pre_content` AS `content`,
					        i.`content` AS `next_content`,
					        i.`param`,
					        IF(i.`content` = '', 0, 0) as `readon`, 
					        a.`username`  AS `author`,
					        DATE_FORMAT(i.`publish_date`, '%d-%m-%Y') AS `date`, 
					        DATE_FORMAT(i.`publish_date`, '%H:%i') AS `time`,
					        u.`username` AS `updated_by`,
					        DATE_FORMAT(i.`updated`, '%d-%m-%Y %H:%i') AS `updated`,
					        st.`name` AS `type`,
					        st.`function` AS `typeFunction`
					FROM `web_blog_items` i
					INNER JOIN `web_blog_show_types` st ON i.`show_type` = st.`id`
					INNER JOIN `mvc_users` a ON i.`author` = a.`id`
					INNER JOIN `mvc_users` u ON i.`updated_by` = u.`id`
					WHERE i.`publish_date` < NOW() AND i.`id` = :id
				", ':id', $id);
				
				$blogItem = new blogItem($blogItem->fetch());
				
				if($niceUrl != nice_url($blogItem->title)){
					redirect($blogItem->getURL());
				}
				
				$this->view->title = $blogItem->title;
				$this->view->content = $blogItem->getHTML() . $blogItem->getReactionsHTML();
				
			}
			else{
				$this->view->content = '<span class="emtyMessage">Blog item niet gevonden!</span>';
			}
			
		}
		
		function reaction(){
			
			if(isset($_POST['reaction'])){
				
				
				
			}
			
		}
		
	}

?>