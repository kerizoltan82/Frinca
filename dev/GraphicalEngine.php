<?php

require_once('Decor.php');
require_once('WinRules.php');
require_once('LoadSaveGame.php');
require_once('PlayerDB.php');
require_once('Tools.php');
require_once('scriptnames.php');

/* ========================================================== */
function printLoginPanel($loggedin_player_id, $curplayername, $curgame)
{
    global $game;
    global $decor;
    global $scriptnames;
    
    print "\n<div class=\"loginpanel\">";
    print '<table class="loginpaneltable"><tr>';
    // game - on the left
    print '<td style="text-align: left; width:30%;">';
    if( $curgame != '') {
        print 'Játék: '.$game['Game_Name'].', '.$game['Game_Rounds'].'. kör';
        print ' | ';
        print ("\n<a href=\"finca.php?logout=game\">Másik játék</a>");
    } else {
        print "<a href=\"".$scriptnames['game_main']."?logout=game\">Játékválasztás</a>";
    }
    print '</td><td style="text-align: center; text-weight: bold;" >';
    print $decor['Game_Name_Caps'].' online';
    print '</td><td style="text-align: right; width:30%;">';
    
    //player  - on the right
    if ($loggedin_player_id == -1) { //multi mode
        print "Több-játékos mód | ";
    } else if ($loggedin_player_id == -2) { // guest login 
        print "Vendég mód | ";
    } else {
        //if( $curgame != '') {
        //    $loggedinname = $game['Name_Player_'.$curplayerindex];
        //} else
        $loggedinname = $curplayername;
        print "Belépve mint $loggedinname | ";
    }
    echo '<a href="finca.php?logout=player">Kijelentkezés</a>&nbsp;';
    print "</td></table></div>\n";
    
    
}

