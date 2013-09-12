<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 13/10/12
 * Time: 19:13
 * To change this template use File | Settings | File Templates.
 */
namespace Lar\UsuarioBundle\Entity;


use Doctrine\ORM\EntityRepository;

class UsuarioRepository extends EntityRepository
{
    public function findUsuarios()
    {
        $em = $this->getEntityManager();
        $c = $this->createQueryBuilder('u');

        return $c;
    }

    /**
     * Valida el ingreso al sistema al momento del ingreso
     * Controla el dia, el horario y el lugar
     * Si es administrador, siempre ingresa
     * @param Objeto usuario $usuario
     * @return boolean $resultado
     */
    public function validarIngreso($usuario) {
        $em = $this->getEntityManager();
        $valido = false;
        // Me fijo si el usuario pertenece al grupo de Administradores:
        $grupos = $usuario->getGrupos();
        $es_administrador = false;
        foreach ($grupos as $grupo) {
            if ($grupo->getNombre() == 'Administrador') {
                $es_administrador = true;
            }
        }
        // $usuario = $this->get('security.context')->isGranted('ROLE_ADMIN');
        if ($es_administrador) {// esta todo bien:
            $em->getRepository('UsuarioBundle:LogIngreso')->guardarRegistro($usuario, true);
            return true;
        }
        $usuario_ingreso = $em->getRepository('UsuarioBundle:UsuarioIngreso')->findOneBy(array('usuario' => $usuario->getId()));
        if (!$usuario_ingreso) { //No tiene definido un lugar y horario de acceso
            $em->getRepository('UsuarioBundle:LogIngreso')->guardarRegistro($usuario, $valido);
            return false;
        }
        switch (date("w")) {
            case 0:
                $valido = $usuario_ingreso->getDomingo();
                break;
            case 1:
                $valido = $usuario_ingreso->getLunes();
                break;
            case 2:
                $valido = $usuario_ingreso->getMartes();
                break;
            case 3:
                $valido = $usuario_ingreso->getMiercoles();
                break;
            case 4:
                $valido = $usuario_ingreso->getJueves();
                break;
            case 5:
                $valido = $usuario_ingreso->getViernes();
                break;
            case 6:
                $valido = $usuario_ingreso->getSabado();
                break;
        }

        //Si no tiene horario de ingreso seteado, no puede entrar: error de datos:
        if (!$usuario_ingreso->getHorario()) {
            $em->getRepository('UsuarioBundle:LogIngreso')->guardarRegistro($usuario, $valido);
            return false;
        }
        //Si el dia es valido, verifico el horario:
        if ($valido) {
            $desde = strtotime($usuario_ingreso->getHorario()->getDesde());
            $hasta = strtotime($usuario_ingreso->getHorario()->getHasta());
            $valido = strtotime(date('H:i:s')) >= $desde && strtotime(date('H:i:s')) <= $hasta;
        }

        //Verifico el lugar desde donde esta ingresando, para ver si se corresponde con lo que tiene asignado:
        if ($valido && $usuario_ingreso->getControlLugar()) { // Tiene control de lugar
            // Traigo la lista de lugares permitidos:
            $lugares = $em->getRepository('UsuarioBundle:LugarIngreso')->findAll();
            if ($lugares) {
                $valido = false;
                foreach ($lugares as $lugar) {
                    $mascara_valida = $lugar->getMascara();
                    $largo_mascara = strlen($mascara_valida);
                    // Obtengo los primeros n caracteres de la ip que intenta ingresar:
                    $mascara_ip_origen = substr($_SERVER['REMOTE_ADDR'], 0, $largo_mascara);
                    if ($mascara_valida == $mascara_ip_origen) {
                        $valido = true; // esta todo bien
                        break;
                    }
                }
            } else {
                $valido = false;
            }
        }
        //Antes de retornar, guardo el registro:
        $em->getRepository('UsuarioBundle:LogIngreso')->guardarRegistro($usuario, $valido);

        return $valido;
    }

}