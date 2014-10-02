<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 30/09/14
 * Time: 20:28
 */

namespace Caja\GeneralBundle\Lib\Reportes;

use Caja\GeneralBundle\Lib\Imprimible;
use Caja\GeneralBundle\Lib\T;

class ImpresionCierre implements Imprimible
{
    private $plantillaReporte;
    private $apertura_id;
    private $caja_id;
    private $parametros = array();
    private $container;
    private $em;
    private $fecha_venc;

    /**
     * Enter description here...
     *
     * @param unknown_type $parametros
     */
    function setValores($parametros)
    {
        $apertura_id = $parametros['apertura_id'];
        $caja_id = $parametros['caja_id'];
        $tipo_impresion = $parametros['tipo_impresion'];
        $this->parametros = $parametros;
        $this->setAperturaId($apertura_id);
        $this->setCajaId($caja_id);
        $this->setTipoImpresion($tipo_impresion);
        $this->setNombrePlantilla();
    }

    /**
     * Setea la apertura id
     */
    private function setAperturaId($apertura_id)
    {
        $this->apertura_id = $apertura_id;
    }

    /**
     * Retorna el id de la apertura
     */
    private function getAperturaId()
    {
        return $this->apertura_id;
    }

    /**
     * Setea la caja id
     */
    private function setCajaId($caja_id)
    {
        $this->caja_id = $caja_id;
    }

    /**
     * Retorna la caja id
     */
    private function getCajaId()
    {
        return $this->caja_id;
    }

