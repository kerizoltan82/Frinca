<?php

// TODO: array, and passwords

function GetPlayers() {
	$fname = 'players.txt';
	$lines = file($fname, FILE_IGNORE_NEW_LINES); 
	return $lines;
}

function AddNewPlayer($name) {
	$players = GetPlayers();
	if(in_array($name, $players) ) {
		return false;
	}
	
	$fh = fopen('players.txt', 'a+');
    fwrite($fh, $name."\n");
    fclose($fh);
	return true;
}


function GetPlayerIDFromDB($name) 
{
    if( $name == 'guest' ) return -2;
    if( $name == 'multi' ) return -1;
	
	$players = GetPlayers();
	$ret = array_search($name, $players);
	// avoid bad practice false = 0
	if($ret === false) {
		return -3;
	}
	return $ret;
	
}

function GetPlayerNameFromDB($id) 
{
	
    if( $id == -2 ) return 'guest';
    if( $id == -1 ) return 'multi';
	$players = GetPlayers();
	return $players[$id];

    return '';
}


?>