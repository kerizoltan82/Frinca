<?php

/*
Used modules:
Tools
WinRules

*/

require_once('WinRules.php');
require_once('Tools.php');
require_once('debug.php');


// increments the plyer, so the next player can move.
function IncrementPlayer()
{
    logs( 'MainRules: IncrementPlayer');
    global $game;
    
    $game['Current_Player'] = $game['Current_Player'] + 1;
    if( $game['Current_Player'] >= $game['Num_Players'] ) {
        $game['Current_Player'] = 0;
        $game['Game_Rounds']++;
    }
}

// executes the player's move according tp the game rules.
function ExecMove($TheMove)
{
    global $game;
    logs('Mainrules: ExecMove start');
    logvar($TheMove);
    // clear error flag and message
    $game['Last_Move_Error'] = '';
    
    MovePlayerResources($TheMove);
    
    if( $game['Last_Move_Error'] != '' ) {
        return false;
    }
    
    $game_end = CheckWin();
    // check for action card 0
    if( $TheMove['Move_Type'] == 'Move_Farmer' && $TheMove['ActionCard'] == '0' ) {
        // get card from player
        RemoveActioncardFromPlayer($TheMove['Player_Index'], 0);
        //$game['Game_Last_Step'] .= " felhasználta a Dupla lépés akciókártyát";
        $game['Game_Last_Step_DoubleMove'] = $game['Game_Last_Step'];
    } else {
        if($game['Game_Last_Step_DoubleMove'] != '' ) {
            $game['Game_Last_Step'] .= ", felhasználta a 'Dupla lépés' akciókártyát. Előtte: ".$game['Game_Last_Step_DoubleMove'];
            $game['Game_Last_Step_DoubleMove'] = '' ;
        }
        // next player
        if( $game_end == -1) {
            IncrementPlayer();
        }
    }
    
    if( $game_end != -1) {
        // game has ended.
        $game['Winner'] = $game_end;
        //$game['Game_Last_Step'] .= ' A játék véget ért!';
        logs( "Mainrules: ExecMove, WINNER: ".$game['Winner']);
    }
    return true;
}


