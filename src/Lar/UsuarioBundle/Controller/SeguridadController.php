<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 07/11/12
 * Time: 09:57
 * To change this template use File | Settings | File Templates.
 */
namespace Lar\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SeguridadController    extends Controller
{
    public  function loginAction(){
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();

        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('UsuarioBundle:Seguridad:login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));

    }
}
