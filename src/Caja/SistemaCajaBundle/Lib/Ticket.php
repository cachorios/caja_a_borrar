<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 25/04/13
 * Time: 18:19
 * To change this template use File | Settings | File Templates.
 */

namespace Caja\SistemaCajaBundle\Lib;

use Doctrine\ORM\EntityManager;

define("ESC", chr(27));
define("NL", chr(10));

class Ticket
{

    /**
     * @var array
     * Contiene pares de valores, conceto e importe
     */
    private $contenido;
    private $valores;

    /**
     * @var CajaManager
     */
    private $cajamanager;
    private $barra;

    public function __construct(CajaManager $cajaman, CodigoBarraLive $barra)
    {
        $this->cajamanager = $cajaman;
        $this->barra = $barra;
        $this->valores = Array();
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }

    public function setValores($valores)
    {
        $this->valores = $valores;
    }

    public function getTicketFull()
    {
        // tipo = 0
        $sticket = $this->getCabecera(0);
        $sticket .= $this->getContenido(0);
        $sticket .= $this->getPie(0);

        return $sticket;
    }

    /*
     * Dependiendo del tipo de seccion
     * la impresion sale por una u otra boca
     */
    public function getTimbrado($seccion)
    {
        /*
        //Si es de cementerio,   convenio     u otras tasas,   o patente    o dominio publico,    o publicidad, o espectaculos publicos
        // imprime con la timbradora
        if ($seccion == 11 || $seccion == 3 || $seccion == 6 || $seccion == 2 || $seccion == 12 || $seccion == 10 || $seccion == 4) {
            $sticket = $this->getTicketPisado($seccion);
        } else {
            $sticket = $this->getTicketFull();
        }
        */
        $sticket = $this->getTicketPisado($seccion);
        return $sticket;
    }

    public function getTicketTestigo()
    {
        // tipo = 1
        $sticket = $this->getCabecera(1);
        $sticket .= $this->getContenido(1);
        $sticket .= $this->getPie(1);

        return $sticket;
    }

    public function getTicketPisado($seccion)
    {
        // tipo = 2
        $completo = "";
        $completo .= ESC . "=" . chr(1); //Activar la impresora
        $completo .= ESC . '@'; //Inicializa
        $completo .= ESC . "c0" . chr(4); // impresion en la cinta slip - documento (timbradora);

        $sticket = $this->getCabecera(2);
        $sticket .= $this->getContenido(2);
        $sticket .= $this->getPie(2);

        //Obtengo la cantidad de lineas que me ocupa la referencia:
        $referencia = 0;
        if (array_key_exists('referencia', $this->valores)) {
            $referencia = $this->valores['referencia'];
        }
        $caracteres = strlen($referencia);
        $lineas = $caracteres / 80;
        if ($lineas != intval($lineas)) {
            $lineas = intval($lineas) + 1;
        }
        //  cementerio        o convenio      o dominio publico, o publicidad,    o inmuebles,   o espectaculos publicos
        if ($seccion == 11 || $seccion == 3 || $seccion == 12 || $seccion == 10 || $seccion == 1 || $seccion == 4) {

            $salto_1 = 10;
            $salto_2 = 30 - (4 + $lineas); //4 es la segunda parte fija, desde caja hasta el final (lugar)
            $salto_3 = 17 - (4 + $lineas);


            // Bloque 1 (talon para el contribuyente)
            $contador = 1;
            while ($contador <= $salto_1) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 2 (talon municipalidad)
            $contador = 1;
            while ($contador <= $salto_2) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 3 (talon rendicion de cuentas)
            $contador = 1;
            while ($contador <= $salto_3) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            $completo .= chr(12); // expulsa la hoja despues de timbrarla
        }

        if ($seccion == 6) { //otras tasas

            $salto_1 = 8;
            $salto_2 = 16 - (4 + $lineas); ///4 es la segunda parte fija, desde caja hasta el final (lugar)
            $salto_3 = 16 - (4 + $lineas);
            $salto_4 = 16 - (4 + $lineas);

            // Bloque 1 (talon para el contribuyente)
            $contador = 1;
            while ($contador <= $salto_1) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 2 (talon otra oficina)
            $contador = 1;
            while ($contador <= $salto_2) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 3 (talon rendicion de cuentas)
            $contador = 1;
            while ($contador <= $salto_3) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 4 (talon oficina recaudadora)
            $contador = 1;
            while ($contador <= $salto_4) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            $completo .= chr(12); // expulsa la hoja despues de timbrarla
        }

        if ($seccion == 2) { //patente

            $salto_1 = 12;
            $salto_2 = 14 - (4 + $lineas); ///4 es la segunda parte fija, desde caja hasta el final (lugar)
            $salto_3 = 14 - (4 + $lineas);
            $salto_4 = 14 - (4 + $lineas);

            // Bloque 1 (talon para el contribuyente)
            $contador = 1;
            while ($contador <= $salto_1) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 2 (talon para el municipio)
            $contador = 1;
            while ($contador <= $salto_2) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 3 (talon DGR)
            $contador = 1;
            while ($contador <= $salto_3) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            // Bloque 4 (talon para el banco)
            $contador = 1;
            while ($contador <= $salto_4) {
                $completo .= str_pad("", 1, " ", STR_PAD_BOTH) . NL;
                $contador++;
            }
            $completo .= $sticket;

            $completo .= chr(12); // expulsa la hoja despues de timbrarla
        }

        return $completo;
    }

