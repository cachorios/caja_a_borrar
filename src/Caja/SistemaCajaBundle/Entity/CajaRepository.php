<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\EntityRepository;


/**
 * CajaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CajaRepository extends EntityRepository
{
    public function findCajas()
    {
        $c = $this->createQueryBuilder('c');
        $c->join("c.cajero","t");

        return $c;
    }

	public function getCajaUsuario($usuario_id)
	{
		$em = $this->getEntityManager();
        $q = $em->createQuery("
        	SELECT c
              FROM SistemaCajaBundle:Caja c
             WHERE c.cajero = :cajero")
			->setParameter("cajero", $usuario_id)
		;

		$res = $q->getSingleResult();

		return $res;
	}



}
