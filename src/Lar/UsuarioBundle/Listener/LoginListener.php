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
class LoginListener {
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        $usuario = $event->getAuthenticationToken()->getUser();
        $algo = $usuario;

    }
}