    private function getCabecera($tipo)
    {
        $fecha = date("d-m-Y");
        $hora = date("H:i:s");
        $str = "";
        $caja = $this->cajamanager->getCaja();
        $em = $this->cajamanager->getEntityManager();
        $habilitacion = $em->getRepository('SistemaCajaBundle:Habilitacion')->findOneBy(array('usuario' => $this->cajamanager->getUsuario()));
        $seccion = "seccion desconocida";
        $detalle = $em->getRepository('SistemaCajaBundle:LoteDetalle')->findOneBy(array('id' => $this->valores['id']));
        if ($detalle) {
            $tabla = $this->barra->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
            $seccion = $em->getRepository("LarParametroBundle:LarParametro")->findOneBy(array('tabla' => $tabla, 'codigo' => $detalle->getSeccion()));
            if ($seccion) {
                $seccion = $seccion->getDescripcion();
            }
        }
        //$str .= ESC . "=" . chr(1); //Activar la impresora
        //$str .= ESC . '@'; //Inicializa
        if ($tipo == 0) {
            $str .= ESC . "=" . chr(1); //Activar la impresora
            $str .= ESC . '@'; //Inicializa
            $str .= ESC . "c0" . chr(2); // impresion full, hacia afuera (receipt)
            $str .= ESC . '!' . chr(8);
            $str .= str_pad("MUNICIPALIDAD DE POSADAS", 30, " ", STR_PAD_BOTH) . NL . ESC . 'J' . chr(20);
            $str .= str_pad("***", 30, " ", STR_PAD_BOTH) . NL . ESC . 'J' . chr(30);
            $str .= ESC . '!' . '0' . NL;
            //$str .= ESC . 'a' . chr(0); //izquierda
            $str .= ESC . '!' . chr(1);
            $str .= "DIR: " . "RIVADAVIA 1579, POSADAS, MISIONES" . NL;
            $str .= str_pad("FECHA: $fecha", 20, " ", STR_PAD_RIGHT) . str_pad("HORA: $hora", 19, " ", STR_PAD_LEFT) . NL;
            $str .= str_pad("", 40, "-") . NL;

            $str .= "CAJA: " . $caja->getNumero() . NL;
            if ($this->valores)
                $str .= "NRO. RECIBO: " . $this->valores['ticket'] . NL;

        } elseif ($tipo == 1) {
            $str .= ESC . "=" . chr(1); //Activar la impresora
            $str .= ESC . '@'; //Inicializa
            $str .= ESC . "c0" . chr(1); // impresion en la cinta testigo (jornal)
            if (isset($this->valores['titulo'])) {
                $str .= str_pad($this->valores['titulo'], 40, " ", STR_PAD_BOTH);
            }
            $str .= "CAJA: " . $caja->getNumero() . NL;
            $str .= str_pad("FECHA:" . $fecha, 20, " ", STR_PAD_RIGHT) . str_pad("HORA: " . $hora, 19, " ", STR_PAD_LEFT) . NL;
            if ($this->valores)
                $str .= "NRO. RECIBO: " . $this->valores['ticket'] . NL;

        } elseif ($tipo == 2) {
            $str .= ESC . "c0" . chr(4); // impresion en la cinta slip - documento (timbradora)
            $referencia = "";
            if (array_key_exists('referencia', $this->valores)) {
                $referencia = $this->valores['referencia'];
            }
            $str .= str_pad($seccion, 25, " ", STR_PAD_RIGHT) . str_pad($referencia, 35, " ", STR_PAD_LEFT) . NL;
            $str .= str_pad("Caja: " . $caja->getNumero() . " Cajero: " . $habilitacion->getUsuario()->getUsername(), 30, " ", STR_PAD_RIGHT) . str_pad(" Fecha: " . $fecha . " / " . $hora, 30, " ", STR_PAD_LEFT) . NL;
            /*
            if (isset($this->valores['titulo'])) {
                $str .= str_pad($this->valores['titulo'], 40, " ", STR_PAD_BOTH);
            }
            $str .= str_pad("DELEGACION: ". $habilitacion->getPuesto()->getDelegacion(), 25, " ", STR_PAD_RIGHT) . str_pad("PUESTO: " . $habilitacion->getPuesto(), 25, " ", STR_PAD_LEFT) . NL;
            $str .= str_pad("CAJA: " .  $caja->getNumero(), 25, " ", STR_PAD_RIGHT) . str_pad("CAJERO: " . $habilitacion->getUsuario()->getUsername(), 25, " ", STR_PAD_LEFT) . NL;
            $str .= str_pad("FECHA: " . $fecha, 25, " ", STR_PAD_RIGHT) . str_pad("HORA:" . $hora, 25, " ", STR_PAD_LEFT) . NL;
            if ($this->valores) {
                $str .= "NRO. RECIBO: " . $this->valores['ticket'] . NL;
            }
            */
        }
        return $str;
    }

