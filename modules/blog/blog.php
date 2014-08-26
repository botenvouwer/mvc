<?php

	class blog extends blogMain{
		
		function main(){
			
			$blogItems = $this->db->query();
			
			/*
			
			SELECT
			    id,
			    CASE
			        WHEN len <= 500 THEN content
			        ELSE CASE
			            WHEN idx > 0 THEN SUBSTRING(content, 1, idx)
			            ELSE ''
			        END
			    END AS content
			FROM (
			  SELECT 
			    id,
			    content,
			    LOCATE('.', content, 500) AS idx,
			    LENGTH(content) AS len
			  FROM web_blog_items
			) AS data
			
			*/
		}
		
		function item($id = 0, $niceUrl = ''){
			
			echo "ja het werkt $id";
			
		}
		
		function react(){
			
			if(isset($_POST['reaction'])){
				
				
				
			}
			
		}
		
	}

?>