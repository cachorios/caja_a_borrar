<?php

namespace Caja\SistemaCajaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Caja\SistemaCajaBundle\Entity\Apertura;
use Caja\SistemaCajaBundle\Entity\Lote;
use Caja\SistemaCajaBundle\Entity\LotePago;

use Caja\SistemaCajaBundle\Form\AperturaAnularType;
use Caja\SistemaCajaBundle\Form\AperturaType;
use Caja\SistemaCajaBundle\Form\AperturaCierreType;
use Caja\SistemaCajaBundle\Form\AperturaFilterType;
use Caja\SistemaCajaBundle\Entity\Caja;
use Symfony\Component\HttpFoundation\Response;
/**
 * Apertura controller.
 *
 */
class AperturaController extends Controller
{
    /**
     * Lists all Apertura entities.
     *
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));

        list($filterForm, $queryBuilder) = $this->filter();
        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return $this->render('SistemaCajaBundle:Apertura:index.html.twig', array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        ));
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createForm(new AperturaFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('SistemaCajaBundle:Apertura')->getAperturas($this->getUser());

        // Reset filter
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'reset') {
            $session->remove('AperturaControllerFilter');
        }

        // Filter action
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->bind($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('AperturaControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('AperturaControllerFilter')) {
                $filterData = $session->get('AperturaControllerFilter');
                $filterForm = $this->createForm(new AperturaFilterType(), $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Get results from paginator and get paginator view.
     *
     */
    protected function paginator($queryBuilder)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function ($page) use ($me) {
            return $me->generateUrl('apertura', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view = new TwitterBootstrapView();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => $translator->trans('views.index.pagprev', array(), 'JordiLlonchCrudGeneratorBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(), 'JordiLlonchCrudGeneratorBundle'),
        ));

        return array($entities, $pagerHtml);
    }

    /**
     * Finds and displays a Apertura entity.
     *
     */
    public function showAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Ver");

        $em = $this->getDoctrine()->getManager();

        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Apertura:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to create a new Apertura entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Nuevo");

        $entity = new Apertura();
        $form = $this->createForm(new AperturaType(), $entity);

