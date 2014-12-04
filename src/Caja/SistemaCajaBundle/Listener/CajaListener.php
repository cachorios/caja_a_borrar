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
        $this->logIn = 1;
        $usuario->setUltimoIngreso(new \DateTime());
        $usuario->setPassword(null);

        $em = $this->contenedor->get('doctrine.orm.entity_manager');

        $em->persist($usuario);
        $em->flush();

    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->logIn == 1) {
            if ($this->contenedor->get("caja.manager")->esCajero()) {//tiene rol de cajero y habilitacion activa
                $apertura = $this->contenedor->get("caja.manager")->getApertura();
                //Si tiene apertura mostrar el monitor, sino, que cree una apertura
                if ($apertura) {
                    $toRedirect = $this->contenedor->get("router")->generate('apertura_monitor');
                } else {
                    $toRedirect = $this->contenedor->get("router")->generate('apertura_new');
                }

            } else if (($this->contenedor->get('security.context')->isGranted('ROLE_ADMIN')) || ($this->contenedor->get('security.context')->isGranted('ROLE_JEFE_CAJA'))) {
                //es administrador O JEFE DE CAJA
                $toRedirect = $this->contenedor->get("router")->generate('apertura');
            } else { // seria para el caso de un usuario comun sin caja asignada o sin habilitacion vigente
                //throw new BadCredentialsException('Ingreso rechazado. El usuario no tiene caja asignada', 0);
                //$event->stopPropagation();
                $toRedirect = $this->contenedor->get("router")->generate('apertura');
            }
            $event->setResponse(new RedirectResponse($toRedirect));
            $event->stopPropagation();
        }

    }

}