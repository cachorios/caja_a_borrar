<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 12/07/13
 * Time: 19:27
 * To change this template use File | Settings | File Templates.
 */


function obtenerNumero( $importe){
    $importe = round($importe,2);
    $v1 = floor($importe/2)*2;
    $v2 = floor($importe/5)*5;
    echo "$importe $v1 $v2 \n";
    return $v1>$v2 ? $v1 : $v2;
};



echo  obtenerNumero(495.10)."\n";







