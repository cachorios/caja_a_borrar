<?php
/**
 * @author fito
 * @version: 11/04/13 18:07
 *
 * Esta clase representa el servicio de auditoria.
 */

namespace Common\AuditorBundle\Lib;

use Caja\SistemaCajaBundle\Entity\Auditoria;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ServicioAuditoria {
    private $contexto;
    private $entityManager;

    public function __construct($contexto, $entityManager) {
        $this->contexto = $contexto;
        $this->entityManager = $entityManager;
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
            $obj = new $clase();
            if (!in_array($controller[1], $obj->getNoAuditables())) {
                $audit = new Auditoria();
                $this->entityManager->persist($audit->auditar($this->contexto->getToken()->getUser()->getUsername(), $controller[1]));
                $this->entityManager->flush();
            }
        }
    }
}