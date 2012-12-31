<?php

namespace Lar\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Lar\UsuarioBundle\Entity\Grupo;
use Lar\UsuarioBundle\Form\GrupoType;
use Lar\UsuarioBundle\Form\GrupoFilterType;

/**
 * Grupo controller.
 *
 */
class GrupoController extends Controller
{
    /**
     * Lists all Grupo entities.
     *
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Grupo", $this->get("router")->generate("grupo"));

        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

    
        return $this->render('UsuarioBundle:Grupo:index.html.twig', array(
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
        $filterForm = $this->createForm(new GrupoFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('UsuarioBundle:Grupo')->createQueryBuilder('e');
    
        // Reset filter
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'reset') {
            $session->remove('GrupoControllerFilter');
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
                $session->set('GrupoControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('GrupoControllerFilter')) {
                $filterData = $session->get('GrupoControllerFilter');
                $filterForm = $this->createForm(new GrupoFilterType(), $filterData);
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
            return $me->generateUrl('grupo', array('page' => $page));
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
     * Finds and displays a Grupo entity.
     *
     */
    public function showAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Grupo", $this->get("router")->generate("grupo"));
        $breadcrumbs->addItem("Show" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Grupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Grupo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Grupo entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Grupo", $this->get("router")->generate("grupo"));
        $breadcrumbs->addItem("Nuevo" );

        $entity = new Grupo();
        $form   = $this->createForm(new GrupoType(), $entity);

        return $this->render('UsuarioBundle:Grupo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Grupo entity.
     *
     */
    public function createAction()
    {
        $entity  = new Grupo();
        $request = $this->getRequest();
        $form    = $this->createForm(new GrupoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('grupo_show', array('id' => $entity->getId())));        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
        }

        return $this->render('UsuarioBundle:Grupo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Grupo entity.
     *
     */
    public function editAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Grupo", $this->get("router")->generate("grupo"));
        $breadcrumbs->addItem("Editar" );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Grupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupo entity.');
        }

        $editForm = $this->createForm(new GrupoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Grupo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Grupo entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Grupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupo entity.');
        }

        $editForm   = $this->createForm(new GrupoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('grupo_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('UsuarioBundle:Grupo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Grupo entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:Grupo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Grupo entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('grupo'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