function MovePlayerResources($TheMove)
{
    global $game;
    global $decor;
    global $FruitCards;
    global $FincaCards;
    
    $player = $TheMove['Player_Index'];
    $move = $TheMove['Move_Type'];
    
    $game['Game_Last_Step'] = $game['Name_Player_'.$player];
    
    if( $move == 'Place_Farmer' )
    {
        if( assert_move($game["Player_Resource_NumFarmers_$player"]>0,'No more farmers left') ) return;
        // decrease available farmers to player
        $game["Player_Resource_NumFarmers_$player"]--;
        // set farmer to windmill blade
        $game["Player_Windmill_Farmers_$player"."_".$game["Player_Windmill_Farmers_$player"] ] =  $TheMove['Windmill_Blade'];
        $game["Player_Windmill_Farmers_$player"]++;
        //return fruit for player
        $fruit = $game["Windmill_Blade_Fruits_".$TheMove['Windmill_Blade']];
        $game["Player_Resource_Fruit_$player"."_".$fruit]++;
        $fruitdesc = $decor['Fruit_Name_'.$fruit];
        $game['Game_Last_Step'] .= ' bábut rakott le';
        $game['Game_Last_Step'] .= ", kapott 1 $fruitdesc-t";
    }
    
    if( $move == 'Move_Farmer' or $move == 'ActionCard1')
    {
        if( assert_move($TheMove['Farmer_Index']>=0,'No farmer on this blade.') ) return;
        if( $move == 'Move_Farmer' ) {
            $fromblade = $game["Player_Windmill_Farmers_$player"."_".$TheMove['Farmer_Index']];
            // determine number of steps
            $steps = GetTotalNumFarmersOnBlade($fromblade);
            $toblade = GetBladePlus($fromblade, $steps);
            $game['Game_Last_Step'] .= ' bábuval lépett';
        } else if ($move == 'ActionCard1') {
            $fromblade = $game["Player_Windmill_Farmers_$player"."_".$TheMove['Farmer_Index']];
            //$TheMove['Farmer_Index'] 
            $toblade = $TheMove['To_Blade'] ;
            $game['Game_Last_Step'] .= ' a Széllökés akciókártyát kijátszotta';
        }
        
        // get number of fruits to acquire
        $fruit = $game["Windmill_Blade_Fruits_".$toblade];
        $numfruits = GetTotalNumFarmersOnBlade($toblade) + 1; //the player's own farmer also counts
        $fruitdesc = $decor['Fruit_Name_'.$fruit];
        $game['Game_Last_Step'] .= ", kapott $numfruits $fruitdesc-t";
        //exec
        $game["Player_Windmill_Farmers_$player"."_".$TheMove['Farmer_Index']] = $toblade;
        $game["Player_Resource_Fruit_$player"."_".$fruit] = $game["Player_Resource_Fruit_$player"."_".$fruit] + $numfruits;
        logs( "movefarmer from:$fromblade to:$toblade fruits:$fruit numfruits: $numfruits MOVEaction: $move");
        
        if( $move == 'Move_Farmer' ) { // actioncard does not give carts
            // check cart
            $givecarts = CartPathCrossed($fromblade, $toblade);
            if( $givecarts > 0) {
                $game['Game_Last_Step'] .= ", kapott szamarat";
                //check if there are enough carts
                if( $game['Num_Carts'] < $givecarts ) {
                    // collect carts from all players
                    for($px=0; $px<$game['Num_Players']; $px++) {
                        $game['Num_Carts'] = $game['Num_Carts'] + $game["Player_Resource_NumCarts_$px"];
                        $game["Player_Resource_NumCarts_$px"] = 0;
                    }
                    $game['Game_Last_Step'] .= ", mindenki visszadta a szamarát";
                }
                // move carts from common to player
                $game['Num_Carts'] =  $game['Num_Carts'] - $givecarts;
                $game["Player_Resource_NumCarts_$player"] = $game["Player_Resource_NumCarts_$player"] + $givecarts;
            }
        }
        
        //remove card from player
        if ($move == 'ActionCard1') {
            // TODO check if card is there
            //if( assert_move($game[""]>0,'card1 is not owned') ) return;
            RemoveActioncardFromPlayer($player, 1);
        }
    }
    
    if( $move == 'Deliver' )
    {
        // move the fruit card and summarize fruits
        $regions = $TheMove['Regions']; //array
        $fruits = array(0,0,0,0,0,0); //6, should be num_fruit_types
        $allnumfruits = 0;
        
        $game['Game_Last_Step'] .= " szállított";

        $deliver_mixedsixfruiter = false;

        // go thru all delivered regions
        for($i=0; $i<count($regions); $i++) {
        
            // ** get top fruitcard for the region
            $cregion = $regions[$i];
            $lastindex = $game["Num_Region_FruitCards_$cregion"]-1;
            $fruitcardindex = $game["Board_Region_FruitCards_$cregion"."_".$lastindex];

            $regionname = $decor['Region_Name_'.$cregion];
            if($i>0) {
                $game['Game_Last_Step'] .= ", ";
            }
            $game['Game_Last_Step'] .= " $regionname-ba";
            
            // ** Count the fruits
            // handle questionmark
            $q_deliver_fruit = $TheMove['Questionmarks'][$i];
            for($j=0; $j<count($FruitCards[$fruitcardindex]); $j++) {
                $cfruit = $FruitCards[$fruitcardindex][$j] ;
                if( $cfruit == 6) { //questionmark
                    $cfruit = $q_deliver_fruit;
                }
                $fruits[$cfruit]++;
                $allnumfruits++;
            }

            // cehck if this is a 6-fruites mixed. (needed later)
            $mixedsixfruiter = true;
            // we can do this inside the regionloop because
            // the mixedsixfruiter var is only needed when delivering
            // 6 fruits, not 10, because then the -1 actioncard cannot be used
            // together with the 10 actioncard.
            for($j=0; $j<$game['Num_Fruit_Types']; $j++) {
                if( $fruits[$j] != 1 ) $mixedsixfruiter = false;
            }
            
            if( $mixedsixfruiter ) {
                $deliver_mixedsixfruiter = true;
                //echo 'deliver mixedsix';
            } 
            
            //check bonus situation before
            $row = GetPlayerFruitCardsFruitNumCountArray($player);
            $noOfFilledBefore = CountBiggerThanZeroElements($row);

            // ** move fruitcard to player
            $game["Player_Resource_FruitCards_$player".'_'.$game["Player_Resource_FruitCards_$player"]] = $fruitcardindex;
            $game["Player_Resource_FruitCards_$player"]++;
            unset( $game["Board_Region_FruitCards_$cregion"."_".$lastindex] );
            $game["Num_Region_FruitCards_$cregion"]--;

            //check bonus situation after
            $row = GetPlayerFruitCardsFruitNumCountArray($player);
            $noOfFilledAfter = CountBiggerThanZeroElements($row);

            // ** check and give bonus
            if( $noOfFilledBefore < 6 && $noOfFilledAfter == 6 ) {
                // first bonus
                logs( 'BONUS BEFORE'.$noOfFilledBefore.' AFTER'. $noOfFilledAfter );
                logvar($row);
                if( $game['Bonuses'] > 0 ) {
                    //echo 'Bonus set' ;
                    $game["Player_Resource_Bonuses_$player".'_'.$game["Player_Resource_Bonuses_$player"]] = $game['Bonuses_'.strval($game['Bonuses']-1) ];
                    //echo ' Bonus from common:'.$game['Bonuses_'.strval($game['Bonuses']-1) ];
                    logs( ' Bonus at player:'.$game["Player_Resource_Bonuses_$player".'_'.$game["Player_Resource_Bonuses_$player"]] );
                    $game["Player_Resource_Bonuses_$player"]++;
                    $game['Bonuses']--;
                    unset( $game['Bonuses_'.$game['Bonuses'] ] );
                    $game['Game_Last_Step'] .= " (bónuszt is kapott)";
                }
            }
          /*  
        }
        // go thru all delivered regions for finca ditribution
        for($i=0; $i<count($regions); $i++) {
        */
            $cregion = $regions[$i];
            // ** check if last fruitcard, then distribute finca to a player
            if ( $game["Num_Region_FruitCards_$cregion"] == 0 ) {
                $regionfincaindex = $game["Board_Region_Finca_$cregion"];
                $winplayer = WhoHasMostFruitsFromFruitCards($FincaCards[$regionfincaindex]);
                $game["Board_Region_Finca_$cregion"] = -1; //no finca
                $game["Board_Region_WhoHasWonFinca_$cregion"] = $winplayer; // save for later display
                if( $winplayer > -1 ) {
                    $game["Player_Resource_Fincas_$winplayer".'_'.$game["Player_Resource_Fincas_$winplayer"] ] = $regionfincaindex;
                    $game["Player_Resource_Fincas_$winplayer"]++;
                    logs( "$winplayer got the finca");
                    $winplayername = $game['Name_Player_'.$winplayer];
                    $game['Game_Last_Step'] .= " (a Frinca kártyát $winplayername kapta)";
                } else {
                    // nobody got this finca card
                    logs( 'nobody got the finca');
                    $game['Game_Last_Step'] .= " (a Frinca kártyát senki se kapta)";
                }
            }
        } // end for regions
        
        // handle '-1' actioncard
        if( $TheMove['ActionCard'] == '3' ) {
            // the fruit type is given in the parameter.
            // allow one less here.
            $fruits[$TheMove['ActionCard_Param1'] ]--;
            // this card cant be used for cards with 1 fruit.
            // special case the mixed 6-fruiters, where it is allowed not to deliver any fruit
            // of one type only, and the case if there is the mixedsixfruiter, and a 1-fruit card,
            // where it is also allowed.
            // (the '10' actioncard can't be used with -1).
            if( !$deliver_mixedsixfruiter ) {
                if( assert_move($fruits[$TheMove['ActionCard_Param1'] ]> 0,"This actioncard can't be used for 1-fruit fruitcards.") ) return;
            }
            // we may deliver 7 fruits also with this actioncard. (regarding limit check).
            $allnumfruits--;
            $game['Game_Last_Step'] .= ", felhasználta a '-1' akciókártyát";
        }
        
        // give fruits back
        logs( 'deliver Fruits:');
        logvar($fruits);

        $game['Game_Last_Step'] .= " ";
        
        $deliveredfruits = 0;
        for($i=0; $i<$game['Num_Fruit_Types']; $i++) {
            if( $fruits[ $i] > 0 ) {
                $game["Player_Resource_Fruit_$player"."_".$i] = $game["Player_Resource_Fruit_$player"."_".$i] - $fruits[$i];
                $ftype = $decor["Fruit_Name_$i"];
                if( assert_move($game["Player_Resource_Fruit_$player"."_".$i]>= 0,"Not enough fruits of $ftype.") ) return;
                $game["Common_NumFruits_$i"] = $game["Common_NumFruits_$i"] + $fruits[$i];
                if( $deliveredfruits>0) {
                   $game['Game_Last_Step'] .= ", ";
                }
                $game['Game_Last_Step'] .= $fruits[$i]." db $ftype-t";
                $deliveredfruits++;
            }
        }
        if( $TheMove['ActionCard'] == '2' ) { //max 10 card
            $limit = 10;
            $game['Game_Last_Step'] .= ", felhasználta a 'Max 10' akciókártyát";
        } else {
            $limit = 6;
        }
        if( assert_move($allnumfruits <= $limit , "Too much delivered fruits ($allnumfruits).") ) return;
        

        //place cart from player to common (only if normal deliver, not with actioncard '10')
        if( $TheMove['ActionCard'] != '2' ) {
            $game["Player_Resource_NumCarts_$player"] = $game["Player_Resource_NumCarts_$player"] - 1;
            //echo 'cartx'.$game["Player_Resource_NumCarts_$player"];
            if( assert_move($game["Player_Resource_NumCarts_$player"] >= 0,"Player has no cart.") ) return;
            $game['Num_Carts'] =  $game['Num_Carts'] + 1;
        }
        
        //remove action cards
        if( $TheMove['ActionCard'] == '2' ) {
            RemoveActioncardFromPlayer($player, 2);
        }
        if( $TheMove['ActionCard'] == '3' ) {
            RemoveActioncardFromPlayer($player, 3);
        }

    }
}


?>