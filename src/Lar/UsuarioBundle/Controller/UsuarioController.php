<?php

namespace Lar\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Imagine\Gd\Imagine;

use Lar\UsuarioBundle\Entity\Usuario;
use Lar\UsuarioBundle\Form\UsuarioType;
use Lar\UsuarioBundle\Form\UsuarioFilterType;

/**
 * Usuario controller.
 *
 */
class UsuarioController extends Controller
{
    /**
     * Lists all Usuario entities.
     *
     */
    public function indexAction()
    {

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario"));


        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);


        return $this->render('UsuarioBundle:Usuario:index.html.twig', array(
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

        $em = $this->getDoctrine()->getManager();

        $filterForm = $this->createForm(new UsuarioFilterType($em));

        $queryBuilder = $em->getRepository('UsuarioBundle:Usuario')->findUsuarios();
            //createQueryBuilder('e');


        // Reset filter
        if ($request->getMethod() == 'POST' && $request->get('filter_action') == 'reset') {
            $session->remove('UsuarioControllerFilter');
        }

        // Filter action
        $accion = $request->get('filter_action');
        $data = $request->get('lar_usuariobundle_usuariofiltertype');
        if($data == null){
            $data = array();
        }
        if( $accion == 'proveedor'){
            $data['tipo'] = '2';
        }
        if( $accion == 'empresa' ){
            $data['tipo'] = '1';
        }

        if ($request->getMethod() == 'POST' && ($accion == 'filter' || $accion == 'empresa' || $accion == 'proveedor')  ) {

            $filterForm->bind($data);


            if ($filterForm->isValid()) {

                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('UsuarioControllerFilter', $filterData);
            }

        } else {
            // Get filter from session
            if ($session->has('UsuarioControllerFilter')) {
                $filterData = $session->get('UsuarioControllerFilter');
                $filterForm = $this->createForm(new UsuarioFilterType($em), $filterData);
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
            return $me->generateUrl('usuario', array('page' => $page));
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
     * Finds and displays a Usuario entity.
     *
     */
    public function showAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario"));
        $breadcrumbs->addItem("Ver");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Usuario:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to create a new Usuario entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario"));
        $breadcrumbs->addItem("Nuevo", $this->get("router")->generate("usuario_new"));

        $entity = new Usuario();
        $form = $this->createForm(new UsuarioType(), $entity);

        return $this->render('UsuarioBundle:Usuario:new.html.twig', array(
            'entity' => $entity,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Usuario entity.
     *
     */
    public function createAction()
    {
        $entity = new Usuario();
        $request = $this->getRequest();
        $form = $this->createForm(new UsuarioType(), $entity);

        $form->bind($request);

        if ($form->isValid()) {

            if (null != $entity->getFoto()) {
                $directorioFotos = $this->container->getParameter(
                    'lar.usuario.imagenes'
                );
                //$entity->subirFoto($directorioFotos);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('usuario_show', array('id' => $entity->getId())));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
        }

        return $this->render('UsuarioBundle:Usuario:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     */
    public function editAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario"));
        $breadcrumbs->addItem("Editar");

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createForm(new UsuarioType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Usuario:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Usuario entity.
     *
     */
    public function updateAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createForm(new UsuarioType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $fotoOriginal = $editForm->getData()->getFoto();

        $editForm->bind($request);

        if ($editForm->isValid()) {

            if (null == $entity->getFoto()) {
                // La foto original no se modifica, recuperar su ruta
                $entity->setFoto($fotoOriginal);
            } else {
                // La foto de la oferta se ha modificado
                $directorioFotos = $this->container->getParameter(
                    'lar.usuario.imagenes'
                );

                if ($fotoOriginal != $entity->getFoto()) {
                    //$entity->subirFoto($directorioFotos);
                    // Borrar la foto anterior
                    if (file_exists($directorioFotos . $fotoOriginal) && $fotoOriginal != '') {
                        unlink($directorioFotos . $fotoOriginal);
                    }
                }
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('usuario_edit', array('id' => $id)));
        } else {
            ////$this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('UsuarioBundle:Usuario:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Usuario entity.
     *
     */
    public
    function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuario entity.');
            }

            $directorioFotos = $this->container->getParameter('lar.usuario.imagenes');
            $foto = $entity->getFoto();

            $em->remove($entity);
            $em->flush();
            if ($foto != null) {
                unlink($directorioFotos . $foto);
            }
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('usuario'));
    }

    private
    function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
