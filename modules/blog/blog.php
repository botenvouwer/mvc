<?php

	class blog extends blogMain{
		
		function main(){
			echo "hier komt blogje";
			echo printRequest();
		}
		
		function item($id = 0, $niceUrl = ''){
			
			echo "ja het werkt $id";
			
		}
		
	}

?>