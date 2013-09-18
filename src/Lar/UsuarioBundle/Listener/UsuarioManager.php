<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 18/09/13
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */

namespace Lar\UsuarioBundle\Listener;



use Lar\UsuarioBundle\Entity\LogIngreso;
use Lar\UsuarioBundle\Entity\Usuario;
use Symfony\Component\DependencyInjection\Container;


class UsuarioManager
{
    /**
     * @var Usuario $usuario
     */
    var $usuario;
    /**
     * @var Container $contenedor
     */
    var $contenedor;


    public function __construct(Container $container)
    {
            $this->contenedor = $container;
    }


    public function RegistrarIngreso(Usuario $usuario){
        $this->usuario = $usuario;

        if($this->verificarIngreso())
        {
            $this->guardarIngreso("Ingreso Valido");
            return true;
        }

        $this->guardarIngreso("Ingreso RECHAZADO");
        return false;

    }


    private function verificarIngreso()
    {


        if ($this->contenedor->get('security.context')->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->esDiaValido(date("w")) && $this->esHorarioValido() ) {
            if($this->usuario->getUsuarioIngreso()->getLugarIngreso()){
                return $this->verificarLugar();
            }
            return true;
        }
        return false;
    }

    /**
     * esDiaValido
     *  parametro nro de dia
     * @param $d
     * @return boolean;
     */
    private function esDiaValido($d)
    {
        $ingregso = $this->usuario->getUsuarioIngreso();

        return $d == 0 && $ingregso->getDomingo() ||
        $d == 1 && $ingregso->getLunes() ||
        $d == 2 && $ingregso->getMartes() ||
        $d == 3 && $ingregso->getMiercoles() ||
        $d == 4 && $ingregso->getJueves() ||
        $d == 5 && $ingregso->getViernes() ||
        $d == 6 && $ingregso->getSabado();
    }

    private function esHorarioValido()
    {
        $ingregso = $this->usuario->getUsuarioIngreso();
        if($ingregso->getHorario()) {
            $ahora = (new \DateTime('now'))->setTime(0,0,0);
            $desde = $ingregso->getHorario()->getDesde();
            $hasta = $ingregso->getHorario()->getHasta();
            return $ahora >= $desde && $ahora <= $hasta;
        }
        return false;
    }

    /**
     *
     * @return bool
     */
    private function verificarLugar()
    {
        $lugares = $this->getLugares();

        foreach ($lugares as $lugar) {
            $mascara_valida = $lugar->getMascara();
            $mascara_ip_origen = substr($this->contenedor->get('request')->getClientIp(), strlen($mascara_valida));

            if ($mascara_valida == $mascara_ip_origen) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return Collection
     */
    private function getLugares()
    {
        return $this->contenedor->get('doctrine.orm.entity_manager')->getRepository('UsuarioBundle:LugarIngreso')->findAll();
    }

    private function guardarIngreso($msg){
        $ip = $this->contenedor->get('request')->getClientIp();
        $log = new LogIngreso($this->usuario,$ip,$msg);

        $em = $this->contenedor->get('doctrine.orm.entity_manager');

        $em->persist($log);
        $em->flush();
    }

}