        return $this->render('SistemaCajaBundle:Apertura:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Apertura entity.
     *
     */
    public function createAction()
    {

        $entity = new Apertura();
        $request = $this->getRequest();
        $form = $this->createForm(new AperturaType(), $entity);

        $form->bind($request);

        if (!$this->container->get("caja.manager")->getApertura() == null) {
            $this->get('session')->getFlashBag()->add('error', 'No puede haber mas de una apertura activa para cada caja');

        } else {

            $caja = $this->container->get("caja.manager")->getCaja();
            $entity->setCaja($caja);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

                return $this->redirect($this->generateUrl('apertura'));
            } else {

                $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            }
        }
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        return $this->render('SistemaCajaBundle:Apertura:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Apertura entity.
     *
     */
    public function editAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Editar");

        $em = $this->getDoctrine()->getManager();

        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }

        $editForm = $this->createForm(new AperturaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Apertura:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Apertura entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));
        $fecha = $entity->getFecha();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }

        $editForm = $this->createForm(new AperturaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->setFecha($fecha);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('apertura_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('SistemaCajaBundle:Apertura:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function cierreAction()
    {
        $entity = $this->container->get('caja.manager')->getApertura();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }
        $entity->setFechaCierre(new \DateTime());
        $editForm = $this->createForm(new AperturaCierreType(), $entity);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'La caja se cerro correctamente');

                return $this->redirect($this->generateUrl('home_page'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
            }
        }

        return $this->render('SistemaCajaBundle:Apertura:cierre.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }


    /**
     * Deletes a Apertura entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SistemaCajaBundle:Apertura')->find($id);


            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Apertura entity.');
            }

            if (!$entity->getFechaCierre()) {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
            } else {
                $this->get('session')->getFlashBag()->add('error', 'No se puede borrar una apertura cerrada');
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('apertura'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }


    public function monitorAction()
    {
        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('success', 'No hay apertura abierta');
            return $this->redirect($this->generateUrl('apertura_new'));
        }

        $em = $this->getDoctrine()->getManager();

        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($apertura->getId());
        $pagosAnulado = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($apertura->getId());

        $DetalleTipoPago = $this->getDetalleTipoPago($apertura->getId());

        return $this->render('SistemaCajaBundle:Apertura:monitor.html.twig', array(
            'caja' => $caja,
            'apertura' => $apertura,
            'importe_pago' => $pagos,
            'pagos_anulado' => $pagosAnulado,
            'detallepago' => $DetalleTipoPago
        ));
    }

    private function getDetalleTipoPago($ap_id)
    {
        $em = $this->getDoctrine()->getManager();
        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($ap_id);


        $tipoPago = array();

        foreach ($PagoTipoPago as $tipo) {
            if (!array_key_exists($tipo['id'], $tipoPago)) {
                $tipoPago[$tipo['id']] = array($tipo['descripcion'], 0, 0);
            }
            if ($tipo['anulado'] == 1) {
                $tipoPago[$tipo['id']][2] = $tipo['1'];
            } else {
                $tipoPago[$tipo['id']][1] = $tipo['1'];
            }

        }
        return $tipoPago;
    }

    public function anularAction()
    {

        $lote = new Lote();
        $lote->addPago(new LotePago());
        $form = $this->createForm(new AperturaAnularType(), $lote);

        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        return $this->render('SistemaCajaBundle:Apertura:anular.html.twig', array(
            'caja' => $caja,
            "form" => $form->createView(),
            'apertura' => $apertura));
    }

    public function anularCreateAction()
    {

        $lote = new Lote();
        $request = $this->getRequest();
        $form = $this->createForm(new AperturaAnularType(), $lote);

        $form->bind($request);


        $caja = $this->container->get("caja.manager")->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            return $this->redirect($this->generateUrl('apertura'));
        }

        $lote->setApertura($apertura);

        if ($form->isValid()) {


            if (strlen($msg = $this->validarDetallesPagos($lote)) == 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($lote);
                $em->flush();

                //Aqui debe retornar al timbrado de cada comprobante
                $this->get('session')->getFlashBag()->add('success', 'flash.create.success');
                return $this->redirect($this->generateUrl('registro'));
            } else {
                $this->get('session')->getFlashBag()->add('error', $msg);
            }
        } else {

            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
        }


        return $this->render("SistemaCajaBundle:Apertura:registro.html.twig", array(
                "lote" => $lote,
                "form" => $form->createView(),
                "caja" => $caja,
                "apertura" => $apertura
            )
        );

    }

    /**
     * Método usado para verificar la existencia de un comprobante que se desea anular
     */
    public function existeComprobanteAction()
    {
        $response = new Response();
        $log = $this->get('logger');

        $cb = $this->getRequest()->get('cb');

        $log->info("---> Codigo de barra: $cb");

        //Codigo de barra recibido
        $cb = trim($cb);

        $apertura = $this->container->get("caja.manager")->getApertura();

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");

        $bm->setCodigo($cb, $apertura->getFecha());
        $imp = $bm->getImporte();
        //Se verifica si existe en la base:
        $em = $this->getDoctrine()->getManager();
        $lotes = $em->getRepository('SistemaCajaBundle:Lote')->getLote($cb);
        //ld($lotes->getDetalle());
        if ($lotes <> null) {
            $rJson = json_encode(array('ok' => 1,
                //'datos' => $lotes->getDetalle(),
                'detalle' => $this->renderView("SistemaCajaBundle:Apertura:_loteDetalle.html.twig", array('elementos' => $lotes->getDetalle()))
            ));
        } else {
            $rJson = json_encode(array(
                'ok' => 0,
                'msg' => 'No existe el comprobante ingresado'
            ));
        }
        return $response->setContent($rJson);
    }
}