/* ========================================================== */
function GraphicalEngine_Display($loggedin_player_id, $curplayername, $curgameindex)
{

    
    global $game;
    global $decor;
    global $BladeFarmerPosition;
    global $BladeFarmerDirection;
    global $NumFruitCards;
    global $FruitCards;
	
	// determine my player index
	$players = GetPlayers();
	if($loggedin_player_id >= 0) {
		$loggedin_name = $players[$loggedin_player_id];
		for($i=0; $i<$game["Num_Players"]; $i++) {
			if($game['Name_Player_'.$i] == $loggedin_name) {
				$curplayerindex = $i;
			}
		}
	} 
	else {
		$curplayerindex = -1;
	}
	
	
    // game info
    $cp = $game["Current_Player"];
    $cpname = $game['Name_Player_'.$cp];
    $board_enabled = true;

    // determine html title string and board enabled state
    $titlestr = $game['Game_Name'].' játék';
    if( $game['Winner'] > -1 ) {
        $titlestr = 'Vége';
        // if someone won, disable all
        $board_enabled = false;
    } else {
        // if non multi-player, disable if not current player
		//logs('curplayer:'.$curplayerindex);
        if( $curplayerindex > -1 ) {
            if( $curplayerindex != $cp ) {
                $board_enabled = false;
                $titlestr = "Várakozás mások lépésére...";
            } else {
                //echo 'board ENABLED or win situation';
                if( $game['Winner'] == -1 ) {
                    $titlestr = "Te jössz!";
                }
            }
        } 
		if ( $loggedin_player_id == -2 ) {
            // guest mode, disable all.
            $board_enabled = false;
			$curplayerindex = -1;
        }
		else if ( $loggedin_player_id == -1 ) {
            // multi mode, enable all
            $board_enabled = true;
			$curplayerindex = $cp;
        }
    }
    
    printStartHtmlEx($titlestr, 'decor.css', 'decor.js', 'timer.js');
    
    printLoginPanel($loggedin_player_id, $curplayername, $curgameindex);
    
    // PLAYER INFO
    if( $game['Winner'] > -1 ) {
        $winname = $game['Name_Player_'.$game['Winner'] ];
        //print "<div class=\"info\">A játék véget ért! $winname nyert!</div>";
        print "<div class=\"board_info\">A játék véget ért! $winname nyert!</div>";
    } else {
        //print "<div class=\"info\" onClick=\"alert('$cpname játékos következik!')\">Állás</div>";
        $err = $game['Last_Move_Error'];
        $msg = "$cpname játékos következik!";
        // last_move_error means last attempt to move failed
        // for the _current_ player. so do not show this error,
        // only to the player who can move in this step, who is the
        // same who attempted an errorenous move.
        // in other cases if board is not enabled, then there cannot be
        // an errorneous move attempt from the player.
        if( $err != '' && $board_enabled ) {
            $msg = 'Hiba: '.$err;
        } 
        print "<div class=\"board_info\" >$msg</div>";
    }
    

    // LAST STEP
    print ('<div class="laststep">');
    print $game['Game_Last_Step'].". ";
    if( $game['Winner'] > -1 ) {
        print "A játék véget ért!";
    } else {
        print "$cpname játékos következik!";
    }
        
    // *** undo button
    if( $board_enabled ) {
        //echo '<div class="game_undo">';
        echo '<form action="finca.php" method="post" style="position: absolute; bottom:-14px; right: 0px;">';
        echo '<input name="undo" type="hidden" value="yes" /> ';
        echo '<input name="submitbutton" type="submit" value="Visszavonás" /> ';
        echo '</form>';
        //echo '</div>';
    }
    
    print "</div>";
        
    // BOARD section
    print ('<div class="board">');

    // Windmill
    $thegamename = $game['Game_Name'];
    print ("<img src=\"windmill_$thegamename.png\" style=\"border: 0px; border-style: none; position: absolute; width: 380px; height: 380px;\" usemap=\"#windmill_map\" />");
    $num_farmers_on_blade = array();
    for($i=0; $i<$game["Num_WindmillBlades"]; $i++) {
        $num_farmers_on_blade[$i] = 0;
    }
    for($i=0; $i<$game["Num_Players"]; $i++) 
    {
        for($f=0; $f<$game["Player_Windmill_Farmers_$i"]; $f++) {
            $blade = $game["Player_Windmill_Farmers_$i"."_".$f];
            $pos = $BladeFarmerPosition[$blade];
            $dir = $BladeFarmerDirection[$blade];
            // farmer stacks
            //$rad = deg2rad($blade*30); //12 blades*30=360
            $xdir = $num_farmers_on_blade[$blade] * $dir[0];// * cos($rad);
            $ydir = $num_farmers_on_blade[$blade] * $dir[1];//* sin($rad);
            //echo '|'.$xdir.':'.$ydir;
            $pos['x'] = $pos['x'] + $xdir;
            $pos['y'] = $pos['y'] + $ydir;
            $num_farmers_on_blade[$blade]++;
            $colorindex = $game['Player_Color_Index_'.$i];
            $style_str = 'left: '.$pos['x']."; top: ".$pos['y']."; pointer-events: none; background-image: url('res/farmer_player_".$colorindex.".png'); ";
            print("<div class=\"windmill_farmer\" style=\"$style_str\">");
            print ('</div>');//farmer
        }
    }
    // windmill blades map
    if( $board_enabled ) {
        print ('<map name="windmill_map" style="border: 0px; border-style: none;">');
        $gamephase = GetGamePhase($cp);
        if( $gamephase == 'Place' ) {
            $blade_function = 'set'.$gamephase.'Blade';
        } else {
            // not only moves, also actioncards are possible.
            $blade_function = 'bladeClick';
        }
        for($i=0; $i<$game["Num_WindmillBlades"]; $i++) {
        /*
            if( $gamephase == 'Move') {
                // if we have no farmers on the blade, no move is possible.
                if( !IsPlayerOnBlade($cp, $i) ) {
                    continue;
                }
            }*/
            $posstr = GetWindmillBladePolyPositions($i);
            $jscript = "onClick=\"$blade_function($i);\"";
            print ("<area shape=\"poly\" coords=\"$posstr\" href=\"#\" alt=\"proba\" $jscript/>");
        }
        print ('</map>');
    }
    
    // common cart
    print("<div id=\"cart_stand\" >");
    for($i= 0; $i<$game["Num_Carts"]; $i++) {
        print ("<img src=\"res/cart.png\" style=\"border: 0; position: absolute; left:".strval($game["Num_Carts"]*2-$i).'; top:'.strval($game["Num_Carts"]*2-$i*2).'; " />');
    }
    print ('</div>');//cart_stand
    
    // the board regions
    for($i=0; $i<$game['Num_Regions']; $i++) 
    {
        // fruit cards
        //display only the last fruit card
        $lastfruitcard = $game["Num_Region_FruitCards_$i"]-1;
        if( $lastfruitcard > -1) { // if there are any fruitcards left
            $pos = GetRegionPosition($i);
            $xpos = $pos['x'];
            $ypos = $pos['y'];
            $fcindex = $game["Board_Region_FruitCards_$i"."_".$lastfruitcard];
            $qm = '0';
            if( IsQuestionmarkFruitCard($fcindex) ) {
                $qm = '1';
            }
            for( $f= 0 ; $f<$lastfruitcard; $f++) {
                $cssstyle = "left: $xpos; top: $ypos; ";
                $htm = "id=\"region_fruitcard_$i\" ";
                $ypos = $ypos - 4;
                $xpos = $xpos - 2;
                DrawFruitCardWithBg(-1, $htm, $cssstyle);
            }
            $jscript ='';
            if( $board_enabled ) {
                $jscript = "onClick=\"setMoveRegion($i, $qm)\"";
            }
            $htm = "id=\"region_fruitcard_$i\" $jscript";
            $cssstyle = "left: $xpos; top: $ypos;";
            DrawFruitCardWithBg($fcindex, $htm, $cssstyle);
        }
       
        // finca card
        $regionfinca = $game["Board_Region_Finca_$i"];
        $pos = GetFincaPosition($i);
        $xpos = $pos['x']- 50; //pos is right-bottom
        $ypos = $pos['y']- 48;
        $htmlatt = "left: $xpos; top: $ypos";
        if( $regionfinca > -1 ) {
            DrawFincaCardWithBg($regionfinca, $htmlatt);
        } else {    
            // colored finca for player who has won it
            $fincawonplayer = $game["Board_Region_WhoHasWonFinca_$i"];
			if($fincawonplayer >= 0) {
				$color_of_player = $game["Player_Color_Index_".$fincawonplayer];
				DrawFincaHouse($color_of_player, $htmlatt);
			}
			else {
				// no one has won it, white color
				DrawFincaHouse(-1, $htmlatt);
			}
            
        }
    }
    print ('</div>'); //board
    
    // players' resources
    for($i=0; $i<$game['Num_Players']; $i++) {
        if( $i == 1 or $i==3 )
            DrawPlayerResources($i, $cp, $board_enabled, true);
        else
            DrawPlayerResources($i, $cp, $board_enabled, false);
    }
    
    // placeholder for board
    print ('<div style="height:600px;"></div>');
    
    //DEBUG
    //CreateWindmill();
    
   // test();
    //print ('<img src="windmill.png"></img>');
    
    // GAM CHANGE part
    
    echo '<div class="game_move">';
    
    // move submit form
    echo '<form id="gamestepform" action="finca.php" method="post">';
    //echo 'The Move:<br>';
    echo '<input id="movetypeparam" name="move" type="text" value="" cols="2" /> ';
    //echo 'The Move parameter 1:<br>';
    echo '<input id="moveparam1" name="moveparam1" type="text" value="" /> ';
    //echo 'The Move parameter 2:<br>';
    echo '<input id="moveparam2" name="moveparam2" type="text" value="" /> ';

    //echo 'Action card:<br>';
    echo '<input id="actionparam1" name="actionparam1" type="text" value="" /> ';
    echo '<input id="actionparam2" name="actionparam2" type="text" value="" /> ';
    
    echo '<input name="submitbutton" type="submit" value="Lépés" /> ';

    echo '</form>';
    echo '</div>';

    //PrintDebugInfo();
    
    // fruit popup blanket
    print ("\n<div id=\"blanket\" style=\"display:none;\">");
    print ("</div>");
        
    print ("<div id=\"fruitpopup_window\" style=\"display:none;\">");
    print "Gyümölcs választó ablak<br><hr>";
    print ('<table id="fruitpopup_table"><tr>');
    for($i=0; $i<$game['Num_Fruit_Types']; $i++) 
    {
        print ('<td>');
        // style=\"background-image: url('fruit_big_$i.png');\"
        //print ("<div class=\"popup_fruit\">");
        print ("<img id=\"fruitpopup_fruit_$i\" src=\"res/fruit_big_$i.png\" style=\"border: 0;\" onClick=\"fruitpopup_selectFruit($i);\" />");
        //</div>");
        print ('</td>');
        if( $i==2) {
            print "</tr><tr>";
        }
        
    }
    print "</tr></table>";
    print "<div id=\"fruitpopup_hidebutton\" onClick='hideFruitPopup();'>Bezárás</div>";

    print ("</div>\n");
    
    PrintAjaxInteractive($loggedin_player_id, $curplayerindex, $curgameindex);
    
    printEndHtml();
    
}

