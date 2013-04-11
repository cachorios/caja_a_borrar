<?php
/**
 * @author fito
 * @version: 11/04/13 18:07
 *
 * Esta clase representa el servicio de auditoria.
 */

namespace Caja\SistemaCajaBundle\Lib;

use Caja\SistemaCajaBundle\Entity\Auditoria;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ServicioAuditoria {
    public function __construct($contenedor) {
        $this->contenedor = $contenedor;
    }

    public function onKernelController(FilterControllerEvent $event) {
        $controller = $event->getController();

        /*
         * el $controller pasado puede ser una clase o un cierre.
         * Esto no es habitual en Symfony2 pero puede suceder.
         * Si se trata de una clase, viene en formato de arreglo
         */
        if (!is_array($controller)) {
            return;
        }

        /*
         * verifica si el controller esta marcado para ser auditado
         */
        if ($controller[0] instanceof IModuloAuditable) {
            $clase = new $controller[0];
            $action = $controller[1];
            $obj = new $clase();
            if (!in_array($action, $obj->getNoAuditables())) {
                $audit = new Auditoria();
                $audit->setFecha(new \DateTime());
                $audit->setAccion($action);
                $audit->setUsuario($this->contenedor->get("security.context")->getToken()->getUser()->getUsername());

                $em = $this->contenedor->get("doctrine.orm.entity_manager");
                $em->persist($audit);
                $em->flush();
            }
        }
    }
}