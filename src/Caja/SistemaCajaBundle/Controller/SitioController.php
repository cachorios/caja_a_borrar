<?php

namespace Caja\SistemaCajaBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SitioController extends Controller
{
    public function estaticaAction($pagina)
    {
        return $this->render('SistemaCajaBundle:Sitio:'.$pagina.'.html.twig');
    }
}