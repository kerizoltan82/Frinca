<?php
/*

 code for game startup, setting variables in $game

*/

require_once('FruitCards.php');

function StartGame()
{
    FillFruitCards();
    
    global $game;
    
    // game main variables
    $game['Last_Move_Error'] = '';
    $game['Current_Player'] = 0;
    $game['Num_Regions'] = 10;
    $game['Num_Fruit_Types'] = 6;
    $game['Num_WindmillBlades'] = 12;
    $game['Start_Num_Region_FruitCards'] = 4;
    $game['StartNumBonuses'] = 4;
    $game['StartNumActionCards'] = 4;
    // last step actioncard, for descriptions
    $game['Game_Last_Step'] = "A játék elkezdődött";
    $game['Game_Last_Step_DoubleMove'] = '';
    $game['Game_Rounds'] = 1; // number of game rounds
    
    $game['Winner'] = -1; // who is the winner
    
    SetNumPlayerDependantResources();
    SetInitialCommonResources();
    SetPlayerInitialResources();
}

function SetInitialCommonResources()
{
    global $game;
    // fruits

    //echo "|startules_numfruittypes=$Num_Fruit_Types";
    $game["Common_NumFruits"] = $game['Num_Fruit_Types'];
    for($i=0; $i<$game['Num_Fruit_Types']; $i++) 
    {
        $game["Common_NumFruits_$i"] = 18;
    }
    
    // carts

    $game['Num_Carts'] = $game['StartNumCarts'];
    
    // bonuses

    $game['NumBonuses'] = $game['StartNumBonuses'];
    $game['Bonuses'] = $game['StartNumBonuses'];
    for($i=0; $i<$game['StartNumBonuses']; $i++) 
    {
        $game["Bonuses_$i"] = 4 + $i;
    }
    
    // windmill
    $par = array();
    for($i=0; $i<$game['Num_WindmillBlades']; $i++)  {
        $b = round($i/2 - 0.1) ;
        if($b <=0 ) { // to avoid "-0" 
            $b = 0;
        }
        $par[$i] = $b;
    }
    
    shuffle($par);
    for($i=0; $i<$game['Num_WindmillBlades']; $i++)  {
        $game["Windmill_Blade_Fruits_$i"] = $par[$i];
    }
    
    // board
    $par = array();
    // 42 fruitcards, 2 are not set. $game['Num_Regions']*$game["Start_Num_Region_FruitCards"]
    for($i=0; $i<42; $i++) {
        $par[$i] = $i;
    }

    shuffle($par);
    $fc = 0;
    for($i=0; $i<$game['Num_Regions']; $i++) {
        $game["Num_Region_FruitCards_$i"] = $game['Start_Num_Region_FruitCards'];
        for($j=0; $j<$game["Num_Region_FruitCards_$i"]; $j++) {
            $game["Board_Region_FruitCards_$i"."_".$j] = $par[$fc]; // fruit card index 
            $fc++;
        }
    }
    //fincas
    $par = array();
    for($i=0; $i<$game['Num_Regions']; $i++) {
        $par[$i] = $i;
    }

    shuffle($par);
    for($i=0; $i<$game['Num_Regions']; $i++) {
        $game["Board_Region_Finca_$i"] = $par[$i]; // finca index
    }

    // won finca
    for($i=0; $i<$game['Num_Regions']; $i++) {
        $game["Board_Region_WhoHasWonFinca_$i"] = -1; // nobody yet
    }
    
}

function SetNumPlayerDependantResources()
{

    global $game;
    
    if($game['Num_Players'] == 2)
    {
        $game['StartNumFarmers'] = 5;
        $game['EndNumFincas'] = 4;
        $game['StartNumCarts'] = 4;
    }
    if($game['Num_Players'] == 3)
    {
        $game['StartNumFarmers'] = 4;
        $game['EndNumFincas'] = 5;
        $game['StartNumCarts'] = 6;
    }
    if($game['Num_Players'] == 4)
    {
        $game['StartNumFarmers'] = 3;
        $game['EndNumFincas'] = 6;
        $game['StartNumCarts'] = 8;
    }
}


function SetPlayerInitialResources()
{
    global $game;
    
    for($p=0; $p<$game['Num_Players']; $p++)
    {
        $game["Player_Resource_NumFarmers_$p"] = $game['StartNumFarmers'];
        $game["Player_Resource_NumCarts_$p"] = 0;
        //fincas, bonuses, actioncards
        $game["Player_Resource_FruitCards_$p"] = 0; // empty
        $game["Player_Resource_Fincas_$p"] = 0; // empty
        $game["Player_Resource_Bonuses_$p"] = 0; // empty
        $game["Player_Resource_Actioncards_$p"] = 4;
        for($f=0; $f<$game['StartNumActionCards']; $f++)
        {
            $game["Player_Resource_Actioncards_$p".'_'.$f] = $f; // action card index
        }
        
        for($f=0; $f<$game['Num_Fruit_Types']; $f++)
        {
            $game["Player_Resource_Fruit_$p"."_".$f] = 0;
        }

        $game["Player_Windmill_Farmers_$p"] = 0; // no farmers yet on windmill blades

    }
}


?>