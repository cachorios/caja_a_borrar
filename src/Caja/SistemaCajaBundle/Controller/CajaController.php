<?php

namespace Caja\SistemaCajaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Caja\SistemaCajaBundle\Entity\Caja;
use Caja\SistemaCajaBundle\Form\CajaType;
use Caja\SistemaCajaBundle\Form\CajaFilterType;


/**
 * Caja controller.
 *
 */
class CajaController extends Controller
{
    /**
     * Lists all Caja entities.
     *
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Caja", $this->get("router")->generate("caja"));

        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);


        return $this->render('SistemaCajaBundle:Caja:index.html.twig', array(
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
        $filterForm = $this->createForm(new CajaFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('SistemaCajaBundle:Caja')->createQueryBuilder('e');
            //findCajas();
            //


        // Reset filter
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'reset') {
            $session->remove('CajaControllerFilter');
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
                $session->set('CajaControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('CajaControllerFilter')) {
                $filterData = $session->get('CajaControllerFilter');
                $filterForm = $this->createForm(new CajaFilterType(), $filterData);
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
        $pagerfanta->setMaxPerPage(5);
        $currentPage = $this->getRequest()->get('page', 1);

        $pagerfanta->setCurrentPage($currentPage);

        $entities = $pagerfanta->getCurrentPageResults();
    
        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('caja', array('page' => $page));
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
     * Finds and displays a Caja entity.
     *
     */
    public function showAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Caja", $this->get("router")->generate("caja"));
        $breadcrumbs->addItem("Ver" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Caja:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Caja entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Caja", $this->get("router")->generate("caja"));
        $breadcrumbs->addItem("Nuevo" );

        $entity = new Caja();
        $form   = $this->createForm(new CajaType(), $entity);

        return $this->render('SistemaCajaBundle:Caja:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Caja entity.
     *
     */
    public function createAction()
    {
        $entity  = new Caja();
        $request = $this->getRequest();
        $form    = $this->createForm(new CajaType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('caja_show', array('id' => $entity->getId())));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
        }

        return $this->render('SistemaCajaBundle:Caja:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Caja entity.
     *
     */
    public function editAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Caja", $this->get("router")->generate("caja"));
        $breadcrumbs->addItem("Editar" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $editForm = $this->createForm(new CajaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Caja:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Caja entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SistemaCajaBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $editForm   = $this->createForm(new CajaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('caja_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('SistemaCajaBundle:Caja:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Caja entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SistemaCajaBundle:Caja')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Caja entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('caja'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
