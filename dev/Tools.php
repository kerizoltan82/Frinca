<?php

require_once('debug.php');

function assert_move($condition, $message)
{
    global $game;
    if( !$condition ) { 
        $game['Last_Move_Error'] = $message;
        logs('Assert failed: '.$message);
        return true;
    }
    return false;
}

function RemoveActioncardFromPlayer($p, $cardindex)
{
    global $game;
    
    $numcards = $game["Player_Resource_Actioncards_$p"];
    $index = 0;
    for($f=0; $f<$numcards; $f++) {
        if( $game["Player_Resource_Actioncards_$p".'_'.$f] == $cardindex ) {
            $index = $f;
        }
    }
    //remove this card
    for($f=$index; $f<$numcards-1; $f++) {
        $game["Player_Resource_Actioncards_$p".'_'.$f] = $game["Player_Resource_Actioncards_$p".'_'.strval($f+1)];
    }
    $game["Player_Resource_Actioncards_$p"]--;
    unset($game["Player_Resource_Actioncards_$p".'_'.$game["Player_Resource_Actioncards_$p"] ] );
}




function WhoHasMostFruitsFromFruitCards($fruitarray)
{
    global $game;
    global $FruitCards;
    //TODO: seems to be error here, should be num_players
    $ret = array(0,0,0,0,0,0,0);//7, questionmark is also a fruit type here
    
    //go thru all players
    for($p=0; $p<$game['Num_Players']; $p++) {
        $numcards = $game["Player_Resource_FruitCards_$p"];
        for($f=0; $f<$numcards; $f++) {
            $fruitcardindex = $game["Player_Resource_FruitCards_$p".'_'.$f];
            $fruitsincard = count( $FruitCards[$fruitcardindex] );
            for($g = 0; $g<$fruitsincard; $g++) {
                $cfruit = $FruitCards[$fruitcardindex][$g];
                if( $cfruit == $fruitarray[0] ) {
                    $ret[$p]++;
                }
                if( count ($fruitarray) > 1) {
                    if( $cfruit == $fruitarray[1] ) {
                        $ret[$p]++;
                    }
                }
            }
        }
    }
    // now determine who has most
    $max = 0;
    $maxindex = 0;
    for($p=0; $p<$game['Num_Players']; $p++) {
        if( $ret[$p] == $max) {
            $maxindex = -1; // nobody has most, because two have identical number of fruits
            continue;
        }
        if( $ret[$p] > $max) {
            $max = $ret[$p];
            $maxindex = $p;
        }
    }
    return $maxindex;
}


function CartPathCrossed($fromblade, $toblade)
{
    global $game;
    // in rare cases it is possible to get two carts at once.
    $ret = 0;
    // if going from 12 to 0 etc, toblade is smaller... handle that
    if( $toblade<$fromblade)
        $toblade += $game['Num_WindmillBlades'];
    // go from blade+1, to simulate the steps taken
    $cb = $fromblade+1; //extra var for current blade
    for($i=$fromblade+1; $i<$toblade+1; $i++) {
        if($cb==12) $cb=0; // turn
        if($cb==0 or $cb==6) $ret++; //two cart giving positions
        $cb++;
    }
    return $ret;
}


function GetTotalNumFarmersOnBlade($Blade_Index)
{
    global $game;
    $ret = 0;
    for($p=0; $p<$game['Num_Players']; $p++) {
        for($i=0; $i<$game["Player_Windmill_Farmers_$p"]; $i++) {
            $blade = $game["Player_Windmill_Farmers_$p"."_".$i];
            if( $blade == $Blade_Index) {
                $ret++;
            }
        }
    }
    return $ret;
}

function SummarizePlayerFruitTypesFromFruitCards($player)
{
    global $game;
    global $FruitCards;
    $ret = array(0,0,0,0,0,0,0);//7, questionmark is also a fruit type here
    $numcards = $game["Player_Resource_FruitCards_$player"];
    for($f=0; $f<$numcards; $f++) {
        $fruitcardindex = $game["Player_Resource_FruitCards_$player".'_'.$f];
        $fruitsincard = count( $FruitCards[$fruitcardindex] );
        for($g = 0; $g<$fruitsincard; $g++) {
            $cfruittype = $FruitCards[$fruitcardindex][$g];
            $ret[$cfruittype]++;
        }
    }
    return $ret;
}

// TODO. rename add  "ForBonus""
function GetPlayerFruitCardsFruitNumCountArray($player)
{
    global $game;
    global $FruitCards;
    // if a player has already a bonus, then 
    $sn = $game["Player_Resource_Bonuses_$player"];
    $row = array(0,-$sn,-$sn,-$sn,-$sn,-$sn,-$sn);
    for($f = 0; $f < $game["Player_Resource_FruitCards_$player"]; $f++) {
        $fcardindex = $game["Player_Resource_FruitCards_$player"."_".$f];
        //echo 'XX'.count($FruitCards[$fcardindex]);
        $row[ count($FruitCards[$fcardindex]) ]++;
    }
    return $row;
}

function CountBiggerThanZeroElements($ar) 
{
    $ret = 0;
    foreach($ar as $val) {
        if( $val > 0 ) $ret++;
    }
    return $ret;
}

function GetBladePlus($startblade, $count)
{
    global $game;
    $c = $startblade + $count;
    if( $c > $game['Num_WindmillBlades']-1) {
        $c = $c - $game['Num_WindmillBlades'];
    }
    return $c;
}

function IsQuestionmarkFruitCard($fcindex)
{
    global $FruitCards;
    $fcar = $FruitCards[$fcindex];
    for($i=0; $i<count($fcar); $i++) {
        if( $fcar[$i] == 6) return true;
    }
    return false;
}

// return the famer which stands onthe given blade.
function BladePosToFarmerIndex($player, $blade)
{
    global $game;
    for($f=0; $f<$game["Player_Windmill_Farmers_$player"]; $f++) {
        $cblade = $game["Player_Windmill_Farmers_$player"."_".$f];
        if($cblade == $blade )  
            return $f;
    }
    return -1;
}


// return if place or move farmer is correct.
function GetGamePhase($player) 
{
    global $game;
    if( $game["Player_Resource_NumFarmers_$player"] > 0 ) {
        return 'Place';
    }
    return 'Move';
}

function IsPlayerOnBlade($player, $blade)
{
    global $game;
    for($f=0; $f<$game["Player_Windmill_Farmers_$player"]; $f++) {
        $cblade = $game["Player_Windmill_Farmers_$player"."_".$f];
        if($cblade == $blade )  
            return true;
    }
    return false;
}

?>