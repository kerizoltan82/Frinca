<?php

// TODO: array, and passwords

function GetPlayerIDFromDB($name) 
{
    if( $name == 'guest' ) return -2;
    if( $name == 'multi' ) return -1;
    if( $name == 'Piros' ) return 0;
    if( $name == 'Kék' ) return 1;
    
    if( $name == 'Agi' ) return 3;
    if( $name == 'Z' ) return 4;
    return -3;
}

function GetPlayerNameFromDB($id) 
{
    if( $id == -2 ) return 'guest';
    if( $id == -1 ) return 'multi';
    if( $id ==  0 ) return 'Piros';
    if( $id ==  1 ) return 'Kék';
    
    if( $id == 3 ) return 'Agi';
    if( $id == 4 ) return 'Z';
    return -3;
}


?>