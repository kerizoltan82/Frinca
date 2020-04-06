<?php

/*
 code for determining who is the winner, from $game var

 contains some logging 

*/

require_once('debug.php');

function CheckWin()
{
    global $game;
    // get number of empty regions
    $emptyregions = 0;
    for($i=0; $i<$game['Num_Regions']; $i++) 
    {
        $regionfinca = $game["Board_Region_Finca_$i"];
        if( $regionfinca < 0 ) {
            $emptyregions++;
        }
    }
    $gameended = false;
    if( $emptyregions >= $game['EndNumFincas'] ) {
        $gameended = true;
    }
    if( $gameended ) {
        // determine who has won.
        $winplayer = WhoHasMostPoints();
        if( $winplayer==-1) {
            // special rules
            logs( 'CheckWin: players have the same points');
        } else {
            logs( "CheckWin: WINNER: $winplayer ");
        }
        return $winplayer;
    } 
    return -1;
}

function WhoHasMostPoints()
{
    global $game;
    $points = array(0,0,0,0,0,0,0);//7, questionmark is also a fruit type here
    //go thru all players
    for($p=0; $p<$game['Num_Players']; $p++) {
        $points[$p] = GetPlayerPoints($p);
    }
    logs( 'WINRULES_POINTS:');
    logvar($points);
    // now determine who has most
    $max = 0;
    $maxindex = 0;
    for($p=0; $p<$game['Num_Players']; $p++) {
        if( $points[$p] == $max) {
            $maxindex = -1; // nobody has most, because two have identical number of points (two winners?)
            continue;
        }
        if( $points[$p] > $max) {
            $max = $points[$p];
            $maxindex = $p;
        }
    }
    return $maxindex;
}

function GetPlayerPoints($player) 
{
    global $game;
    // delivered fruit cards
    $points = GetTotalDeliveredFruits($player);
    
    //fincas count as 5
    $points = $points + 5 * $game["Player_Resource_Fincas_$player"];
    
    // bonuses
    for($i=0; $i< $game["Player_Resource_Bonuses_$player"]; $i++) {
        $points = $points + $game["Player_Resource_Bonuses_$player".'_'.$i];
    }
    
    //remaining action cards each for 2 points
    $points = $points + 2 * $game["Player_Resource_Actioncards_$player"] ;
    
    return $points;
}


function GetTotalDeliveredFruits($player)
{
    global $game;
    global $FruitCards;
    $totalfruits = 0;
    $numcards = $game["Player_Resource_FruitCards_$player"];
    for($f=0; $f<$numcards; $f++) {
        $fruitcardindex = $game["Player_Resource_FruitCards_$player".'_'.$f];
        $fruitsincard = count( $FruitCards[$fruitcardindex] );
        $totalfruits = $totalfruits + $fruitsincard;
    }
    return $totalfruits;
}


?>