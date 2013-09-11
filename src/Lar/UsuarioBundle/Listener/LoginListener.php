<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrador
 * Date: 02/05/13
 * Time: 19:53
 * To change this template use File | Settings | File Templates.
 */
namespace Lar\UsuarioBundle\Listener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lar\UsuarioBundle\Entity\UsuarioIngreso;
class LoginListener {

    protected $contenedor;
    public function __construct($cnt = null)
    {
        $this->contenedor = $cnt;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        $em = $this->contenedor->get('doctrine.orm.entity_manager');
        $ingreso_valido = $em->getRepository('UsuarioBundle:Usuario')->validarIngreso($event->getAuthenticationToken()->getUser());
        if (!$ingreso_valido) {
            throw new BadCredentialsException('Ingreso rechazado. Dia, horario o lugar no permitidos.', 0);
            $event->stopPropagation();
        }
    }
}