function PrintAjaxInteractive($loggedin_player_id, $curplayerindex, $curgameindex)
{
    global $game;
    
    //for multi-mode, there is no ajax.
    if( $loggedin_player_id == -1) return;
    // for guest mode also no ajax.
    if( $loggedin_player_id == -2) return;
    // if someone has won, do not do check for update
    if( $game['Winner'] > -1 ) return;
    
    $ItsThisPlayersTurn = ($game["Current_Player"] == $curplayerindex);
    // TODO: always check because of undo option?
    if($ItsThisPlayersTurn) return;
    
    print "<script>\n";
    $cg = $game["Current_Player"];
    print "setupTimer('$curgameindex', $cg);\n";
    print "</script>\n";
}


/* ========================================================== */
// playerindex, currentplayerindex, enabled, horizontal
function DrawPlayerResources($i, $cp, $board_enabled, $isHorizontal ) 
{
    global $game;
    global $decor;
    
    $colorindex = $game['Player_Color_Index_'.$i];
    $col = $decor['Player_Color_'.$colorindex];
    $ItsThisPlayersTurn = ($i == $cp);
    
    $fruitcards_start = 190;
    
    $xtracss = "";
    $bx = 2;
    if( $ItsThisPlayersTurn ) {
        //$xtracss = "background-color: #d0d0f0;";
        //$bx = 2;
    }
    print ('<div class="div_player_'.$i.'" style="border: '.$bx.'px solid #'.$col."; $xtracss\">");
    
    //player name
    //print "<span style=\"color: $col; font-weight: bold; font-size: 16pt; font-family: Sans-Serif;\">".$game['Name_Player_'.$i].'</span>';
    
    //farmers
    $currentspace = 0;
    if($game["Player_Resource_NumFarmers_$i"] > 0) {
        $blockwidth = GetCssPlayerBlock($isHorizontal, 24, 38, true);
        print "<div style=\"$blockwidth\">";
        for($f=0; $f<$game["Player_Resource_NumFarmers_$i"]; $f++) {
            print ("<img src=\"res/farmer_player_$colorindex.png\" style=\"display: inline;\" >");
        }
        print '</div>';
        // add the space to currentspace
        if( $isHorizontal ) {
            $currentspace = $currentspace+25;
        } else {
            $currentspace = $currentspace+39;
        }
    }
    
    //actioncards
    $isfirst = ($currentspace == 0);
    $blockwidth = GetCssPlayerBlock($isHorizontal, 32, 32, $isfirst);
    print "<div style=\"$blockwidth\">";
    $currentspace = $currentspace+33;
    for($f=0; $f<$game["Player_Resource_Actioncards_$i"]; $f++) {
        $acindex = $game["Player_Resource_Actioncards_$i".'_'.$f];
        $jscript = '';
        if( $ItsThisPlayersTurn && $board_enabled) {
            $jscript = " onClick=\"setActioncard($i, $acindex);\"";
        }
        print ("<img id=\"player_$i"."_actioncard_$acindex\" src=\"res/actioncard_$colorindex".'_'.$acindex.".png\" style=\"display: inline;\" $jscript>");
        
    }
    
    //carts
    $dense_offset = -12;
    $dense_num = 2;
    for($f=0; $f<$game["Player_Resource_NumCarts_$i"]; $f++) {
        $jscript = '';
        if( ($i == $cp) && $board_enabled) {
            $jscript = " onClick=\"setDeliver();\"";
        }
        $xtrastyle = '';
        if( $game["Player_Resource_NumCarts_$i"] > $dense_num && $f>0) {
            if( $isHorizontal ) {
                $xtrastyle = "position: relative; top: ".strval($dense_offset*$f)."px;";
            } else {
                $xtrastyle = "position: relative; left: ".strval($dense_offset*$f)."px;";
            }
        }
        print ("<img src=\"res/cart_middle.png\" style=\"display: inline; $xtrastyle\" $jscript>");
    }
    print '</div>';
    
    //fruits
    $dense_offset = -10;
    $dense_num = 5;
    $densemax = 7;
    
    $fruitspace = $fruitcards_start - $currentspace;

    $blockwidth = GetCssPlayerBlock($isHorizontal, $fruitspace, $fruitspace, false);
    print "<div style=\"$blockwidth\">";
    for($j=0; $j<$game["Num_Fruit_Types"]; $j++) {
        $blockwidth = GetCssPlayerBlock(!$isHorizontal, 30, 30, ($j==0) );
        print "<div style=\"$blockwidth\">";
        $numfruityoftype = $game["Player_Resource_Fruit_$i"."_".$j];
        $isdensearrangement = $numfruityoftype > $dense_num;
        if( ! $isdensearrangement ) {
            $fruitdistance = 26;
        } else {
            $fruitdistance = 16;
        }        
        for($f=0; $f<$numfruityoftype; $f++) {
                
            $xtrastyle = 'position: absolute; ';
            /*if(  && $f>0) {
                if( !$isHorizontal ) {
                    $xtrastyle = "position: relative; top: ".strval($dense_offset*$f)."px;";
                } else {
                    $xtrastyle = "position: relative; left: ".strval($dense_offset*$f)."px;";
                }
            }*/

            if( !$isHorizontal ) {
                $xtrastyle .= "top: ".strval($currentspace+$fruitdistance*$f)."px; left: ".strval(3+$j*31)."px;";
            } else {
                $xtrastyle .= "left: ".strval($currentspace+$fruitdistance*$f)."px; top: ".strval(3+$j*31)."px;";
            }
            DrawFruit($j, $xtrastyle);
            if( $f >= $densemax ) {
                break;
            }

        }
        
        // do not draw even more fruits.
        $xtrastyle = 'position: absolute; ';
        if( $numfruityoftype >= 5 ) {
            $col = $decor["Fruit_Color_$j"];
            if( !$isHorizontal ) {
                $xtrastyle .= "top: ".strval($currentspace+$fruitspace-24)."px; left: ".strval(3+$j*30)."px;";
            } else {
                $xtrastyle .= "left: ".strval($currentspace+$fruitspace-24)."px; top: ".strval(3+$j*30)."px;";
            }
            print "<span style=\"padding:2px; font-weight: bold; color: #$col; $xtrastyle\">".$numfruityoftype."</span>";
        }
        
        print '</div>';
    }
    print '</div>';
    
    //acquired fruitcards
    $blockwidth = GetCssPlayerBlock($isHorizontal, 400, 400, false);
    print "<div style=\"$blockwidth\" onClick=\"popupPlayerInfo($i)\">";

    if( $isHorizontal ) {
        $cols = 5; $rows = 3;
        $startx = $fruitcards_start; $starty = 0;
    } else {
        $cols = 3; $rows = 5;
        $starty = $fruitcards_start; $startx = 0;
    }
    $crow = 0; $ccol = 0;

    for($f=0; $f<$game["Player_Resource_FruitCards_$i"] ; $f++) {

        $htmlatt = "left: ".strval($startx+$ccol*60)."; top: ".strval($starty+$crow*60)."; ";
        DrawFruitCardWithBg($game["Player_Resource_FruitCards_$i"."_".$f], "", $htmlatt);

        if( $isHorizontal ) {
            $crow++;
            if($crow>2) { $ccol++;$crow = 0;}
            if($ccol > 5) {break;}
        } else {
            $ccol++;
            if($ccol>2) {$crow++;$ccol = 0;}
            if($crow > 5) {break;}
        }

    }
    print '</div>';
    
    // acquired fincas
    $blockwidth = GetCssPlayerBlock($isHorizontal, 50, 50, false);
    print "<div style=\"$blockwidth\">";

    for($f=0; $f<$game["Player_Resource_Bonuses_$i"]; $f++) {
        //bonus
        $bon = $game["Player_Resource_Bonuses_$i".'_'.$f];
        $img = "res/bonus_$bon.png";
        if($isHorizontal) {
            // TODO: move to css
            $htmlatt = "style=\"background-image: url('$img'); width: 42px; height: 42px; position: static; display: inline-block;\"";
        } else {
            $htmlatt = "style=\"background-image: url('$img'); width: 42px; height: 42px; position: static; display: inline-block; float: left;\"";
        }        
        print "<div $htmlatt></div>";
    }
    for($f=0; $f<$game["Player_Resource_Fincas_$i"]; $f++) {
        if($isHorizontal) {
            $htmlatt = "position: static; display: inline-block;";
        } else {
            $htmlatt = "position: static; display: inline-block; float: left;";
        }
        DrawFincaCardWithBg( $game["Player_Resource_Fincas_$i"."_".$f], $htmlatt );
    }
    echo 'P: ';
    $points = GetPlayerPoints($i) ;
    echo $points;
    print ('</div>');
    
    print ('</div>');
/*
    // blanket for other players
    if( $ItsThisPlayersTurn ) {
        // allow click-thru for blanket
        print ('<div class="div_player_'.$i.'" style="border: 2px solid #'.$col.'; pointer-events: none;  background: #A4B6B9; opacity: 0.45; filter: alpha(opacity=45);">');
        print ('</div>');
    }
    */
    // print text for popup info window
    PrintPlayerInfo($i);
    
}

