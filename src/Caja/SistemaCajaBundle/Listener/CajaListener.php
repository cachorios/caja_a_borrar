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
        $tk = $this->contenedor->get('security.context')->getToken();
        $usuario = $tk->getUser();
        //$usuario = $event->getAuthenticationToken()->getUser();
        $usuario->getContenedor()->set('lar',"Luis A. Rios");
        $tk->setUser($usuario);

        $this->contenedor->get('security.context')->setToken($tk);


    }

}

