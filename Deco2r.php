<?php


function GetDecorations()
{
    global $decor;
    $decor  = array();
    
    $decor['Game_Name'] = 'Frinca';
    $decor['Game_Name_Caps'] = 'F R I N C A';

    
    $decor['Region_Name_0'] = 'Albambus';
    $decor['Region_Name_1'] = 'Lanco';
    $decor['Region_Name_2'] = 'Lacazzo';
    $decor['Region_Name_3'] = 'Bilbom';
    $decor['Region_Name_4'] = 'Hu Chu';
    $decor['Region_Name_5'] = 'Vandumi';
    $decor['Region_Name_6'] = 'Lagor';
    $decor['Region_Name_7'] = 'Harbhata';
    $decor['Region_Name_8'] = 'Jynxata';
    $decor['Region_Name_9'] = 'Colonne';
    
    $decor['Fruit_Name_0'] = 'Barack';
    $decor['Fruit_Name_1'] = 'Szőlő';
    $decor['Fruit_Name_2'] = 'Körte';
    $decor['Fruit_Name_3'] = 'Cseresznye';
    $decor['Fruit_Name_4'] = 'Dió';
    $decor['Fruit_Name_5'] = 'Szilva';
    $decor['Fruit_Name_6'] = 'Kérdőjel';

    $decor['Fruit_Color_0'] = 'ff8000';
    $decor['Fruit_Color_1'] = '008f00';
    $decor['Fruit_Color_2'] = 'ffff00';
    $decor['Fruit_Color_3'] = 'ff0000';
    $decor['Fruit_Color_4'] = '804000';
    $decor['Fruit_Color_5'] = '000080';
    
    $decor['Player_Color_0'] = 'ff0000';
    $decor['Player_Color_1'] = '0000ff';
    $decor['Player_Color_2'] = 'ffff00';
    $decor['Player_Color_3'] = '00ff00';
    
    
    $decor['windmill_size'] = 380;
        
    FillBladeFarmerPositions();
}

function GetRegionPosition($region)
{
    $pos = array();
    if($region == 0 ) {
        $pos['x'] = 385;
        $pos['y'] = 151;
    }
    if($region == 1 ) {
        $pos['x'] = 657;
        $pos['y'] = 154;
    }
    if($region == 2 ) {
        $pos['x'] = 372;
        $pos['y'] = 287;
    }
    if($region == 3 ) {
        $pos['x'] = 515;
        $pos['y'] = 247;
    }
    if($region == 4 ) {
        $pos['x'] = 623;
        $pos['y'] = 306;
    }
    if($region == 5 ) {
        $pos['x'] = 115;
        $pos['y'] = 450;
    }
    if($region == 6 ) {
        $pos['x'] = 214;
        $pos['y'] = 388;
    }
    if($region == 7 ) {
        $pos['x'] = 361;
        $pos['y'] = 454;
    }
    if($region == 8 ) {
        $pos['x'] = 438;
        $pos['y'] = 398;
    }
    if($region == 9 ) {
        $pos['x'] = 598;
        $pos['y'] = 396;
    }
    return $pos;
}


function GetFincaPosition($region)
{
    $pos = array();
    if($region == 0 ) {
        $pos['x'] = 495;
        $pos['y'] = 120;
    }
    if($region == 1 ) {
        $pos['x'] = 643;
        $pos['y'] = 226;
    }
    if($region == 2 ) {
        $pos['x'] = 432;
        $pos['y'] = 265;
    }
    if($region == 3 ) {
        $pos['x'] = 571;
        $pos['y'] = 361;
    }
    if($region == 4 ) {
        $pos['x'] = 704;
        $pos['y'] = 281;
    }
    if($region == 5 ) {
        $pos['x'] = 184;
        $pos['y'] = 427;
    }
    if($region == 6 ) {
        $pos['x'] = 325;
        $pos['y'] = 397;
    }
    if($region == 7 ) {
        $pos['x'] = 404;
        $pos['y'] = 429;
    }
    if($region == 8 ) {
        $pos['x'] = 548;
        $pos['y'] = 494;
    }
    if($region == 9 ) {
        $pos['x'] = 622;
        $pos['y'] = 502;
    }
    return $pos;
}


