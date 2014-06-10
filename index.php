<?php
	
	$request = $_SERVER['REQUEST_URI'];
	list($request) = explode('?', $request, 2);
	$root = explode('/', $_SERVER['SCRIPT_NAME']);
	array_pop($root);
	$root = implode('/', $root);
	$request = str_replace($root, '', $request);
	
	$adres = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT']).$root;
	
	echo "
		$_SERVER[PHP_SELF] <br>
		$_SERVER[SERVER_NAME] <br>
		$_SERVER[QUERY_STRING] <br>
		$_SERVER[DOCUMENT_ROOT] <br>
		$_SERVER[REMOTE_ADDR] <br>
		$_SERVER[SCRIPT_FILENAME] <br>
		$_SERVER[SCRIPT_NAME] <br>
		$_SERVER[REQUEST_URI] <br>
		$root <br>
		$request <br>
		$adres <br>
	";
	
?>