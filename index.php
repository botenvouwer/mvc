<?php
	
	/* ~index.php - MicroBoatMVC
	
		Version 0.0.4
	
	*/
	
	session_start();
	
	$request = $_SERVER['REQUEST_URI'];
	list($request) = explode('?', $request, 2);
	$root = explode('/', $_SERVER['SCRIPT_NAME']);
	array_pop($root);
	$root = implode('/', $root);
	$request = str_replace($root, '', $request);
	$request = trim($request, '/');
	
	$adres = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT']).$root;
	$url = $adres;
	$root = $_SERVER['DOCUMENT_ROOT'].$root;
	$conf = parse_ini_file($root.'/conf.ini');
	date_default_timezone_set($conf['timezone']);
	
	if($conf['debug']){
		error_reporting(E_ALL);
	}
	else{
		error_reporting(0);
	}
	
	foreach(glob($root.'/engine/library/*.php') as $path){
		require_once($path);
	}
	
	$ajax = false;
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
	   $ajax = true;
	}
	
	$db = new microBoatDB($conf['db_adres'],$conf['database'],$conf['db_user'],$conf['db_pass']);
	
	if(!isset($_SESSION['user'])){
		$_SESSION['user'] = 0;
	}
	
	$urlAction = $db->one("SELECT `action` FROM `web_url` WHERE `url` = :req", ':req', $request);
	
	if($urlAction){
		$param = $request;
		$request = $urlAction;
	}
	
	$requestPart = trim($request, '/');
	$requestPart = explode('/', $requestPart);
	$num = count($requestPart);
	
	if(isset($requestPart[0])){
		if(!$requestPart[0]){
			unset($requestPart[0]);
		}
	}
	
	$nameSpace = '';
	$action = '';
	$subaction = '';
	
	//namespace opmaken
	$nameSpace = 'default';
	
	$nameSpaces = array_filter(glob($root.'/namespaces/*'), 'is_dir');
	$nameSpaces = array_map('getLastPartOfString', $nameSpaces);
	
	if(isset($requestPart[0])){
		if(in_array($requestPart[0], $nameSpaces)){
			$nameSpace = $requestPart[0];
			unset($requestPart[0]);
			$requestPart = array_values($requestPart);
		}
	}
	
	$nameSpaceConf = parseJsonFile("$root/namespaces/$nameSpace/conf.json");
	
	//action opmaken
	$action = $nameSpaceConf['action'];
	$view = $nameSpaceConf['view'];
	
	if(isset($requestPart[0])){
		if(class_exists($requestPart[0])){
			pageNotFound();
		}
		$action = $requestPart[0];
		unset($requestPart[0]);
		$requestPart = array_values($requestPart);
	}
	else{
		if(class_exists($action)){
			error('Default nameSpace action is an existing php class');
		}
	}
	
	$actionFound = false;
	if(file_exists("$root/modules/$nameSpace/")){
		
		if(file_exists("$root/modules/$nameSpace/main.php")){
			include_once("$root/modules/$nameSpace/main.php");
		}
		
		foreach(glob("$root/modules/$nameSpace/*.php") as $path){
			include_once($path);
			if(class_exists($action)){
				$actionFound = true;
				break;
			}
		}
	}
	
	if(!$actionFound){
		$modules = array_filter(glob($root.'/modules/*'), 'is_dir');
		foreach($modules as $module){
			
			$modParts = glob($module.($nameSpace != 'default' ? "/$nameSpace" : '').'/*.php');
			if($nameSpace == 'default'){
				if($key = array_search("$module/main.php", $modParts)){
					include_once($modParts[$key]);
					unset($modParts[$key]);
				}
			}
			
			foreach($modParts as $path){
				include_once($path);
				
				if(class_exists($action)){
					$actionFound = true;
					break 2;
				}
			}
		}
	}
	
	if(!$actionFound){
		pageNotFound();
	}
	
	//subaction opmaken
	$mainMethodExists = method_exists($action, $nameSpaceConf['subaction']);
	
	$requestMethodExists = false;
	if(isset($requestPart[0])){
		$requestMethodExists = method_exists($action, $requestPart[0]);
	}
	
	$subactionFound = false;
	if($requestMethodExists){
		$subaction = $requestPart[0];
		$subactionFound = true;
		unset($requestPart[0]);
		$requestPart = array_values($requestPart);
	}
	else if($mainMethodExists){
		$subaction = $nameSpaceConf['subaction'];
		$subactionFound = true;
	}
	
	if(!$subactionFound){
		pageNotFound();
	}
	
	//parameters opmaken
	$parameters = $requestPart;
	
	$param = null;
	if($paramCount = count($parameters) >= 1){
		$param = $parameters[0];
	}
	
	if(!userHasRight($request)){
		if($ajax){
			//stuur ajax foutmelding
		}
		else{
			$nameSpace = 'authentication';
			//laad autenticatie part
		}
	}
	
	//todo: filter op standaart object namen die men nooit kan aanroepen
	
	$actionInstance = new $action();
	$actionInstance->view = false;
	
	//view opmaken
	if(!$ajax){
		if($actionInstance->view){
			setView($actionInstance->view);
		}
		else{
			//todo: haal standaart view op of haal view op uit module
		}
	}
	
	//todo: classe methode analyseren //- parameters valideren
	
	//todo: objecten toevoegen -! nakijken
	$actionInstance->param = $param;
	$actionInstance->db = $db;
	$actionInstance->url = $url;
	$actionInstance->adres = $adres;
	$actionInstance->root = $root;
	$actionInstance->conf = $conf;
	$actionInstance->root = $root;
	
	//todo: methode aantroepen met prarameters -! nakijken
	call_user_func_array(array($actionInstance, $subaction), $parameters);
	
	if($actionInstance->view){
		$actionInstance->view->send();
	}
	
?>