    /**
     * Retorna la apertura
     */
    private function getApertura() {
        return $this->em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $this->getAperturaId(), "caja" =>  $this->getCajaId()));
    }

    /**
     * Setea el tipo de impresion
     */
    private function setTipoImpresion($tipo_impresion)
    {
        $this->tipo_impresion = $tipo_impresion;
    }

    /**
     * Retorna el tipo de impresion
     */
    private function getTipoImpresion()
    {
        return $this->tipo_impresion;
    }

    /**
     * Retorna los valores.
     *
     */
    function getValores()
    {
        return $this->parametros;
    }

    /**
     * Funcion que setea el nombre del reporte
     */
    public function setNombrePlantilla()
    {
        $this->plantillaReporte = "reimpresionCierre.jrxml";
    }

    /**
     * Funcion que devuelve el nombre del reporte
     */
    public function getNombrePlantilla()
    {
        return $this->plantillaReporte;
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Genera el txt de la reimpresión del cierre.
     *
     */
    function generarTxt()
    {

        $datos = "titulo|numero_caja|fecha_cierre|numero_apertura|cajero|seccion|comprobante|referencia|importe|forma_cobro|importe_forma_cobro|anulado_forma_cobro|comprobantes_validos|comprobantes_anulados|importe_cobrado|importe_anulado\n";

        $em = $this->getEntityManager();
        $caja = $this->container->get('caja.manager')->getCaja();
        //$entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $this->getAperturaId(), "caja" => $this->getCajaId()));
        $apertura = $this->getApertura();
        //$servicio_tabla = $this->get("lar.parametro.tabla");

        $bm = $this->container->get("caja.barra");

        // Se ingresa una suerte de encabezado de cierre, para facilitar la division de grupos
        if ($this->getTipoImpresion() == 1) {
            $contenido = str_pad("CIERRE DE CAJA EFECTUADO", 40, "-", STR_PAD_BOTH) . NL;
        } else {
            $contenido = str_pad("REIMPRESION DE CIERRE DE CAJA", 40, "-", STR_PAD_BOTH) . NL;
        }
        $datos .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= "CAJA: " . $caja->getNumero() . NL;
        $datos .= str_pad("FECHA: " . date("d-m-Y"), 20, " ", STR_PAD_RIGHT) . str_pad("HORA: " . $apertura->getFechaCierre()->format("H:i:s"), 19, " ", STR_PAD_LEFT) . NL;

        ///////////////////////////////////////////////////////////////////////////////////////////

        //Primer parte de la impresion: detalle de pagos por tipo de seccion:
        $detalle_pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetallePagos($apertura->getId());
        $datos .= str_pad("Cierre de Caja", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Apertura nro. " . $apertura->getId(), 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Cajero: " . $caja->getCajero()->getUsername(), 40, " ", STR_PAD_BOTH) . NL;
        $nombre_seccion_actual = "";
        $monto_total_seccion = 0;
        $cantidad_comprobantes_seccion = 0;
        $monto_total_general = 0;
        $cantidad_comprobantes_general = 0;
        foreach ($detalle_pagos as $detalle) {

            $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
            $seccion = $em->getRepository("LarParametroBundle:LarParametro")->findOneBy(array('tabla' => $tabla, 'codigo' => $detalle->getSeccion()));
            //$seccion = $servicio_tabla->getParametro($tabla, $detalle->getSeccion());
            if ($seccion) {
                $nombre_seccion = $seccion->getDescripcion();
            } else {
                $nombre_seccion = "seccion desconocida";
            }

            //Pregunto si es la misma seccion, o tengo que hacer el "cambio" (corte de control)
            if ($nombre_seccion_actual == "") { //entra la primera vez
                $datos .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
                $datos .= str_pad("SECCION: " . $nombre_seccion, 40, " ", STR_PAD_BOTH) . NL;
                //$datos .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $detalle->getReferencia();
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion += $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                }
                $datos .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                $nombre_seccion_actual = $nombre_seccion;

                $cantidad_comprobantes_seccion++;
            } else if ($nombre_seccion == $nombre_seccion_actual) { //entra si es igual al anterior
                //$datos .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $detalle->getReferencia();
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion += $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                }
                $datos .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
                $seccion_actual = $em->getRepository("LarParametroBundle:LarParametro")->findOneBy(array('tabla' => $tabla, 'codigo' => $detalle->getSeccion()));
                if ($seccion_actual) {
                    $nombre_seccion_actual = $seccion_actual->getDescripcion();
                } else {
                    $nombre_seccion_actual = "seccion desconocida";
                }

                $cantidad_comprobantes_seccion++;
            } else { //corte de control, immprimo una linea, muestro totales, otra linea y empiezo otra seccion:
                $datos .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL;
                $datos .= str_pad($nombre_seccion_actual . ": $ " . sprintf("%9.2f", $monto_total_seccion), 40, " ", STR_PAD_BOTH) . NL;
                $datos .= str_pad("Comprobantes: " . $cantidad_comprobantes_seccion, 40, " ", STR_PAD_BOTH) . NL;
                $datos .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
                $monto_total_general += $monto_total_seccion;
                $cantidad_comprobantes_general += $cantidad_comprobantes_seccion;
                $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
                $datos .= str_pad("SECCION: " . $nombre_seccion, 40, " ", STR_PAD_BOTH) . NL;
                //$datos .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $detalle->getReferencia();
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion = $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                    $monto_total_seccion = 0;
                }
                $datos .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                //INICIALIZO LOS ACUMULADORES DE SECCION

                $cantidad_comprobantes_seccion = 1;

                $seccion_actual = $em->getRepository("LarParametroBundle:LarParametro")->findOneBy(array('tabla' => $tabla, 'codigo' => $detalle->getSeccion()));
                if ($seccion_actual) {
                    $nombre_seccion_actual = $seccion_actual->getDescripcion();
                } else {
                    $nombre_seccion_actual = "seccion desconocida";
                }
            }
        }
        $monto_total_general += $monto_total_seccion;
        $cantidad_comprobantes_general += $cantidad_comprobantes_seccion;
        $datos .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad($nombre_seccion_actual . ": $ " . sprintf("%9.2f", $monto_total_seccion), 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Comprobantes: " . $cantidad_comprobantes_seccion, 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
        $datos .= str_pad("TOTAL COBRADO: $ " . sprintf("%9.2f", $monto_total_general), 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("CANTIDAD DE COMPROBANTES: " . $cantidad_comprobantes_general, 40, " ", STR_PAD_BOTH) . NL;

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Segunda parte de la impresion: detalle de pagos por tipo de pago:
        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($apertura->getId());
        $tipoPagos = array();
        foreach ($PagoTipoPago as $tipo) {
            if (!array_key_exists($tipo['id'], $tipoPagos)) {
                $tipoPagos[$tipo['id']] = array($tipo['descripcion'], 0, 0);
            }
            $tipoPagos[$tipo['id']][1] = $tipo['importe'] + $tipo['anulado'];
            $tipoPagos[$tipo['id']][2] = $tipo['anulado'];

        }

        $datos .= str_pad("=", 40, "=", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Formas de Cobro: ", 40, " ", STR_PAD_RIGHT) . NL;
        foreach ($tipoPagos as $tipoPago) {
            $datos .= str_pad($tipoPago[0] . ": ", 40, " ", STR_PAD_RIGHT) . NL;
            $datos .= str_pad("$ " . sprintf("%9.2f", $tipoPago[1]) . " - Anulado: $ " . sprintf("%9.2f", $tipoPago[2]), 40, "-", STR_PAD_LEFT) . NL;

        }

        ///////////////////////////////////////////////////////////////////////////
        //Tercer parte de la impresion: cantidad de comprobantes y montos finales:

        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($apertura->getId());
        $pagosAnulado = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($apertura->getId());
        //$ticket = $this->get("sistemacaja.ticket");
        $datos .= str_pad("=", 40, "=", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Apertura nro. " . $apertura->getId(), 40, " ", STR_PAD_BOTH) . NL;
        $datos .= str_pad("Comprobantes Validos: " . $apertura->getComprobanteCantidad(), 40, " ", STR_PAD_RIGHT) . NL;
        $datos .= str_pad("Comprobantes Anulados: " . $apertura->getComprobanteAnulado(), 40, " ", STR_PAD_RIGHT) . NL;
        $datos .= str_pad("Importe Cobrado: $ " . sprintf("%9.2f", $pagos), 40, " ", STR_PAD_RIGHT) . NL;
        $datos .= str_pad("Importe Anulado: $ " . sprintf("%9.2f", $pagosAnulado), 40, " ", STR_PAD_RIGHT) . NL;


        // EL RESUMEN SE HACE SOLO EN EL CASO NORMAL (NO REIMPRESION)
        if ($this->getTipoImpresion() == 1) {
            //Genero una segunda parte de la impresion, que va hacia afuera, a modo de resumen para el cajero:
            $ticket_resumen = $this->get("sistemacaja.ticket");
            $contenido_resumen = str_pad("=", 40, "=", STR_PAD_BOTH) . NL; //doble linea
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("RESUMEN DE CIERRE DE CAJA", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Apertura nro. " . $apertura->getId(), 40, " ", STR_PAD_BOTH) . NL;
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Comprobantes Validos: " . $apertura->getComprobanteCantidad(), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Comprobantes Anulados: " . $apertura->getComprobanteAnulado(), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Importe Cobrado: $ " . sprintf("%9.2f", $pagos), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Importe Anulado: $ " . sprintf("%9.2f", $pagosAnulado), 40, " ", STR_PAD_RIGHT) . NL;

            //Se agrega un resumen por tipo de cobro:
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Formas de Cobro: ", 40, " ", STR_PAD_RIGHT) . NL;
            foreach ($tipoPagos as $tipoPago) {
                $contenido_resumen .= str_pad($tipoPago[0] . ": ", 40, " ", STR_PAD_RIGHT) . NL;
                $contenido_resumen .= str_pad("$ " . sprintf("%9.2f", $tipoPago[1]) . " - Anulado: $ " . sprintf("%9.2f", $tipoPago[2]), 40, "-", STR_PAD_LEFT) . NL;
            }

            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("_________________________", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("RECIBIDO POR JEFE DE CAJA", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("DIRECCION DE TESORERIA - MUN. POSADAS", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $datos .= $contenido_resumen;
        }

        return utf8_encode($datos);


    }


} 