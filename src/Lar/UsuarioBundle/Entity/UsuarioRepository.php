<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 13/10/12
 * Time: 19:13
 * To change this template use File | Settings | File Templates.
 */
namespace Lar\UsuarioBundle\Entity;


use Doctrine\ORM\EntityRepository;

class UsuarioRepository extends EntityRepository
{
    public function findUsuarios()
    {
        $em = $this->getEntityManager();
        $c = $this->createQueryBuilder('u');

        return $c;
    }

}