// prints info into an invisible div, that can be accessed then
// thru click and JS
function PrintPlayerInfo($p)
{
    global $game;
    global $decor;
    
    print '<div id="playerinfo_'.$p.'" class="playerinfo">';
    $pname = $game['Name_Player_'.$p];
    print "INFORMÁCIÓ: $pname játékos\n\n";
    $fruittypear = SummarizePlayerFruitTypesFromFruitCards($p);
    print "Megszerzett gyümölcskártyák gyümölcsei:\n";
    $i=0;
    // questionmark is also valid here, 7 fruit types.
    foreach($fruittypear as $fruit) {
        $fname = $decor["Fruit_Name_$i"];
        print "$fname: $fruit\n";
        $i++;
    }
    $points = GetPlayerPoints($p) ;
    print "\nÖsszpontszám: ".$points;
    print '</div>';
}


/* ========================================================== */
// returns a horizontal or vertical css style attributes for a html div
function GetCssPlayerBlock($isHorizontal, $w, $h, $isfirst )
{
    $border_color = "#166419";
    if( $isHorizontal) {
        $xtra = '';
        if( !$isfirst ) {
            $xtra = "border-left: 1px solid $border_color;";
        }
        $blockwidth = "width: $w"."px; height: 100%; float: left; ".$xtra;
    } else {
        $xtra = '';
        if( !$isfirst ) {
            $xtra = "border-top: 1px solid $border_color;";
        }
        $blockwidth = "height: $h"."px; width: 100%; ".$xtra;
    }
    return $blockwidth;
}

