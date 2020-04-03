<?php
/*
 this script contains code to fill the fruit and frinca cards at startup randomly

*/

function FillFruitCards()
{
    global $FruitCards;
    global $NumFruitCards;
    
    $FruitCards = array();
    $NumFruitCards = 42;
    for($i=0; $i<$NumFruitCards; $i++) {
        $FruitCards[$i] = array();
    }
    
    // one-fruiters
    $f=0;
    for($i=0; $i<6; $i++) {
        $FruitCards[$f][0] = $i;
        $f++;
    }
    
    // two-fruiters
    for($i=0; $i<6; $i++) {
        $FruitCards[$f][0] = $i;
        $FruitCards[$f][1] = $i;
        $f++;
    }

    // three-fruiters
    for($i=0; $i<6; $i++) {
        $FruitCards[$f][0] = $i;
        $FruitCards[$f][1] = $i;
        $FruitCards[$f][2] = $i;
        $f++;
    }
    
    //four fruits
    for($i=0; $i<5; $i++) {
        $FruitCards[$f][0] = $i;
        $FruitCards[$f][1] = $i;
        $FruitCards[$f][2] = $i+1;
        $FruitCards[$f][3] = $i+1;
        $f++;
    }
    $FruitCards[$f][0] = 5;
    $FruitCards[$f][1] = 5;
    $FruitCards[$f][2] = 0;
    $FruitCards[$f][3] = 0;
    $f++;
    
    //five fruits (rather custom)
    $FruitCards[$f][0] = 0;
    $FruitCards[$f][1] = 0;
    $FruitCards[$f][2] = 0;
    $FruitCards[$f][3] = 2;
    $FruitCards[$f][4] = 2;
    $f++;

    $FruitCards[$f][0] = 1;
    $FruitCards[$f][1] = 1;
    $FruitCards[$f][2] = 1;
    $FruitCards[$f][3] = 5;
    $FruitCards[$f][4] = 5;
    $f++;
    
    $FruitCards[$f][0] = 2;
    $FruitCards[$f][1] = 2;
    $FruitCards[$f][2] = 2;
    $FruitCards[$f][3] = 4;
    $FruitCards[$f][4] = 4;
    $f++;

    $FruitCards[$f][0] = 3;
    $FruitCards[$f][1] = 3;
    $FruitCards[$f][2] = 3;
    $FruitCards[$f][3] = 0;
    $FruitCards[$f][4] = 0;
    $f++;

    $FruitCards[$f][0] = 4;
    $FruitCards[$f][1] = 4;
    $FruitCards[$f][2] = 4;
    $FruitCards[$f][3] = 1;
    $FruitCards[$f][4] = 1;
    $f++;

    $FruitCards[$f][0] = 5;
    $FruitCards[$f][1] = 5;
    $FruitCards[$f][2] = 5;
    $FruitCards[$f][3] = 3;
    $FruitCards[$f][4] = 3;
    $f++;    
    
    //six fruits
    for($i=0; $i<3; $i++) {
        for($j=0; $j<6; $j++) {
            $FruitCards[$f][$j] = $j;
        }
        $f++;
    }
    
    //questionmarks - all 3 pieces the same
    for($x=0; $x<6; $x++) 
    {
        for($j=0; $j<4; $j++) 
            $FruitCards[$f][$j] = 6;
        $f++;
        for($j=0; $j<5; $j++) 
            $FruitCards[$f][$j] = 6;
        $f++;
        for($j=0; $j<6; $j++) 
            $FruitCards[$f][$j] = 6;
        $f++;
    }
    
}

function FillFincaCards()
{
    global $FincaCards;
    global $NumFincaCards;
    global $game;
    
    $FincaCards = array();
    $NumFincaCards = $game['Num_Regions'];
    for($i=0; $i<$NumFincaCards; $i++) {
        $FincaCards[$i] = array();
    }
    
    //first 7 single fruits
    for($i=0; $i<7; $i++) {
        $FincaCards[$i][0] = $i;
    }
    
    //3 mixed fruits
    for($i=0; $i<3; $i++) {
        $FincaCards[7+$i][0] = $i*2;
        $FincaCards[7+$i][1] = $i*2+1;
    }
    
}


?>