function FillBladeFarmerPositions() 
{
    global $BladeFarmerPosition;
    global $BladeFarmerDirection;
    
    $BladeFarmerPosition = array();
    $BladeFarmerDirection = array();
    
    for($i=0; $i<12; $i++) {
        $BladeFarmerPosition[$i] = array();
    }
    
    $BladeFarmerDirection[0] = array(10,8);
    $BladeFarmerDirection[1] = array(10,8);
    $BladeFarmerDirection[2] = array(10,8);
    $BladeFarmerDirection[3] = array(7,10);
    $BladeFarmerDirection[4] = array(7,10);
    $BladeFarmerDirection[5] = array(10,8);
    $BladeFarmerDirection[6] = array(10,8);
    $BladeFarmerDirection[7] = array(10,8);
    $BladeFarmerDirection[8] = array(10,8);
    $BladeFarmerDirection[9] = array(10,8);
    $BladeFarmerDirection[10] = array(10,8);
    $BladeFarmerDirection[11] = array(10,8);
    
    $BladeFarmerPosition[0]['x'] = 54;
    $BladeFarmerPosition[0]['y'] = 130;
    
    $BladeFarmerPosition[1]['x'] = 92;
    $BladeFarmerPosition[1]['y'] = 71;
    
    $BladeFarmerPosition[2]['x'] = 148;
    $BladeFarmerPosition[2]['y'] = 36;
    
    $BladeFarmerPosition[3]['x'] = 220;
    $BladeFarmerPosition[3]['y'] = 30;
    
    $BladeFarmerPosition[4]['x'] = 300;
    $BladeFarmerPosition[4]['y'] = 68;
    
    $BladeFarmerPosition[5]['x'] = 336;
    $BladeFarmerPosition[5]['y'] = 130;
    
    $BladeFarmerPosition[6]['x'] = 336;
    $BladeFarmerPosition[6]['y'] = 206;
    
    $BladeFarmerPosition[7]['x'] = 280;
    $BladeFarmerPosition[7]['y'] = 260;
    
    $BladeFarmerPosition[8]['x'] = 230;
    $BladeFarmerPosition[8]['y'] = 320;
    
    $BladeFarmerPosition[9]['x'] = 162;
    $BladeFarmerPosition[9]['y'] = 318;
    
    $BladeFarmerPosition[10]['x'] = 82;
    $BladeFarmerPosition[10]['y'] = 276;
    
    $BladeFarmerPosition[11]['x'] = 54;
    $BladeFarmerPosition[11]['y'] = 226;

}

function GetWindmillBladePosition($blade) 
{
    global $decor;
    $pos = array();
    if($blade == 0 ) {
        $pos['x'] = 51;
        $pos['y'] = 128;
    }
    if($blade == 1 ) {
        $pos['x'] = 75;
        $pos['y'] = 60;
    }
    if($blade == 2 ) {
        $pos['x'] = 132;
        $pos['y'] = 30;
    }
    if($blade == 3 ) {
        $pos['x'] = 212;
        $pos['y'] = 50;
    }
    if($blade == 4 ) {
        $pos['x'] = 237;
        $pos['y'] = 73;
    }
    if($blade == 5 ) {
        $pos['x'] = 254;
        $pos['y'] = 132;
    }
    if($blade == 6 ) {
        $pos['x'] = 259;
        $pos['y'] = 211;
    }
    if($blade == 7 ) {
        $pos['x'] = 212;
        $pos['y'] = 235;
    }
    if($blade == 8 ) {
        $pos['x'] = 165;
        $pos['y'] = 253;
    }
    if($blade == 9 ) {
        $pos['x'] = 130;
        $pos['y'] = 258;
    }
    if($blade == 10 ) {
        $pos['x'] = 60;
        $pos['y'] = 211;
    }
    if($blade == 11 ) {
        $pos['x'] = 32;
        $pos['y'] = 165;
    }

    return $pos;
}

function GetWindmillBladePolyPositions($blade) 
{
    global $decor;
    if($blade == 0 ) {
        return '66,131,156,181,151,205,55,205,66,131'; //bad
    }
    if($blade == 1 ) {
        return '124,70,177,159,165,177,77,127,124,70'; //bad
    }
    if($blade == 2 ) {
        return '205,46,205,149,187,158,136,73,205,46'; //bad
    }
    if($blade == 3 ) {
        return '214,55,214,153,235,154,286,66,214,55';
    }
    if($blade == 4 ) {
        return '290,77,346,124,257,175,241,163,290,77';
    }
    if($blade == 5 ) {
        return '344,135,371,204,269,204,260,185,344,135';
    }
    if($blade == 6 ) {
        return '262,212,363,212,351,285,260,235,262,212';
    }
    if($blade == 7 ) {
        return '252,239,338,290,292,343,240,257,252,239';
    }
    if($blade == 8 ) {
        return '233,259,277,343,212,369,212,266,233,259';
    }
    if($blade == 9 ) {
        return '180,260,204,262,204,362,133,350,180,260';
    }
    if($blade == 10 ) {
        return '161,239,176,251,128,338,70,293,161,239';
    }
    if($blade == 11 ) {
        return '46,212,147,212,159,231,75,279,46,212';
    }

    return "";
}

function setTransparency($new_image,$image_source) 
{ 
    
    $transparencyIndex = imagecolortransparent($image_source); 
    $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255); 
     
    if ($transparencyIndex >= 0) { 
        $transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);    
    } 

    $transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']); 
    imagefill($new_image, 0, 0, $transparencyIndex); 
    imagecolortransparent($new_image, $transparencyIndex); 

} 

