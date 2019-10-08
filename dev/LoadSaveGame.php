<?php

require_once('debug.php');
//require_once('Tools.php');

// returns an array with the files found in the game dierctory.
// backups are excluded.
function GetUnfinishedGameList()
{
    $allgames =  GetGameList('games');
    // filter out finished
    for($i=0; $i<count($allgames); $i++) {
        // open game
    }
    // TODO: filter
    // TODO: move finished games to an "archived" folder.
    return $allgames;
}

function GetArchivedGameList()
{
    $allgames =  GetGameList('archive_games');
    // filter out finished
    for($i=0; $i<count($allgames); $i++) {
        // open game
    }
    // TODO: filter
    // TODO: move finished games to an "archived" folder.
    return $allgames;
}

// checks if there are finished unarchived games and archives them if necessary.
// also removes the backup file since it is not neceassry anymore.
function CheckArchivedGames() 
{
    global $game;
    $dir = 'games/';
    $allgames = GetUnfinishedGameList();
    $archivegames = array();
    // now check
    for($i=0; $i<count($allgames); $i++) {
        LoadGame($allgames[$i]);
        if( $game['Winner'] > -1 ) {
            $archivegames[] = $allgames[$i];
        }
    }
    // now archive those
    for($i=0; $i<count($archivegames); $i++) {
        //move to archived
        rename('games/game_'.$archivegames[$i].'.txt', 'archive_games/game_'.$archivegames[$i].'.txt');
        // delete backup file
        unlink('games/game_'.$archivegames[$i].'_backup.txt');
    }
    
}

// returns an array with the files found in the game dierctory.
// backups are excluded.
function GetGameList($dir)
{
    $dir .= '/';
    $ret = array();
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if( $file != '.' && $file != '..' ) {
                $r = substr($file, 5, -4) ;
                $backupgame = strpos($file, 'backup');
                if( $r != '' && $backupgame === false)
                    $ret[] =  $r;
            }
        }
        closedir($dh);
    }
    return $ret;
}


function BackupGame($gameid) {

    $fname = 'games/game_'.$gameid.'.txt';
    copy($fname, 'games/game_'.$gameid.'_backup.txt');
    logs( "LoadSavegame:backupgame");
}

function RestoreGame($gameid) {
    if( !file_exists('games/game_'.$gameid.'_backup.txt') ) {
        return false;
    }
    $fname = 'games/game_'.$gameid.'.txt';
    copy('games/game_'.$gameid.'_backup.txt', $fname);
    return true;
}

function LoadGame($gameid)
{
    global $game;
    $fname = 'games/game_'.$gameid.'.txt';
    // if we did not find it, then look into archived games.
    if( !file_exists($fname) ) {
        $fname = 'archive_games/game_'.$gameid.'.txt';
    }
    $lines = file($fname, FILE_IGNORE_NEW_LINES); 
    foreach($lines as $line) 
    {
        $la = explode("=", $line) ;
        $game[ $la[0] ] = $la[1] ;
    }
}

function SaveGame()
{
    global $game;
    $gameid = $game['Game_Name'];
    $fname = 'games/game_'.$gameid.'.txt';
        
    $lines = array();
    $l=0;
    
    // save file
    foreach ($game as $key => $value) {
        $lines[$l++] = "$key=$value";
    }
    $all = implode("\n", $lines);
    file_put_contents($fname, $all);
    logs( "LoadSavegame: saveame");
}

?>