    private function getContenido($tipo)
    {
        $str = "";
        $str .= ESC . '!' . chr(1);
        $total = 0;
        $em = $this->cajamanager->getEntityManager();
        $detalle = $em->getRepository('SistemaCajaBundle:LoteDetalle')->findOneBy(array('id' => $this->valores['id']));
        if ($tipo == 0) { //ticket full
            if (is_array($this->contenido)) {
                $str .= $this->armaDetalle($total);
                $str .= ESC . "U" . chr(1); //unidireccional
                $str .= ESC . "!" . chr(17);
                $str .= str_pad("TOTAL:", 20, " ", STR_PAD_RIGHT) . str_pad(sprintf('%9.2f', $total), 20, " ", STR_PAD_LEFT) . NL;
            } else {
                $str .= $this->contenido . NL;
            }
        }
        if ($tipo == 1) { // ticket testigo
            if (is_array($this->contenido)) {
                $str .= $this->armaDetalle($total);
                $str .= ESC . "U" . chr(1); //unidireccional
                $str .= str_pad("TOTAL:", 20, " ", STR_PAD_RIGHT) . str_pad(sprintf('%9.2f', $total), 20, " ", STR_PAD_LEFT) . NL;
            } else {
                $str .= $this->contenido . NL;
            }
        }
        if ($tipo == 2) { // timbradora (slip)
            $pagos = "";
            $lotePagos = $em->getRepository('SistemaCajaBundle:LotePago')->findBy(array('lote' => $detalle->getLote()->getId()));
            foreach ($lotePagos as $lotePago) {
                if ($pagos == "") {
                    $pagos .= $lotePago->getTipoPago();
                } else {
                    $pagos .= " - " . $lotePago->getTipoPago();
                }
            }
            $str .= str_pad("Rec: " . $this->valores['ticket'], 15, " ", STR_PAD_RIGHT) . str_pad("$ " . sprintf('%9.2f', $detalle->getImporte()), 15, " ", STR_PAD_LEFT) . str_pad(" - Pago con " . $pagos, 50, " ", STR_PAD_RIGHT);
            /*
            if (is_array($this->contenido)) {
                $str .= $this->armaDetalle($total);
                $str .= ESC . "U" . chr(1); //unidireccional
                $str .= str_pad("TOTAL:", 20, " ", STR_PAD_RIGHT) . str_pad(sprintf('%9.2f', $total), 20, " ", STR_PAD_LEFT) . NL;
            } else {
                $str .= $this->contenido . NL;
            }
            */
        }
        $str .= ESC . 'J' . chr(20); ////////DESCOMENTAR //////////////////

        return $str;
    }