function DrawFincaCardWithBg($regionfinca, $htmlatt)
{
    print "<div class=\"region_finca\" style=\"$htmlatt; background-image: url('res/fincacard_$regionfinca.png')\" > ";
    //DrawFincaCard($regionfinca);
    print "</div>";
}

// draws the finca card, each fruit (simple mode)
function DrawFincaCard($fincaindex)
{
    global $FincaCards;
    for($f=0; $f<count($FincaCards[$fincaindex]); $f++) {
        $fr = $FincaCards[$fincaindex][$f];
        DrawFruit($fr, '');
    }
}

// draws the finca house for the player
// (finca house is got when fruitcards are empty for that region)
// player can b -1, then grey finca card is drawn (nobody got the finca)
function DrawFincaHouse($color, $extracss) 
{
    print "<div class=\"region_finca\" style=\"$extracss; background-image: url('res/fincahouse_$color.png')\" > ";
    print "</div>";
}

function DrawFruitCardWithBg($fruitcardindex, $htmlattr, $cssstyle)
{
    if( $fruitcardindex == -1 ) {
        $img = "res/fruitcard_empty.png";
    } else {
        $img = "res/fruitcard_$fruitcardindex.png";
    }
    print "<div class=\"region_fruitcard\" $htmlattr style=\"background-image: url('$img'); $cssstyle\"> ";
    //DrawFruitCard( $fruitcardindex );
    print "</div>";
}

