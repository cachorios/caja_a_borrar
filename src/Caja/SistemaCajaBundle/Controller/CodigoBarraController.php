<?php

namespace Caja\SistemaCajaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Caja\SistemaCajaBundle\Entity\CodigoBarra;
use Caja\SistemaCajaBundle\Form\CodigoBarraType;
use Caja\SistemaCajaBundle\Form\CodigoBarraFilterType;

/**
 * CodigoBarra controller.
 *
 */
class CodigoBarraController extends Controller
{
    /**
     * Lists all CodigoBarra entities.
     *
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("CodigoBarra", $this->get("router")->generate("codigobarra"));

        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

    
        return $this->render('SistemaCajaBundle:CodigoBarra:index.html.twig', array(
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
        $filterForm = $this->createForm(new CodigoBarraFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('SistemaCajaBundle:CodigoBarra')->createQueryBuilder('e');
    
        // Reset filter
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'reset') {
            $session->remove('CodigoBarraControllerFilter');
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
                $session->set('CodigoBarraControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('CodigoBarraControllerFilter')) {
                $filterData = $session->get('CodigoBarraControllerFilter');
                $filterForm = $this->createForm(new CodigoBarraFilterType(), $filterData);
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
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('codigobarra', array('page' => $page));
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
     * Finds and displays a CodigoBarra entity.
     *
     */
    public function showAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("CodigoBarra", $this->get("router")->generate("codigobarra"));
        $breadcrumbs->addItem("Ver" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:CodigoBarra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodigoBarra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:CodigoBarra:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new CodigoBarra entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("CodigoBarra", $this->get("router")->generate("codigobarra"));
        $breadcrumbs->addItem("Nuevo" );

        $entity = new CodigoBarra();
        $form   = $this->createForm(new CodigoBarraType(), $entity);

        return $this->render('SistemaCajaBundle:CodigoBarra:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new CodigoBarra entity.
     *
     */
    public function createAction()
    {
        $entity  = new CodigoBarra();
        $request = $this->getRequest();
        $form    = $this->createForm(new CodigoBarraType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($entity->getPosiciones() as $posicion) {
                $posicion->setCodigoBarra($entity);
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('codigobarra_show', array('id' => $entity->getId())));        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
        }

        return $this->render('SistemaCajaBundle:CodigoBarra:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing CodigoBarra entity.
     *
     */
    public function editAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("CodigoBarra", $this->get("router")->generate("codigobarra"));
        $breadcrumbs->addItem("Editar" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:CodigoBarra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodigoBarra entity.');
        }

        $editForm = $this->createForm(new CodigoBarraType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:CodigoBarra:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing CodigoBarra entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:CodigoBarra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodigoBarra entity.');
        }

        $originalPosisiones = array();

        // cargar en el array todas las posiciones
        foreach ($entity->getPosiciones() as $posicion) {
            $originalPosisiones[] = $posicion;
        }


        $editForm   = $this->createForm(new CodigoBarraType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {

            // filtrar $originalPosiciones que no estan presente
            foreach ($entity->getPosiciones() as $posicion) {
                if($posicion->getCodigoBarra() == null){
                    $posicion->setCodigoBarra($entity);
                }
                foreach ($originalPosisiones as $key => $toDel) {
                    if ($toDel->getId() === $posicion->getId()) {
                        unset($originalPosisiones[$key]);
                    }
                }
            }

            // los que quedaron en el array son para borrar
            foreach ($originalPosisiones as $posicion) {
                $entity->getPosiciones()->removeElement($posicion);
                $em->remove($posicion);
            }



            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('codigobarra_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('SistemaCajaBundle:CodigoBarra:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CodigoBarra entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SistemaCajaBundle:CodigoBarra')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CodigoBarra entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('codigobarra'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
