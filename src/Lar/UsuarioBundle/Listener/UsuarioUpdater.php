<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 01/11/12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
namespace Lar\UsuarioBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;


class UsuarioUpdater
{

    private $encoder_fact;

    public function __construct($arg_enc=null)
    {

        $this->encoder_fact = $arg_enc ;

    }


    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em= $args->getEntityManager();

        if ($entity instanceof \Lar\UsuarioBundle\Entity\Usuario) {

            $old = $em->getUnitOfWork()->getEntityChangeSet($entity);

            if (null == $entity->getPassword()) {
                $entity->setPassword($old['password'][0]);
            }else{
                $entity->setPassword($this->encoder_fact->getEncoder($entity)->encodePassword($entity->getPassword(), $entity->getSalt()));
            }
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)),$entity);
        }
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        //$entityManager = $args->getEntityManager();

        if ($entity instanceof \Lar\UsuarioBundle\Entity\Usuario) {

            $entity->setFechaAlta(new \DateTime());
            $salt = md5(time());
            $entity->setSalt($salt);
            $entity->setPassword(
                    $this->encoder_fact->getEncoder($entity)
                        ->encodePassword($entity->getPassword(), $entity->getSalt()
                    )
            );

       }
    }


 }
