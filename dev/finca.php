<?php

/*

Used Modules:
LoadSaveGame
MainRules
GraphicalEngine
FruitCards

Used variables:
Current_Player
Current_Game
Current_Move

GraphicalEngine_ErrorMove
GraphicalEngine_AskLogin_Page
GraphicalEngine_Display

*/

require_once('LoadSaveGame.php');
require_once('PlayerDB.php');

require_once('MainRules.php');
require_once('GraphicalEngine.php');
require_once('Decor.php');
require_once('FruitCards.php');
require_once('debug.php');

error_reporting (E_ALL);

session_start();

GetDecorations();

// logout required?
if( isSet( $_GET['logout'] ))
{
    if( $_GET['logout'] == 'game' ) {
        //logout only from game - forget game
        unset( $_SESSION['gameid'] );
    } else {
        // logout from player
        session_destroy();
        GraphicalEngine_LogOut();
        exit(0);
    }    
}

// player is logged in?
if( isSet($_SESSION['playerid']) )
{
    $curplayer = $_SESSION['playerid'];
    $curplayername = GetPlayerNameFromDB($curplayer);
}
else
{
    // see if it is params
    if (isSet($_POST['player'] ))
    {
        // when POST player is set, the name is given, not id.
        $curplayername = $_POST['player'];
        $curplayer = GetPlayerIDFromDB($curplayername);
        // check login error.
        if(  $curplayer == -3 ) {
            GraphicalEngine_AskLogin_Page('Hiba: ilyen nevű játékos még nincs az adatbázisban.');
            exit(0);
        }
        $_SESSION['playerid'] = $curplayer; // $_POST['player'];
    }
    else
    {
        // player is not set, require login
        GraphicalEngine_AskLogin_Page('');
        exit(0);
    }
}

// get the player ID from the name.
//$curplayer = GetPlayerFromDB($curplayername); //if 'multi', thenreturn -1.

// get game
$Current_Game = '';
if( isSet( $_SESSION['gameid'] ))
{
    $Current_Game = $_SESSION['gameid'];
}
else
{
    if( isSet( $_POST['game'] ))
    {
        $Current_Game = $_POST['game'];
        $_SESSION['gameid'] = $Current_Game;
    }
}
if($Current_Game == '')
{   
    // we have no game
    if( isset($_POST['archivedgames'] )) {
        $archived = ($_POST['archivedgames'] == 1);
    } else {
        $archived = false;
    }
    GraphicalEngine_StartOrResumeGame($curplayer, $curplayername,  $archived );
    exit(0);
}

// init graphics
FillFruitCards();
FillFincaCards();

// main game variable
$game = array();

// handle undo
if( isSet( $_POST['undo'] ))
{
    $ret = RestoreGame($Current_Game);
    if($ret) {
        logs( 'game restored to previous!');
    } else {    
        logs( 'game could not be restored.');
    }
}

// load the game from disk
LoadGame( $Current_Game );

// get player move if any
if( isSet( $_POST['move'] ))
{
    if( $_POST['move'] != '' ) {
        logs( 'player move');
        //$Current_Move = $_POST['move'];
        $Current_Move = array();
        $Current_Move['Player_Index'] = $game['Current_Player'];
        GetMoveParametersFromPhp( $Current_Move ); 
        // save backup
        BackupGame($Current_Game);
        $ret = ExecMove( $Current_Move );
        if( !$ret ) {
            // a move error has occured
            $err = $game['Last_Move_Error'];
            RestoreGame($Current_Game);
            LoadGame( $Current_Game );
            $game['Last_Move_Error'] = $err;
        } else {
            SaveGame();
        }
    }
}

// displays boards+players
GraphicalEngine_Display($curplayer, $curplayername, $Current_Game);


function GetMoveParametersFromPhp(&$TheMove)
{
    global $game;
    if( isSet( $_POST['move'] )) {
        $TheMove['Move_Type'] =  $_POST['move'];
    }
    
    if($TheMove['Move_Type'] == 'Place_Farmer') {
        $TheMove['Windmill_Blade'] = $_POST['moveparam1'];
    }
    if($TheMove['Move_Type'] == 'Move_Farmer') {
        //in move param, the blade is given,. but we need the farmer index.
        $TheMove['Farmer_Index'] = BladePosToFarmerIndex($game['Current_Player'], $_POST['moveparam1'] );
        // actioncard 0 'double move'
        $TheMove['ActionCard'] = $_POST['actionparam1'] ;
    }
    if($TheMove['Move_Type'] == 'Deliver') {
        //delivered regions array
        $TheMove['Regions'] = explode( ',', $_POST['moveparam1'] );
        // questionmarks deliver (moveparam2 must be given, the fruit indexes)
        $TheMove['Questionmarks'] = explode( ',', $_POST['moveparam2'] );
        //  actioncard 3 '-1' and actioncard 2 '10'
        $TheMove['ActionCard'] = $_POST['actionparam1'] ;
        $TheMove['ActionCard_Param1'] = $_POST['actionparam2'] ;
    }
    
    //actioncards
    if($TheMove['Move_Type'] == 'ActionCard1') {
        //'get where i want'
        $TheMove['Farmer_Index'] =  BladePosToFarmerIndex($game['Current_Player'], $_POST['actionparam1'] ) ;
        $TheMove['To_Blade'] =  $_POST['actionparam2'] ;
    }
}

?>