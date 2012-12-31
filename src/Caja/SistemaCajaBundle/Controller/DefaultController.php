<?php

namespace Caja\SistemaCajaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CajaBundle:Default:index.html.twig', array('name' => $name));
    }
}
