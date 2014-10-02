<?php

namespace Caja\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\GeneralBundle\Lib\MotorImpresion;
//use MP\GeneralBundle\Lib\ReportesCatastroImagenPlancheta;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\Reference;
use Caja\GeneralBundle\Lib\T;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class ReporteController extends Controller {
    /* public function indexAction($name) {
      return $this->render('GeneralBundle:Default:index.html.twig', array('name' => $name));
      } */

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function imprimirAction() {


        ////////////////////////////////////////////////////////////
        // comienzo seteando el entity manager
        ////////////////////////////////////////////////////////////
        $em = $this->getDoctrine()->getManager();
        T::setEM($em);

        // retrieve messages
        foreach ($this->get('session')->getFlashBag()->get('parametro') as $valor) {
            $parametros = $valor;
        }

        if (!isset($parametros['clase'])) {
            throw new AccessDeniedException();
        }

        $claseImprimible = $parametros["clase"];

        if ($claseImprimible != '' && $claseImprimible != null) {
            if ($claseImprimible == 'convenioSimularCaida') {
                $convenioId = $this->getRequest()->get('convenioId');
                $reimp = $this->getRequest()->get('reimp');
                $parametros = array('convenio' => $convenioId, "reimp" => $reimp);
            }

            //$reflection_class = new ReflectionClass($claseImprimible);
            //$imprimible = $reflection_class->newInstanceArgs($params);
            //$claseImprimible = "MP\GeneralBundle\Lib\CatastroImagenPlancheta";
            //ld($claseImprimible);

            $imprimible = new $claseImprimible;
            //$imprimible = new CatastroImagenPlancheta();
            $imprimible->setContainer($this->container);
            $imprimible->setEntityManager($this->getDoctrine()->getManager());
            $imprimible->setValores($parametros);

            // LLAMO AL MOTOR DE IMPRESION Y LE PASO LA CLASE INSTANCIADA
            // QUE IMPLEMENTA LA INTERFACE DE IMPRIMIBLE
            $motorImpresion = new MotorImpresion($this->container, $imprimible);
            $motorImpresion->imprimirReporte($imprimible);

            $parametrosReportes = $motorImpresion->obtenerParametrosReporte();

            return $this->render('GeneralBundle:Reporte:index.html.twig', array('parametros' => $parametrosReportes));
        } else {
            $log = $this->get('logger');
            $log->addInfo("***** Error en el modulo reporte. Usuario: ");
        }
        exit;
    }

}