function DrawFruitCard($fruitcardindex)
{
    global $FruitCards;
    if($fruitcardindex >= 0) {
        for($f=0; $f<count($FruitCards[$fruitcardindex]); $f++) {
            $fr = $FruitCards[$fruitcardindex][$f];
            DrawFruit($fr, '');
        }
    }
}

// html img element
function DrawFruit($fruitindex, $xtracss)
{
    print ("<img src=\"res/fruit_$fruitindex.png\" style=\"display: inline; pointer-events: none; $xtracss\" >");
}


/* ========================================================== */
// prints debug info, players resources, fruit cards, etc
function PrintDebugInfo()
{
        
    global $game;
    global $decor;
    global $BladeFarmerPosition;
    global $NumFruitCards;
    global $FruitCards;
    
    // leave place for absolute elements
    echo '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
    echo '=== DEBUG GAME INFO ===<br>';
    print "Number of players: ".$game["Num_Players"]."<br>";
    print "Current player: ".$game["Current_Player"]."<br>";
    print "The Game Name: ".$game["Game_Name"]."<br>";
    print "The GamePhase: ".$gamephase."<br>";
    
    //carts
    global $Num_Carts;
    echo "<br><br>Number of free carts: ".$game["Num_Carts"]." <br>";
   
    //textual display - players' recources
    for($i=0; $i<$game['Num_Players']; $i++) 
    {
        
        echo '<br><br>Players:<br>';
        print " =Player_Resource_NumFarmers_ $i: ".$game["Player_Resource_NumFarmers_$i"]."<br>";
        print " =Player_Resource_NumCarts_ $i: ".$game["Player_Resource_NumCarts_$i"]."<br>";
        
        for($f=0; $f<$game["Num_Fruit_Types"]; $f++) {
            print " - Player fruits $i / $f: ".$game["Player_Resource_Fruit_$i"."_".$f]."<br>";
        }
        for($f=0; $f<$game["Player_Windmill_Farmers_$i"]; $f++) {
            print " - Player farmers on windmill blade $i / $f: ".$game["Player_Windmill_Farmers_$i"."_".$f]."<br>";
        }
        /*
        for($f=0; $f<$game["Player_Windmill_Farmers_$i"]; $f++) {
            print " - Player farmers on windmill blade $i / $f: ".$game["Player_Windmill_Farmers_$i"."_".$f]."<br>";
        }*/
        
    }
    
    print ('<div class="regions">');
    print ('</div>');
    echo '<br><br>This is the board:<br>';
    
    for($i=0; $i<$game['Num_Regions']; $i++) 
    {
        print " =Region finca index $i: ".$game["Board_Region_Finca_$i"]."<br>";
        for($f=0; $f<$game["Num_Region_FruitCards_$i"]; $f++) {
            print " - Region fruitcard index $i / $f: ".$game["Board_Region_FruitCards_$i"."_".$f]."<br>";
        }
    }
    
    //draw all fruitcards
    for($i=0; $i<$NumFruitCards; $i++) {
        print ('<div style="border: 1px solid black; height:66px; width:400px;">');
        print ("Card $i");
        DrawFruitCard($i);
        //DrawFruitCardWithBg( $i, "", "position: static; display: inline;" );
        print "<img src=\"res/fruitcard_$i.png\" />";
        print ('</div>');
    }
    
    //draw all finca cards
    global $NumFincaCards;
    for($i=0; $i<$NumFincaCards; $i++) {
        print ('<div style="border: 1px solid black; height:56px; width:400px; background: #c0c0c0;">');
        print ("FincaCard $i");
        DrawFincaCard($i);
        //DrawFincaCardWithBg($i, "position: static; display: inline;");
        print "<img src=\"res/fincacard_$i.png\" />";
        print ('</div>');
    }
    
    // windmill textual display
    echo '<br><br>The windmill:<br>';
    for($i=0; $i<$game["Num_WindmillBlades"]; $i++) 
    {
        print '<span class="span_fruit_'.$game["Windmill_Blade_Fruits_$i"].'">';
        print "-Windmill blade fruit $i: ".$game["Windmill_Blade_Fruits_$i"]."<br>";
        print '</span>';
    }
    
    //bonuses
    echo '<br><br>Available bonuses:<br>';
    for($i=0; $i<$game['NumBonuses']; $i++) 
    {
        print " -Bonus $i: ".$game["Bonuses_$i"]." points<br>";
    }
    
} //PrintDebugInfo


