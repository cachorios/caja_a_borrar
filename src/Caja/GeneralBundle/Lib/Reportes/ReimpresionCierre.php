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

class ReimpresionCierre implements Imprimible
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
        $this->parametros = $parametros;
        $this->setAperturaId($apertura_id);
        $this->setCajaId($caja_id);
        $this->setNombrePlantilla();
    }

    /**
     * Setea la tasa
     */
    private function setAperturaId($apertura_id)
    {
        $this->apertura_id = $apertura_id;
    }

    /**
     * Retorna la tasa
     */
    private function getAperturaId()
    {
        return $this->apertura_id;
    }

    /**
     * Setea la tasa
     */
    private function setCajaId($caja_id)
    {
        $this->caja_id = $caja_id;
    }

    /**
     * Retorna la tasa
     */
    private function getCajaId()
    {
        return $this->caja_id;
    }

    private function getApertura() {
        $entity = $this->em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('apertura_id' => $id, "caja_id" => $caja->getId()));
    }

    /**
     * Enter description here...
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
     * Enter description here...
     *
     */
    function generarTxt()
    {

        $datos = "";

        $datos = $this->generaCabecera();
        $datos = $this->generaLineas();

    }

    public function generaCabecera(){

        return "";
    }

    public function generaLineas(){
        return "";
    }
} 