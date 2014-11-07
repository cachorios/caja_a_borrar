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
    private $reporte;

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
        $reporte = $parametros['reporte'];
        $this->parametros = $parametros;
        $this->setAperturaId($apertura_id);
        $this->setCajaId($caja_id);
        $this->setTipoImpresion($tipo_impresion);
        $this->setReporte($reporte);
        $this->setNombrePlantilla();
    }

    /**
     * Setea el tipo de reporte a generar
     */
    private function setReporte($reporte)
    {
        $this->reporte = $reporte;
    }

    /**
     *Indica el tipo de reporte a generar
     */
    private function getReporte()
    {
        return $this->reporte;
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
    private function getApertura()
    {
        return $this->em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $this->getAperturaId(), "caja" => $this->getCajaId()));
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
        if ($this->getReporte() == "cierre"){
            $this->plantillaReporte = "cierre_caja.jrxml";
        }else{
            $this->plantillaReporte = "cierre_arqueo.jrxml";
        }

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
     * Genera el txt de la reimpresi�n del cierre.
     *
     */
    function generarTxt() {

        //$datos = "titulo|numero_caja|fecha_cierre|hora_cierre|numero_apertura|cajero|forma_cobro1|importe_forma_cobro1|anulado_forma_cobro1|forma_cobro2|importe_forma_cobro2|anulado_forma_cobro2|forma_cobro3|importe_forma_cobro3|anulado_forma_cobro3|forma_cobro4|importe_forma_cobro4|anulado_forma_cobro4|comprobantes_validos|comprobantes_anulados|importe_cobrado|importe_anulado|seccion|comprobante|referencia|importe|observacion\n";
        $datos = "titulo|numero_caja|ubicacion|fecha_apertura|hora_apertura|fecha_cierre|hora_cierre|numero_apertura|cajero|comprobantes_validos|comprobantes_anulados|importe_cobrado|importe_anulado|forma_cobro1|importe_forma_cobro1|anulado_forma_cobro1|forma_cobro2|importe_forma_cobro2|anulado_forma_cobro2|forma_cobro3|importe_forma_cobro3|anulado_forma_cobro3|forma_cobro4|importe_forma_cobro4|anulado_forma_cobro4|forma_cobro5|importe_forma_cobro5|anulado_forma_cobro5|seccion|comprobante|referencia|importe|observacion\n";

        $em = $this->getEntityManager();
        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->getApertura();
        $bm = $this->container->get("caja.barra");
        $detalle_pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetallePagos($apertura->getId());
        $habilitacion = $em->getRepository('SistemaCajaBundle:Habilitacion')->findOneBy(array('id' => $apertura->getHabilitacion()->getId()));
        $datos_apertura = "CIERRE DE CAJA" . "|"; //TITULO
        /*
        // Se ingresa una suerte de encabezado de cierre, para facilitar la division de grupos
        if ($this->getTipoImpresion() == 1) {
            $datos_apertura = "CIERRE DE CAJA EFECTUADO" . "|"; //TITULO
        } else {
            $datos_apertura = "REIMPRESION DE CIERRE DE CAJA" . "|"; //TITULO
        }
        */
        $datos_apertura .= $caja->getNumero() . "|"; //numero_caja
        $datos_apertura .= $apertura->getHabilitacion()->getPuesto()->getDescripcion() . "|"; //ubicacion
        $datos_apertura .= $apertura->getFecha()->format("d/m/Y") . "|"; //fecha_apertura
        $datos_apertura .= $apertura->getFecha()->format("H:i:s") . "|"; //hora_apertura
        $datos_apertura .= $apertura->getFechaCierre()->format("d/m/Y") . "|"; //fecha_cierre
        $datos_apertura .= $apertura->getFechaCierre()->format("H:i:s") . "|"; //hora_cierre
        $datos_apertura .= $apertura->getId() . "|"; //numero_apertura
        $datos_apertura .= $habilitacion->getUsuario()->getUsername() . "|"; //cajero

        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($apertura->getId());
        $pagosAnulado = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($apertura->getId());
        $datos_comprobantes = $apertura->getComprobanteCantidad() . "|"; //comprobantes_validos
        $datos_comprobantes .= $apertura->getComprobanteAnulado() . "|"; //comprobantes_anulados
        $datos_comprobantes .= strtr(sprintf('%1.2f', $pagos), '.', ',') . "|"; //importe_cobrado
        $datos_comprobantes .= strtr(sprintf('%1.2f', $pagosAnulado), '.', ',') . "|"; //importe_anulado


        ///////////////////////////////////////////////////////////////////////////////////////////
        //siguiente parte de la impresion: detalle de pagos por tipo de pago:
        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($apertura->getId());
        $tipoPagos = array();
        foreach ($PagoTipoPago as $tipo) {
          if (!array_key_exists($tipo['id'], $tipoPagos)) {
              $tipoPagos[$tipo['id']] = array($tipo['descripcion'], 0, 0);
          }
          $tipoPagos[$tipo['id']][1] = $tipo['importe'] + $tipo['anulado'];
          $tipoPagos[$tipo['id']][2] = $tipo['anulado'];
          $tipoPagos[$tipo['id']][3] = $tipo['id'];
        }

        $cantidad_pagos = count($tipoPagos);
        $formas_pago = "";
        foreach ($tipoPagos as $tipoPago) {
            $formas_pago .= $tipoPago[0] . "|"; //forma_cobro
            $formas_pago .= strtr(sprintf('%1.2f', $tipoPago[1]), '.', ',') . "|"; //importe_forma_cobro
            $formas_pago .= strtr(sprintf('%1.2f', $tipoPago[2]), '.', ',') . "|"; //anulado_forma_cobro
        }
        //Relleno los tipos faltantes, deben ser 5 en total:
        while ($cantidad_pagos < 5) { //relleno hasta completar
            $formas_pago .= "|||";
            $cantidad_pagos ++;
        }

        foreach ($detalle_pagos as $detalle) {
            ////$datos_apertura/////
            $datos .= $datos_apertura;
            ////$datos_apertura/////

            ////$datos_comprobantes/////
            $datos .= $datos_comprobantes;
            ////$datos_comprobantes/////

            ////$formas_pago/////
            $datos .= $formas_pago;
            ////$formas_pago/////

            //////////////////////inicio detalle///////////////////
            $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
            $seccion = $em->getRepository("LarParametroBundle:LarParametro")->findOneBy(array('tabla' => $tabla, 'codigo' => $detalle->getSeccion()));
            if ($seccion) {
                $datos .= $seccion->getDescripcion() . "|";
            } else {
                $datos .= "seccion desconocida" . "|";
            }

            $datos .= $detalle->getComprobante() . "|"; //cajero
            $datos .= $detalle->getReferencia() . "|"; //referencia
            //$datos .= strtr(sprintf('%1.2f', $detalle->getImporte()), '.', ',') . "|"; //importe
            if ($detalle->getAnulado()) {
                $datos .= strtr(sprintf('%1.2f', 0), '.', ',') . "|"; //importe
                $datos .= "ANULADO"; //observacion
            } else {
                $datos .= strtr(sprintf('%1.2f', $detalle->getImporte()), '.', ',') . "|"; //importe
                $datos .= ""; //observacion
            }
            //////////////////////fin detalle///////////////////

            $datos .= "\n"; //salto de linea
        }
        return utf8_encode($datos);
    }
}