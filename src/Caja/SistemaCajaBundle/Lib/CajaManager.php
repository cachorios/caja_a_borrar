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
	private $caja;

	public function __construct($contenedor)
	{
		$this->contenedor = $contenedor;
	}


	/**
	 * @return \Caja\SistemaCajaBundle\Entity\Caja
	 */
	public function getCaja()
	{

		if($this->contenedor->get("security.context")->getToken()->isAuthenticated()) {

			$usuario = $this->contenedor->get("security.context")->getToken()->getUser();

			$idUsuario = $usuario->getId();


			try {
				if($this->caja == null) {
					$caja = $this->contenedor->get("doctrine.orm.entity_manager")->getRepository("SistemaCajaBundle:Caja")->getCajaUsuario($idUsuario);
						//find(1);
						//getCajaUsuario($idUsuario);
					$this->caja = $caja;
				} else {
					$caja = $this->caja;
				}

				//findOneBy(array("cajero" =>$idUsuario ));
			} catch(Symfony\Component\Config\Definition\Exception\Exception $e) {
				die("No recupero!");
			}


			return $caja;
		}
	}

	/**
	 * getApertura
	 * @return \Caja\SistemaCajaBundle\Entity\Apertura
	 */
	public function getApertura()
	{
		$caja_id = null; //caja por defecto
        if ($this->getCaja()) {
            $caja_id = $this->getCaja()->getId();
        }

		$apertura = $this->contenedor->get("doctrine.orm.entity_manager")->getRepository("SistemaCajaBundle:Apertura")->findOneBy(array("caja" => $caja_id, 'fecha_cierre' => null));

		return $apertura;
	}


	public function esCajero()
	{
		$caja = $this->getCaja();
		return !$caja == null;
	}


}