/* ========================================================== */
//          HTML printing functions
/* ========================================================== */

// prints the start or resume page with game list.
function GraphicalEngine_StartOrResumeGame($loggedin_player_id, $cpname, $archivegamestoload)
{
    global $scriptnames;

    CheckArchivedGames() ;

    printStartHtmlEx("Játékválasztás", "decor.css", 'decor.js', '', 'new_game_load();');
    printLoginPanel($loggedin_player_id, $cpname, '');
    
    // left finca logo
    echo '<div class="finca_cover"></div>';

    // resume game panel
    print ("<div class=\"island_panel\" style=\"width: 560px; float: left;\">");

    echo '<h1>Csatlakozás/Folytatás</h1>';
    if( $archivegamestoload ) {
        echo 'Befejezett játékok: ';
    } else {
        echo 'Jelenleg aktív játékok: ';
    }
    echo '<form action="'.$scriptnames['game_main'].'" method="post">';

    echo '<select name="game" style="width: 260px; height: 140px;" multiple="multiple">';
    if( $archivegamestoload ) {
        $games = GetArchivedGameList();
    } else {
        $games = GetUnfinishedGameList($cpname);
    }
    foreach($games as $g) {
        print '<option>'.$g.'</option>';
    }
    echo '</select>';
    echo '<br/><br/><center><input name="submit" type="submit" value="Csatlakozás" style="width: 280px; height: 40px;" /></center> ';
    echo '</form>';

    print ('<form action="'.$scriptnames['game_main'].'" method="post">');
    if( $archivegamestoload ) {
        print ('<input type="hidden" name="archivedgames" value="0" >');
        print ('<input type="submit" value="Aktív játékok" /></form>');
    } else {
        print ('<input type="hidden" name="archivedgames" value="1" >');
        print ('<input type="submit" value="Befejezett játékok" /></form>');
    }

    echo '</div>';
    
    // new game panel
    print ("<div class=\"island_panel\" style=\"width: 560px; float: left;\">");

    echo '<h1>Új játék</h1>';
    echo '<form action="'.$scriptnames['game_start'].'" method="post">';

    echo 'Az új játék neve: ';
    // get a game name which is not already occupied
    $newgamenamebase='Jatek';
    $newgamename = $newgamenamebase.'1';
    for($i=0; $i<100; $i++) {
        $gamefound= true;
        foreach($games as $g) {
            if($g == $newgamename) {
                $gamefound= false;
                break;
            }
        }
        if($gamefound) {
            break;
        }
        $newgamename = $newgamenamebase.$i;
    }
    print ("<input Name=\"inp_name\" type=\"text\" value=\"$newgamename\" /> ");
    
    echo '<br>Játékosok száma:';
    echo '<select Name="inp_numplayers" onchange="new_game_player_numberofplayers_changed(this.selectedIndex);" ><option>2</option><option>3</option><option>4</option></select>';
    //echo '<input Name="inp_numplayers" type="text" value="2" /> ';
	echo '<br>';
	
	$players = GetPlayers();
	$colors[0] = 'F00000';
	$colors[1] = '0000F0';
	$colors[2] = 'F0F000';
	$colors[3] = '008000';
	
	
		
	for($p=0; $p<4; $p++) {
		$player_disp_name = ($p+1);
		$disp = 'display: block;';
		if($p>1) { $disp = 'display: none;'; }
		echo '<div id="new_player_box_'.$p.'" style="'.$disp.'">'.$player_disp_name.'. Játékos:';
		//echo '<input Name="inp_player_'.$p.'" type="hidden" value=""  /> ';
		// gets POSTed when form is  submitted
		echo '<select id="player_name_select_'.$p.'" name="player_name_select_'.$p.'" > ';
		//onselect="player_name_selected('.$p.');"  // does not get called when selcting from combo
		for($i=0; $i<count($players); $i++) {
			echo '<option>'.$players[$i].'</option>';
		}
		echo '</select>';
		
		echo ' színe:';
		echo '<input Name="inp_color'.$p.'" id="inp_color'.$p.'" type="hidden" value="'.$p.'" /> ';

		for($j=0; $j<4; $j++) {
			$pclass = 'player_color_selector';
			if ($p==$j) {
				$pclass = 'player_color_selector_selected';
			}
			print '<div id="player_color_selector_box'.$p.'_'.$j.'" onClick="player_select_color('.$p.','.$j.')" class="'.$pclass.'" style="background-color: #'.$colors[$j].';">&nbsp;</div>';
		}
		echo '</div>'; //new_player_box_
	}
	/*
    echo '<br>2. Játékos neve:';
    echo '<input Name="inp_player1" type="text" value="Kék" /> ';
    echo '2. Játékos színe:';
    echo '<input Name="inp_color1" type="text" value="1" /> ';

    echo '<br>3. Játékos neve:';
    echo '<input Name="inp_player2" type="text" value="Sárga" /> ';
    echo '3. Játékos színe:';
    echo '<input Name="inp_color2" type="text" value="2" /> ';

    echo '<br>4. Játékos neve:';
    echo '<input Name="inp_player3" type="text" value="Zöld" /> ';
    echo '4. Játékos színe:';
    echo '<input Name="inp_color3" type="text" value="3" /> ';
*/
    echo '<br /><br /><center><input Name="submit" type="submit" value="Új játék létrehozása" style="width: 280px; height: 40px;"/></center> ';
    echo '</form></div>';
    printEndHtml();
}

