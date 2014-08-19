<?php
	
	/* ~index.php - MicroBoatMVC
	
		Version 0.0.8
	
	*/
	
	//Basic configuration
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
	$conf = parse_ini_file($root.'/conf/conf.ini');
	date_default_timezone_set($conf['timezone']);
	
	//error reporting and debug mode configuration
	if($conf['debug']){
		error_reporting(E_ALL);
	}
	else{
		error_reporting(0);
	}
	
	//load standard library files
	foreach(glob($root.'/engine/library/*.php') as $path){
		require_once($path);
	}
	
	//check if request is an ajax request
	$ajax = false;
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
	   $ajax = true;
	}
	
	//initiate database class
	$db = new microBoatDB($conf['db_adres'],$conf['database'],$conf['db_user'],$conf['db_pass']);
	
	//initiate user session
	if(!isset($_SESSION['user'])){
		$_SESSION['user'] = 0;
	}
	
	//url lookup in database (if found the namespace, action and subaction will be specified by the web_url table)
	$urlAction = $db->one("SELECT `action` FROM `web_url` WHERE `url` = :req", ':req', $request);
	
	if($urlAction){
		$urlAction = trim($urlAction, '/');
		$urlID = $db->one("SELECT `id` FROM `web_url` WHERE `url` = :req", ':req', $request);
		$requestPart = explode('/', $urlAction);
		$requestPart[] = $urlID;
	}
	else{
		$requestPart = explode('/', $request);
	}
	
	//parse url to: namespace, action, subaction and optional parameters
	$num = count($requestPart);
	if(isset($requestPart[0])){
		if(!$requestPart[0]){
			unset($requestPart[0]);
		}
	}
	
	$nameSpace = '';
	$action = '';
	$subaction = '';
	
	//namespace
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
	
	if(is_file($path = "$root/namespaces/$nameSpace/conf.json")){
		$nameSpaceConf = parseJsonFile($path);
		
		if(!isset($nameSpaceConf['action'])){
			trigger_error('MVC error: no default action set in nameSpace conf.json', E_USER_ERROR);
		}
		
		if(!isset($nameSpaceConf['subaction'])){
			trigger_error('MVC error: no default subaction set in nameSpace conf.json', E_USER_ERROR);
		}
	}
	else{
		trigger_error('MVC error: no nameSpace conf.json file set at "'.$path.'"', E_USER_ERROR);
	}
	
	//action
	$action = $nameSpaceConf['action'];
	
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
			trigger_error('MVC error: Default nameSpace action is an existing php class', E_USER_ERROR);
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
	
	//set moduleConf if module conf exists
	$moduleConf = false;
	if(is_file($path = "$root/modules/$action/conf.json")){
		$moduleConf = parseJsonFile($path);
	}
	
	//subaction
	$mainMethodExists = method_exists($action, $nameSpaceConf['subaction']);
	
	$requestMethodExists = false;
	if(isset($requestPart[0])){
		if($requestPart[0] === 'main'){
			pageNotFound();
		}
		else{
			$requestMethodExists = method_exists($action, $requestPart[0]);
		}
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
	
	$requestedAction = new ReflectionMethod($action, $subaction);
	
	if(!$requestedAction->isPublic()){
		pageNotFound();
	}
	
	//analyze parameters of method and compare them with the given parameters from the request
	$parameters = $requestPart;
	
	$paramCount = count($parameters);
	$requiredParametersCount = $requestedAction->getNumberOfRequiredParameters();
	$allParametersCount = $requestedAction->getNumberOfParameters();
	$paramReflection = $requestedAction->getParameters();
	
	if($allParametersCount == 1){
		
		if(is_array($paramReflection[0]->getDefaultValue()) && $paramCount > 0){
			$paramCount = 1;
			$parameters = array($parameters);
		}
		else if(is_string($paramReflection[0]->getDefaultValue()) && $paramCount > 0){
			$paramCount = 1;
			$parameters = array(implode('/',$parameters));
		}
		
	}
	
	if($requiredParametersCount > 0 && $requiredParametersCount != $paramCount){
		pageNotFound();
	}
	
	if($allParametersCount > 0 && $allParametersCount < $paramCount){
		pageNotFound();
	}
	
	
	//check if user has rights to use requested actions
	if(!userHasRight($request)){
		if($ajax){
			//stuur ajax foutmelding
		}
		else{
			$nameSpace = 'authentication';
			//laad autenticatie part
		}
	}
	
	$actionInstance = new $action();
	
	//find view for the request
	if(!$ajax){
		
		if(isset($actionInstance->view)){
			//set view from class
			if($actionInstance->view){
				setView($actionInstance->view);
			}
		}
		else{	
			if(isset($moduleConf['view'])){
				//set view from module conf
				setView($moduleConf['view']);
			}
			else{
				//set view from namaspace conf
				if(isset($nameSpaceConf['view'])){
					setView($nameSpaceConf['view']);
				}
			}
		}
	}
	
	$actionInstance->db = $db;
	$actionInstance->url = $url;
	$actionInstance->adres = $adres;
	$actionInstance->root = $root;
	$actionInstance->conf = $conf;
	$actionInstance->moduleConf = $moduleConf;
	$actionInstance->nameSpaceConf = $nameSpaceConf;
	
	call_user_func_array(array($actionInstance, $subaction), $parameters);
	
	if($actionInstance->view){
		$actionInstance->view->send();
	}
	
?>