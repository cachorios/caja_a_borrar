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
        if ($this->contenedor->get("security.context")->getToken()->isAuthenticated()) {
            //$usuario = $this->contenedor->get("security.context")->getToken()->getUser();
            //$idUsuario = $usuario->getId();

            try {
                if ($this->caja == null) {
                    //Primero obtengo la apertura:
                    $apertura = $this->getApertura();
                    if ($apertura) {
                        $caja = $apertura->getHabilitacion()->getCaja();
                    } else {
                        $caja = null;
                    }
                    $this->caja = $caja;
                } else {
                    $caja = $this->caja;
                }

            } catch (Symfony\Component\Config\Definition\Exception\Exception $e) {
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
        $usuario = $this->contenedor->get("security.context")->getToken()->getUser();
        $consulta = $this->contenedor->get("doctrine.orm.entity_manager")
            ->createQuery("SELECT a
                FROM SistemaCajaBundle:Apertura a JOIN a.habilitacion h
                WHERE a.fecha_cierre is null
                AND h.usuario = :usuario_id")
            ->setParameter("usuario_id", $usuario->getId())
            ->setMaxResults(1);
        try {
            $apertura = $consulta->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            //No trajo resultados:
            return null;
        } catch (\Doctrine\Orm\NonUniqueResultException $e) {
            //Si devolvio una excepcion es porque trajo mas de un resultado,
            // o sea es un error grave haber cobrado mas de una vez un comprobante
            return null;
        }
        return $apertura;
    }


    public function esCajero()
    {
        //Pregunto si tiene el rol de cajero
        if (!$this->contenedor->get('security.context')->isGranted('ROLE_USUARIO')) {
            return false;
        }

        $usuario = $this->getUsuario();
        // determinar si el usuario logueado tiene habilitacion vigente

        $consulta = $this->contenedor->get("doctrine.orm.entity_manager")
            ->createQuery("SELECT h
                FROM SistemaCajaBundle:Habilitacion h
                WHERE h.hasta is null

                AND h.usuario = :usuario_id")
            ->setParameter("usuario_id", $usuario)
            //->setParameter("hoy", $usuario)
            //->setParameter("hasta", $usuario)
            //and h.desde BETWEEN campo2 and campo3
            ->getResult();

        if (count($consulta)>0) {
            return true;
        } else {
            return false;
        }
    }


    public function getEntityManager()
    {
        return $this->contenedor->get("doctrine.orm.entity_manager");
    }

    public function getUsuario()
    {
        return $usuario = $this->contenedor->get("security.context")->getToken()->getUser();
    }
}