    /**
     * armaDetalle
     * Crea el detalle del contenido
     * @param $total por referencia, para retornar la suma
     * @return string
     */
    private function armaDetalle(& $total)
    {
        $str = "";
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
                $str .= str_pad(sprintf("%01.2f", $reg[1]), 11, ' ', STR_PAD_LEFT) . NL;
            }
            $total += $reg[1];
        }
        return $str;
    }


    private function getPie($tipo)
    {
        $str = "";
        // $str .= ESC . "U" . chr(0); //cancelar unidireccional
        $str .= ESC . "!" . chr(1); //caracter normal
        $em = $this->cajamanager->getEntityManager();
        $habilitacion = $em->getRepository('SistemaCajaBundle:Habilitacion')->findOneBy(array('usuario' => $this->cajamanager->getUsuario()));
        if ($tipo == 0) { // IMPRESION HACIA AFUERA - FULL
            if ($this->valores)
                $str .= "COD.BARRA: " . $this->valores['codigobarra'] . NL;
            if (array_key_exists('referencia', $this->valores)) {
                $str .= "REF: " . $this->valores['referencia'] . NL;
            }
            $str .= str_pad("", 40, "-") . NL;
            $str .= str_pad("*** GRACIAS ***", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(2);
            $str .= str_pad("CAJERO: " . $habilitacion->getUsuario()->getUsername(), 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(2);
            $str .= str_pad("visite: www.posadas.gov.ar", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(1);
            $str .= str_pad("***", 40, " ", STR_PAD_BOTH) . ESC . "d" . chr(12); //posicion de corte
            $str .= ESC . 'i';
        }
        if ($tipo == 1) { //CINTA TESTIGO (JOURNAL)
            if ($this->valores)
                $str .= "COD.BARRA: " . $this->valores['codigobarra'] . NL;
            if (array_key_exists('referencia', $this->valores)) {
                $str .= "REF: " . $this->valores['referencia'] . NL;
            }
            $str .= str_pad("", 40, "-") . NL;

        }
        if ($tipo == 2) { //TIMBRADORA - SPLIT
            if ($this->valores) {
                $str .= "COD.BARRA: " . $this->valores['codigobarra'] . NL;
            }
            $str .= str_pad($habilitacion->getPuesto() . " / " . $habilitacion->getPuesto()->getDelegacion() . " / MUNICIPALIDAD DE POSADAS", 60, " ", STR_PAD_BOTH) . NL;
            /*
            if (array_key_exists('referencia', $this->valores)) {
                $str .= "REF: " . $this->valores['referencia'] . NL;
            }
            */
            ///$str .= str_pad("", 40, "-") . NL;
            //$str .= chr(12); // expulsa la hoja despues de timbrarla
        }
        return $str;
    }

}