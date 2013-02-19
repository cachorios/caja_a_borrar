<?php
//namespace Caja\SistemaCajaBundle\Lib;
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 18/01/13
 * Time: 12:28
 * To change this template use File | Settings | File Templates.
 */

function aFechaAADDD( $texto )
{
	$texto --;
    $fecha = date_create_from_format('yz', $texto);
    return $fecha;
}

/**
 * @param $texto
 * @return DateTime
 */
function aFechaAAMMDD( $texto )
{
    $fecha = date_create_from_format('ymd', $texto);
    return $fecha;
}


/**
 * @param $fecha DateTime
 * @param $dias int
 * @return dateTime
 */
function sumarAFecha($fecha, $dias){
    $fecha->add(new DateInterval('P'.$dias.'D'));
    return $fecha;
}

