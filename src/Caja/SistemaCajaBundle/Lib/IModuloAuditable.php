<?php
/**
 * @author fito
 * @version: 11/04/13 18:04
 *
 * Esta interfase tiene dos intenciones:
 * 1-Marcar una clase ed módulo para que pueda ser auditada
 * 2-Representar el comportamiento deseado de una clase que pueda ser auditada
 */

namespace Caja\SistemaCajaBundle\Lib;


interface IModuloAuditable {
    /**
     * @return Array, un array con los nombres de los actions excluidos
     */
    function getNoAuditables();
}