<?php
namespace Caja\SistemaCajaBundle\Lib;
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 12/01/13
 * Time: 01:02
 * To change this template use File | Settings | File Templates.
 */
class CajaManager
{

    private $contenedor;

    public function __construct($contenedor)
    {
        $this->contenedor = $contenedor;
    }


    /**
    * @return \Caja\SistemaCajaBundle\Entity\Caja
    */
    public function getCaja(){

        $usuario = $this->contenedor->get("security.context")->getToken()->getUser();

        $caja = $this->contenedor->get("doctrine.orm.entity_manager")->getRepository("SistemaCajaBundle:Caja")->findOneBy(array("cajero" => $usuario->getId()));
        return $caja;
    }

    /**
    * @return \Caja\SistemaCajaBundle\Entity\Apertura
    */
    public function getApertura()
    {
        $caja_id = $this->getCaja()->getId();
        $apertura = $this->contenedor->get("doctrine.orm.entity_manager")->getRepository("SistemaCajaBundle:Apertura")->findOneBy(array("caja" => $caja_id, 'fecha_cierre' => null));

        return $apertura;
    }

    public function esCajero(){
        $caja = $this->getCaja();
        return !$caja == null;
    }


}
