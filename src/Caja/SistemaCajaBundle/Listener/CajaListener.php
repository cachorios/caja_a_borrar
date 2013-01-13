<?php
namespace Caja\SistemaCajaBundle\Listener;
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 05/01/13
 * Time: 14:31
 * To change this template use File | Settings | File Templates.
 */
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class CajaListener
{
    protected $contenedor;

    public function __construct($cnt = null){
        $this->contenedor = $cnt;
    }

    public function postLoad(LifecycleEventArgs $ag){

    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $tk = $event->getAuthenticationToken();
        $usuario = $tk->getUser();

        $usuario->setUltimoIngreso(new \DateTime() );
        $usuario->setPassword(null);

        $em = $this->contenedor->get('doctrine.orm.entity_manager');

        $em->persist($usuario);
        $em->flush();

        //$this->contenedor->get("caja.manager")->setCaja();



    }

}

