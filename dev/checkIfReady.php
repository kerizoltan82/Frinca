<?php
/*
 This should be called via ajax from the client (browser) to check if the other player has moved already.
 it returns the strings "update" or "none" (or some error).
 
 attention: all files included must not have any spaces!!
 (as html content, before or after php sign whatever)
*/

require_once('LoadSaveGame.php');
require_once('debug.php');


error_reporting (E_ALL);

session_start();


// player is logged in?
if( isSet($_SESSION['playerid']) )
{
    //$curplayername = $_SESSION['player'];
}
else
{
    print "error_player_login";
    exit(0);
}

//get game from post param
if( isSet( $_POST['game'] )) {
    $Current_Game = $_POST['game'];
} else {
    print "error_game_id";
    exit(0);
}

if( isSet( $_POST['expected'] )) {
    $expected_player = $_POST['expected'];
} else {
    print "error_expected";
    exit(0);
}


$game = array();
LoadGame( $Current_Game );

// if other player than now, or if someone has won
// (if someone won, it will be only once update, then the checking will stop)
if( ($expected_player != $game["Current_Player"]) || ($game['Winner'] > -1) ) {
    
    print "update";
    //print " $expected_player ".$game["Current_Player"];
} else {
    print "none";
    //print " $expected_player ".$game["Current_Player"];
}

?>