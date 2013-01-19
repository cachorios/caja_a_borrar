<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 17/01/13
 * Time: 23:19
 * To change this template use File | Settings | File Templates.
 */

$exp  = aN('0012345');

$fn = create_function("", "return ({$exp});" );

$ret = $fn();

echo $exp,'  ', $ret,"\n";

eval("\$ret=".$exp.";");
echo $exp,'  ', $ret,"\n";

function aN($var){
    $cad = "$var";
    echo "cad $cad \n";
    $ret = (integer)$var;

    echo "\n",$var," ", $ret;
    return $ret;

}
?>
