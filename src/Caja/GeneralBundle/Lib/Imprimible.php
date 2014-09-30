<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace MP\GeneralBundle\Lib;

/**
 *
 * @author fito
 */
interface Imprimible {
   
	/**
	 * Setea los parametros
	 *
	 * @param array $parametros
	 */
	function setValores($parametros);
   
	/**
	 * Devuelve el array de parametros
	 *
	 */
   	function getValores();

    /**
     * @param $em
     * @return mixed
     */
    function setEntityManager($em);

    /**
     * @return mixed
     */
    function getEntityManager();
   
   	/**
   	 * Genera el origen de datos
   	 *
   	 */
   	function generarTxt();
   
	/**
	 * Funcion que setea el nombre del reporte
	 */
	function setNombrePlantilla();
	
	/**
	 * Funcion que devuelve el nombre del reporte
	 */
	public function getNombrePlantilla();
}
?>
