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
define("NL", chr(10));
class Ticket
{

    /**
     * @var array
     * Contiene pares de valores, conceto e importe
     */
    private $contenido;
    /**
     * @var CajaManager
     */
    private $cajamanager;

    public function __construct(CajaManager $cajaman)
    {
        $this->cajamanager = $cajaman;
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }


    public function getTicketFull()
    {
        // tipo = 0
        $sticket = $this->getCabecera(0);
        $sticket .= $this->getContenido(0);
        $sticket .= $this->getPie(0);

        return $sticket;


    }

    public function getTicketTestigo()
    {
        // tipo = 1
        $sticket = $this->getCabecera(1);

    }

    public function getTicketPisado()
    {
        // tipo = 2
        $sticket = $this->getCabecera(2);

    }

    private function getCabecera($tipo)
    {
        $fecha = date("d-m-Y");
        $hora = date("H:i:s");
        $str = "";
        $caja = $this->cajamanager->getCaja();

        if ($tipo == 0) {
            $str .= ESC . "=" . chr(1); //Activar la impresora
            $str .= ESC . '@'; //Inicializa
            $str .= ESC . 'a' . chr(1); //centro
            $str .= "MUNICIPALIDAD DE POSADAS" . ESC . 'J' . chr(30);
            $str .= "***" . ESC . 'J' . chr(30);

            $str .= ESC . 'a' . chr(0); //izquierda
            $str .= "DIR: " . "RIVADAVIA 1501, POSADAS" . NL;
            $str .= str_pad("FECHA: $fecha", 20, " ", STR_PAD_RIGHT) . str_pad("HORA: $hora", 20, " ", STR_PAD_LEFT) . NL;
            $str .= str_pad("", 40, "-");

            $str .= "CAJA: " . $caja->getNumero() . NL;
         //   $str .= ESC . 'p' . chr(0) . chr(10) . chr(100); //envia pulso?? esto copie, voy a verificarlo

        }

        return $str;
    }


    private function getContenido($tipo)
    {
        $str = "";
        $str .= ESC . '!' . chr(1);

        $total = 0;
        //0,1
        if ($tipo == 0) {
            if (is_array($this->contenido)) {
                foreach ($this->contenido as $reg) {
                    //largo del concepto supera 29, dividirlo
                    $arr = str_split($reg[0], 29);

                    if (count($arr) > 1) {
                        for ($i = 0; $i < count($arr); $i++) {
                            $str .= str_pad($arr[$i], 29, ' ');
                            if ($i < count($arr) - 1) {
                                $str .= NL;
                            } else {
                                $str .= str_pad($reg[1], 11, ' ', STR_PAD_LEFT) . NL;
                            }

                        }
                    } else {
                        $str .= str_pad($reg[0], 29, ' ');
                        $str .= str_pad($reg[1], 11, ' ', STR_PAD_LEFT) . NL;
                    }
                    $total += $reg[1];
                }

                $str .= ESC . "U" . chr(1); //unidireccional
                $str .= ESC . "!" . chr(17);
                $str .= str_pad("TOTAL:", 20, " ", STR_PAD_RIGHT) . str_pad($total, 20, " ", STR_PAD_LEFT) . NL;
            } else {
                $str .= $this->contenido.NL;
            }

        }
        return $str;
    }

    private function getPie($tipo)
    {
        $str = "";
        $str .= ESC . "U" . chr(0); //cancelar unidireccional
        $str .= ESC . "!" . chr(1); //caracter normal
        $caja = $this->cajamanager->getCaja();
        if ($tipo == 0) {
            $str .= str_pad("", 40, "-") . NL;
            $str .= str_pad("*** GRACIAS ***", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(2);
            $str .= str_pad("CAJERO: ". $caja->getCajero()->getUsername()  , 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(2);
            $str .= str_pad("visite: www.posadas.gov.ar", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(1);
            $str .= str_pad("***", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(11); //posicion de corte
        }

        return $str;
    }

}