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


    public function registrarIngreso(Usuario $usuario)
    {
        $this->usuario = $usuario;

        if ($this->verificarIngreso()) {
            $this->guardarIngreso("Ingreso Valido");
            return true;
        }

        $this->guardarIngreso("Ingreso RECHAZADO");
        return false;

    }

    /*
     * Controlo que tenga un rol asignado para poder ingresar al sistema:
     */
    public function validarRol(Usuario $usuario)
    {
        $grupos = $usuario->getGrupos();
        if (count($grupos) > 0) {
            return true;
        }

        $this->guardarIngreso("Ingreso RECHAZADO. Perfil incompleto");
        return false;

    }

    private function verificarIngreso()
    {
        if (($this->contenedor->get('security.context')->isGranted('ROLE_ADMIN')) || ($this->contenedor->get('security.context')->isGranted('ROLE_JEFE_CAJA'))) {
            return true;
        }
        if ($this->esDiaValido(date("w")) && $this->esHorarioValido()) {

            if ($this->usuario->getUsuarioIngreso()->getLugarIngreso()) {
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
        $ingreso = $this->usuario->getUsuarioIngreso();

        if (!$ingreso) return false;

        return $d == 0 && $ingreso->getDomingo() ||
        $d == 1 && $ingreso->getLunes() ||
        $d == 2 && $ingreso->getMartes() ||
        $d == 3 && $ingreso->getMiercoles() ||
        $d == 4 && $ingreso->getJueves() ||
        $d == 5 && $ingreso->getViernes() ||
        $d == 6 && $ingreso->getSabado();
    }

    private function esHorarioValido()
    {
        $ingreso = $this->usuario->getUsuarioIngreso();
        if ($ingreso->getHorario()) {
            $ahora = strtotime('now');
            $desde = strtotime($ingreso->getHorario()->getDesde());
            $hasta = strtotime($ingreso->getHorario()->getHasta());
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
            $mascara_ip_origen = substr($this->contenedor->get('request')->getClientIp(), 0, strlen($mascara_valida));
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

    private function guardarIngreso($msg)
    {
        $ip = $this->contenedor->get('request')->getClientIp();
        $log = new LogIngreso($this->usuario, $ip, $msg);

        $em = $this->contenedor->get('doctrine.orm.entity_manager');

        $em->persist($log);
        $em->flush();
    }

}