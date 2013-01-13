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
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CajaListener
{
    protected $contenedor;
    private $logIn;
    public function __construct($cnt = null)
    {
        $this->contenedor = $cnt;
        $this->logIn = 0;
    }

    public function postLoad(LifecycleEventArgs $ag)
    {

    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $tk = $event->getAuthenticationToken();
        $usuario = $tk->getUser();
        $this->logIn=1;
        $usuario->setUltimoIngreso(new \DateTime());
        $usuario->setPassword(null);

        $em = $this->contenedor->get('doctrine.orm.entity_manager');

        $em->persist($usuario);
        $em->flush();


    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        if($this->logIn == 1){
            $caja = $this->contenedor->get("caja.manager")->getCaja();
            if ($caja) {
                //es cajero
                $apertura = $this->contenedor->get("caja.manager")->getApertura();
                //Si tiene apertura mostrar el monitor, sino, que cree una apertura
                if($apertura){
                    $toRedirect = $this->contenedor->get("router")->generate('apertura_monitor') ;
                }else{
                    $toRedirect = $this->contenedor->get("router")->generate('apertura_new') ;
                }

            } else {
                //mostrar el home de no cajeros
                $toRedirect = $this->contenedor->get("router")->generate('usuario') ;
            }
            $event->setResponse(new RedirectResponse($toRedirect));
            $event->stopPropagation();
        }

    }


}

