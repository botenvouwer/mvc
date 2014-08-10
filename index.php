<?php
	
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
			
			foreach(glob($module.($nameSpace != 'default' ? $nameSpace : '').'/*.php') as $path){
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
	if(count($parameters) >= 1){
		$param = $parameters[0];
	}
	
	//actions ontleden en analyzeren controleren of gebruiker rechten heeft en toegang
		//controleren of actie voorkomt in db en db actie ophalen
		//contoleren of opgevraagde functie niet al bestaad als deze al bestaad foutmelding
		//alle controlers laden tot gewenste controler is gevonden
		//rechten contole kijk of gebruiker recht heeft op functie
		//controleren of actie een eigen style template heeft en deze ophalen
	
	if(!userHasRight($request)){
		$nameSpace = 'authentication';
		//Haal instellingen van autenticatie namespace op en valideer deze opnieuw
	}
	
	//voer actie uit
	echo printRequest();
	
?>