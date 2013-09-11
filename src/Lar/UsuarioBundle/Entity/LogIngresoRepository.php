<?php

namespace Lar\UsuarioBundle\Entity;


use Doctrine\ORM\EntityRepository;

class LogIngresoRepository extends EntityRepository
{

    /**
     * @param $usuario
     * @param $valido
     */

    public function guardarRegistro($usuario, $valido) {
        $em = $this->getEntityManager();
        $logaudit = new LogIngreso();
        $logaudit->setUsuario($usuario);
        $logaudit->setFecha(new \DateTime());
        if ($valido) {
            $logaudit->setDescripcion('INGRESO VALIDO:' . $usuario->getNombre() . ":" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER['REMOTE_ADDR']);
        } else {
            $logaudit->setDescripcion('INGRESO RECHAZADO:' . $usuario->getNombre() . ":" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER['REMOTE_ADDR']);
        }
        $em->persist($logaudit);
        $em->flush();
    }

    public function prueba() {
        return true;
    }
}