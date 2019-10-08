<?php

function logs($fstr)
{
    $fh = fopen('debuglog.txt', 'a+');
    fwrite($fh, $fstr."\n");
    fclose($fh);
}

function logvar($vars)
{
    $fstr = var_export($vars, true);
    logs($fstr);
}

?>