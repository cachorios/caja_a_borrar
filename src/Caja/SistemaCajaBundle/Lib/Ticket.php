<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 25/04/13
 * Time: 18:19
 * To change this template use File | Settings | File Templates.
 */

namespace Caja\SistemaCajaBundle\Lib;

define("ESC", chr(27));
class Ticket {

    private $contenido;

    public function __construct(CajaManager $caja){

    }

    public function setContenido( $contenido )
    {
        $this->contenido = $contenido;
    }



    public function getTicketFull()
    {
        // tipo = 0

    }

    public function getTicketTestigo()
    {
        // tipo = 1

    }

    public function getTicketPisado()
    {
        // tipo = 3

    }

    private function getCabecera($tipo)
    {

        $str = "";
        $str .= ESC . "=".chr(1); //Activar la impresora
        $str .= ESC .'@'; //Inicializa


        /* //Para definir un stamp
        PRINT #1, CHR$(&H1D);"*";CHR$(40);CHR$(4);
        FOR I=1 to 1280
            READ a$: PRINT #1, CHR$(VAL("&H"+a$));
        NEXT I
        */

        return $str;
    }

    private function getPie($tipo)
    {

    }
    private function getContenido($tipo)
    {

    }








}