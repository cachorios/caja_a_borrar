<?php

namespace Caja\ComercioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ComercioBundle:Default:index.html.twig', array('name' => $name));
    }
}
