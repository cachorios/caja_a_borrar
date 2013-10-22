<?php

namespace Lar\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Lar\UsuarioBundle\Entity\UsuarioIngreso;
use Lar\UsuarioBundle\Form\UsuarioIngresoType;
use Lar\UsuarioBundle\Form\UsuarioIngresoFilterType;

/**
 * UsuarioIngreso controller.
 *
 */
class UsuarioIngresoController extends Controller
{
    /**
     * Lists all UsuarioIngreso entities.
     *
     */
    public function indexAction()
    {
        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return $this->render('UsuarioBundle:UsuarioIngreso:index.html.twig', array(
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
        $filterForm = $this->createForm(new UsuarioIngresoFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('UsuarioBundle:UsuarioIngreso')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('UsuarioIngresoControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->bind($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('UsuarioIngresoControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('UsuarioIngresoControllerFilter')) {
                $filterData = $session->get('UsuarioIngresoControllerFilter');
                $filterForm = $this->createForm(new UsuarioIngresoFilterType(), $filterData);
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
            return $me->generateUrl('usuarioingreso', array('page' => $page));
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
     * Creates a new UsuarioIngreso entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new UsuarioIngreso();
        $form = $this->createForm(new UsuarioIngresoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('usuarioingreso_show', array('id' => $entity->getId())));
        }

        return $this->render('UsuarioBundle:UsuarioIngreso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new UsuarioIngreso entity.
     *
     */
    public function newAction()
    {
        $entity = new UsuarioIngreso();
        $form   = $this->createForm(new UsuarioIngresoType(), $entity);

        return $this->render('UsuarioBundle:UsuarioIngreso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UsuarioIngreso entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:UsuarioIngreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UsuarioIngreso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:UsuarioIngreso:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing UsuarioIngreso entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:UsuarioIngreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UsuarioIngreso entity.');
        }

        $editForm = $this->createForm(new UsuarioIngresoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:UsuarioIngreso:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing UsuarioIngreso entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:UsuarioIngreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UsuarioIngreso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UsuarioIngresoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('usuarioingreso_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('UsuarioBundle:UsuarioIngreso:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a UsuarioIngreso entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:UsuarioIngreso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UsuarioIngreso entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('usuarioingreso'));
    }

    /**
     * Creates a form to delete a UsuarioIngreso entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
