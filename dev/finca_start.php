<?php
/*

this script is the game starter

Used Modules:
LoadSaveGame
StartRules
GraphicalEngine

*/

require_once('LoadSaveGame.php');
require_once('StartRules.php');
require_once('GraphicalEngine.php');
require_once('PlayerDB.php');
require_once('scriptnames.php');
require_once('debug.php');

session_start();

// COPY FROM frinca.php
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
		logs('reg_check: '.$_POST['register_check'] );
		if (isSet($_POST['register_check']) && $_POST['register_check'] == true ) {
			
			$ret = AddNewPlayer($curplayername );
			if(  $ret == false ) {
				GraphicalEngine_AskLogin_Page('Regisztrációs hiba: ilyen nevű játékos már van az adatbázisban.');
				exit(0);
			}
		}
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

// get game

if( isSet( $_POST['inp_name'] ))
{
    $Current_Game = $_POST['inp_name']; //get new identifier
    $gamename = $_POST['inp_name'];
} else {   
    //error, we have no game
    GraphicalEngine_Error('game name is not given.');
    exit(0);
}

$_SESSION['gameid'] = $Current_Game;

//get number of players
if( isSet( $_POST['inp_numplayers'] ))
{
    $numplayers = $_POST['inp_numplayers'];
} else {   
    //error, this param is not given
    GraphicalEngine_Error('number of players is not given.');
    exit(0);
}

$game = array();

$game['Game_Name'] = $gamename;
$game['Num_Players'] = $numplayers;

for($i=0; $i<$numplayers; $i++) {
	//logs('inp player '.$_POST['inp_player'.$i]);
	//logs('selection'.$_POST['player_name_select_'.$i]);
    $game['Name_Player_'.$i] = $_POST['player_name_select_'.$i];
    $game['Player_Color_Index_'.$i] = $_POST['inp_color'.$i];
}

//start and save the game.
StartGame($game);

SaveGame();

// draw windmill background once only
CreateWindmill($gamename);

// PRINT html
print ("<html>");
printHeaderEx('Játékkezdés', 'game_start.css', '', '');
print ("<body>");

print ("<div class=\"main_panel\">");
$gamemainscript = $scriptnames['game_main'];
print ("<br/><br/><br/><a href=\"$gamemainscript\">Tovább a játékhoz</a>");
print ("</div>");

?>

</body>
</html>