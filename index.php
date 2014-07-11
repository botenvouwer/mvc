<?php
	
	$request = $_SERVER['REQUEST_URI'];
	list($request) = explode('?', $request, 2);
	$root = explode('/', $_SERVER['SCRIPT_NAME']);
	array_pop($root);
	$root = implode('/', $root);
	//todo manier om deze urls te makern zo kort mogelijk maken
	$request = str_replace($root, '', $request);
	$adres = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT']).$root;
	$root = $_SERVER['DOCUMENT_ROOT'].$root;
	
	echo "
		<table>
			<tr>
				<td>ACTION:</td>
				<td>$request</td>
			</tr>
			<tr>
				<td>ADRES:</td>
				<td>$adres</td>
			</tr>
			<tr>
				<td>ROOT:</td>
				<td>$root</td>
			</tr>
		</table>
	";
	
	//standaart variablen instellen
	
	//gebruiker sessie cheken en of sessie aanmaken
	
	//instellingen laden
	
	//db object maken
	
	//db instellingen laden
	
	//db gebruiker instellingen laden
	
	//ontvangen request exploden naar losse actions
	
	//actions ontleden en analyzeren controleren of gebruiker rechten heeft en toegang
		//controleren of actie voorkomt in db en db actie ophalen
		//contoleren of opgevraagde functie niet al bestaad als deze al bestaad foutmelding
		//alle controlers laden tot gewenste controler is gevonden
		//rechten contole kijk of gebruiker recht heeft op functie
		//controleren of actie een eigen style template heeft en deze ophalen
	
	if(true){
		//action uitvoeren	
	}
	else{
		//foutmelding geven
	}
	
	
	
?>