/* ========================================================== */
function GraphicalEngine_Error($desc)
{
    printStartHtml("Error", 'decor.css', '', '');
    echo 'This is an error.<br>'.$desc;
  //  print "The game number: $Current_Game<br>";
   // print "The Current player: ".$game["Current_Player"]."<br>";
    printEndHtml();
}

/* ========================================================== */
function GraphicalEngine_LogOut()
{
    global $scriptnames;
    
    printStartHtmlEx("Kilépés", 'game_message_page.css', '', '');
    print ("<table><tr><td>");
    print ("<div id=\"main_panel\">");
    
    echo '<br /><br />Kiléptél a játékból.<br>';
    echo '<br /> <a href="'.$scriptnames['game_main'].'">Belépés ismét</a>';
    
    print ("</div>");
    print ("</td></tr></table>");
    printEndHtml();
}


/* ========================================================== */
function GraphicalEngine_AskLogin_Page($extramsg)
{
    global $scriptnames;
    
    printStartHtmlEx("Belépés", 'game_message_page.css', 'messagepage.js', '');
    print ("<table><tr><td>");
    print ("<div id=\"main_panel\">");
    print ('<div style=" width: 100%; border-bottom: 2px solid #22D522; margin:0 ; padding: 3px;">BELÉPÉS</div>');
    
    echo '<form id="loginform" method="post" action="'.$scriptnames['game_main'].'">';
    if($extramsg!='') {
        print ( $extramsg.'<br/>');
    } else {
        echo '<br/>';
    }
    echo 'Név:';
    echo '<input id="loginname" name="player" type="text" value="" cols="16"/> <br/>'; 
    
    
    echo '<input id="register_check" name="register_check" type="checkbox" value="reg" /> <label for="register_check">Regisztráció</label><br>';
	echo '<br/> <input id="loginbutton" class="loginbutton" name="submitbutton" type="submit" value="Belépés"/> ';

    echo '<br /><input id="loginguest" class="loginbutton" name="submitbutton2" type="submit" value="Belépés vendégként" onClick="guestlogin();"/> ';
    echo '</form>';
    print ("</div>");
    print ("</td></tr></table>");
    printEndHtml();
}


/* ========================================================== */

function IncludeStylesheet($cssfile)
{
    print("<link rel=\"stylesheet\" href=\"$cssfile\" type=\"text/css\" />\n");
}

// TODO .remove tthis function
function printHeader($includeJs, $title)
{
    printHeaderEx($includeJs, $title, "decor.css");
}

function printHeaderEx($title, $cssfile, $includeJs1, $includeJs2)
{
    global $decor;
    echo '<head>';  
    print ('<meta http-equiv="content-type" content="text/html; charset=utf-8" />');
    if( $title !="") $title = " | ".$title;
    print ("<title>".$decor['Game_Name']."$title</title>");
    IncludeStylesheet($cssfile);
    if( $includeJs1 !=  '' ) {
        print('<script type="text/javascript" src="'.$includeJs1.'"></script>');
    }
    if( $includeJs2 !=  '' ) {
        print('<script type="text/javascript" src="'.$includeJs2.'"></script>');
    }
    echo '</head>';
}

function printStartHtmlEx($title, $cssfile, $includeJs1, $includeJs2, $body_onload_funct = '')
{
    echo '<html>';
    printHeaderEx($title, $cssfile, $includeJs1, $includeJs2 );
	if($body_onload_funct == '') {
		echo '<body>';	
	}
	else {
		echo '<body onLoad="'.$body_onload_funct.'">';	
	}
    
}

function printEndHtml()
{
    echo '</body>';
    echo '</html>';
}

?>