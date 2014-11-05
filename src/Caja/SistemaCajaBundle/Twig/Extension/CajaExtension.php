<?php

namespace Caja\SistemaCajaBundle\Twig\Extension;
class CajaExtension extends \Twig_Extension
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'lar.usuario';
    }

    public function getFunctions()
    {
        return array(
            'esCajero' => new \Twig_Function_Method($this, 'esCajero'),
            'tieneApertura' => new \Twig_Function_Method($this, 'tieneApertura')
        );
    }


    public function esCajero()
    {
        return $this->container->get("caja.manager")->esCajero();
    }

    public function tieneApertura()
    {
        return !$this->container->get("caja.manager")->getApertura() == null;
    }

}