function imagecopyx($dest_img , $src_img , $dest_x , $dest_y, $imgw,$imgh)
{
   // imagesetpixel($imgdest, $x, $floor, $color);
    for ($x = 0; $x < $imgw; $x++) {
        for ($y = 0; $y < $imgh; $y++) {
            $mask_pix = imagecolorat($src_img,$x,$y);
            //$mask_pix_color = imagecolorsforindex($src_img, $mask_pix);
            //if ($mask_pix_color['alpha'] < 127) {
            $a     = ($mask_pix & 0x7F000000) >> 24;
            //echo $a.'.';
            if($a < 127) {
                imagesetpixel($dest_img, $x+$dest_x, $y+$dest_y, $mask_pix);
                ///$src_pix = imagecolorat($src,$x,$y);
                //$src_pix_array = imagecolorsforindex($src, $src_pix);
                //imagesetpixel($src, $x, $y, imagecolorallocatealpha($src, $src_pix_array['red'], $src_pix_array['green'], $src_pix_array['blue'], 127 - $mask_pix_color['alpha']));
            }
        }
    }
}

function test()
{
    //fruit
    $secondimg = imagecreatefrompng("blade_0.png");
    imagesavealpha ( $secondimg ,true );
    imagealphablending ( $secondimg ,false );

    //bg start
    $blank = imagecreatetruecolor(380, 380);
    imagealphablending($blank, false);
    imagesavealpha ( $blank ,true );
    $trp = imagecolorallocatealpha($blank,0,0,0,127);
    imagefill($blank, 0, 0, $trp);
    
    //rot img
    $background = imagecolortransparent($secondimg);
    $rotimg = imagerotate ( $secondimg , 38 , $background ); 
    imagesavealpha ( $rotimg ,true );

    imagecopy ( $blank , $rotimg , 100 , 100, 0 , 0 , 76,107);

    //rot2
    $rotimg2 = imagerotate ( $secondimg , 73 , $background ); 
  
    $imgw = imagesx($rotimg2);
    $imgh = imagesy($rotimg2);
    imagecopyx ( $blank , $rotimg2 , 50 , 50, $imgw,$imgh);
    
    //save
    imagepng ( $blank , 'windmillx.png');

    imagedestroy($blank);
    imagedestroy($secondimg);
    imagedestroy($rotimg);
    imagedestroy($rotimg2);
}

function CreateWindmill($game_name)
{
    global $game;
    
    // prepare fruit images (each will be used twice)
    $fruitimages = array();
    for($i=0; $i<$game["Num_Fruit_Types"]; $i++) {
        //$fruitimages[$i] = imagecreatefrompng("blade_fruit_$i.png");
        $fruitimages[$i] = imagecreatefrompng("blade_$i.png");
        
        imagesavealpha ( $fruitimages[$i] ,true );
        imagealphablending($fruitimages[$i], false);
        
    }

    // load background
  /*  $curbg = imagecreatefrompng("blades.png");
    $imgw = imagesx($curbg);
    $imgh = imagesy($curbg);
    imagesavealpha ( $curbg ,true );
*/
    $blank = imagecreatetruecolor(380, 380);
    //$black = imagecolorallocate($blank, 0, 0, 0);
    //imagecolortransparent($blank, $black);
    //imagecopy( $blank, $curbg, 0,0,0,0,$imgw,$imgh);
    /*
    imagealphablending($blank, true);
    setTransparency($blank,$fruitimages[0] ); 
    */
    imagealphablending($blank, false);    
    imagesavealpha ( $blank ,true );
    
    $trp = imagecolorallocatealpha($blank,0,0,0,127);
    imagefill($blank, 0, 0, $trp);
    
    for($i=0; $i<$game["Num_WindmillBlades"]; $i++) {
        $pos = GetWindmillBladePosition($i) ;
        
        $fruit = $game["Windmill_Blade_Fruits_$i"];
        $rotate_deg = -($i-3)*30;
        $fruitimg = $fruitimages[$fruit];
        
        $background = imagecolortransparent($fruitimg);
        $rotimg = imagerotate ( $fruitimg , $rotate_deg , $background ); 
        
        $imgw = imagesx($rotimg);
        $imgh = imagesy($rotimg);
        //imagecopymerge ($blank, $rotimg, $pos['x'] , $pos['y'],0,0,$imgw,$imgh,100);
        //imagecopy ($blank, $rotimg, $pos['x'] , $pos['y'],0,0,$imgw,$imgh);
        imagecopyx ($blank, $rotimg, $pos['x'] , $pos['y'],$imgw,$imgh);

    }
    //save background as windmill
    imagesavealpha($blank, true);
    imagepng ( $blank , "windmill_$game_name.png");
    
    
}

?>