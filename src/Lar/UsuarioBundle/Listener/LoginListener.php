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
        //$em = $this->getEntityManager();
        $usuario = $event->getAuthenticationToken()->getUser();
        // Me fijo si el usuario pertenece al grupo de Administradores:
        $grupos = $usuario->getGrupos();
        $es_administrador = false;
        foreach ($grupos as $grupo) {
            if ($grupo->getNombre() == 'Administradores') {
                $es_administrador = true;
            }
        }
        if ($es_administrador) {
            // es un administrador, esta todo bien
            $valido = true;
        } else { // Pregunto si es un dia permitido:
            $valido = false;
            $usuario_ingreso = $em->getRepository('UsuarioBundle:UsuarioIngreso')->findOneBy(array(
                'usuario' => $usuario->getId()
            ));

            if ($usuario_ingreso) {
                $dia = date("w");
                if ($dia == 0) {//domingo
                    $domingo = $usuario_ingreso->getDomingo();
                    if ($domingo) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 1) {//lunes
                    $lunes = $usuario_ingreso->getLunes();
                    if ($lunes) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 2) {//martes
                    $martes = $usuario_ingreso->getMartes();
                    if ($martes) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 3) {//miercoles
                    $miercoles = $usuario_ingreso->getMiercoles();
                    if ($miercoles) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 4) {//jueves
                    $jueves = $usuario_ingreso->getJueves();
                    if ($jueves) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 5) {//viernes
                    $viernes = $usuario_ingreso->getViernes();
                    if ($viernes) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                } else if ($dia == 6) {//sabado
                    $sabado = $usuario_ingreso->getSabado();
                    if ($sabado) {
                        $valido = true;
                    } else {
                        $valido = false;
                    }
                }

                // Si el dia es valido, sigo el control, ahora el horario:
                if ($valido) {
                    // Pregunto si tiene control de horario:
                    if ($usuario_ingreso->getHorario()) {
                        $hora_actual = strtotime(date('H:i:s'));
                        $horario_permitido_desde = strtotime($usuario_ingreso->getHorario()->getDesde());
                        $horario_permitido_hasta = strtotime($usuario_ingreso->getHorario()->getHasta());
                        if ($hora_actual >= $horario_permitido_desde && $hora_actual <= $horario_permitido_hasta) {
                            // esta todo ok
                        } else {
                            $valido = false;//esta fuera del horario comprendido
                        }
                    }

                }

                // Si paso el control de horario, verifico el lugar desde donde accede:
                if ($valido) {

                    // Pregunto si el lugar desde donde accede se corresponde con lo que tiene asignado:
                    if ($usuario_ingreso->getControlLugar()) { // Tiene control de lugar
                        // Traigo la lista de lugares permitidos:
                        $lugares = $em->getRepository('UsuarioBundle:LugarIngreso')->findAll();
                        if ($lugares) {
                            $ip_usuario = $_SERVER['REMOTE_ADDR'];
                            //$ip_usuario = '190.226.43.186'; -- se puso fijo para probar, en su momento
                            $encontro = false;
                            foreach ($lugares as $lugar) {
                                $mascara_valida = $lugar->getMascara();
                                $largo_mascara = strlen($mascara_valida);
                                // Obtengo los primeros n caracteres de la ip que intenta ingresar:
                                $mascara_ip_origen = substr($ip_usuario, 0, $largo_mascara);
                                if ($mascara_valida == $mascara_ip_origen) {
                                    // esta todo bien
                                    $encontro = true;
                                    break;
                                }
                            }
                            // Si no pudo validar despues de haber recorrido los lugares:
                            if (!$encontro) {
                                $valido = false; // no es una ip habilitada para ingresar
                            }
                        } else {
                            $valido = false;// No se pudo recuperar la lista de lugares:
                        }
                    }
                }

            } else {
                // No se pudo recuperar las reglas de ingreso asociadas al usuario:
                $valido = false;
            }

        }

        // Despues de haber hecho todos los controles, pregunto si paso o no los mismos:
        if (!$valido) {
            // No se puede loguear, esta fuera de los dias y horarios permitidos
            // Registro el ingreso rechazado antes de salir:

            $logaudit = new \Lar\UsuarioBundle\Entity\LogIngreso();
            $logaudit->setUsuario($usuario);
            $logaudit->setDescripcion('INGRESO RECHAZADO:'.$usuario->getNombre().":".$_SERVER['HTTP_HOST'].":".$_SERVER['REMOTE_ADDR']);
            $logaudit->setFecha(new \DateTime());
            $em->persist($logaudit);
            $em->flush();
            throw new BadCredentialsException('Ingreso rechazado. Lugar de acceso u horario no permitidos.', 0);
            $event->stopPropagation();

        }
        if ($valido) {
            $logaudit = new \Lar\UsuarioBundle\Entity\LogIngreso();
            $logaudit->setUsuario($usuario);
            $logaudit->setDescripcion('INGRESO VALIDO:'.$usuario->getNombre().":".$_SERVER['HTTP_HOST'].":".$_SERVER['REMOTE_ADDR']);
            $logaudit->setFecha(new \DateTime());
            $em->persist($logaudit);
            $em->flush();
        }

    }
}