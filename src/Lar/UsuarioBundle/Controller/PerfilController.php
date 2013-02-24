<?php

namespace Lar\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Imagine\Gd\Imagine;

use Lar\UsuarioBundle\Entity\Usuario;
use Lar\UsuarioBundle\Form\PerfilType;


/**
 * Usuario controller.
 *
 */
class PerfilController extends Controller
{


    /**
     * Displays a form to edit an existing Usuario entity.
     *
     */
    public function editAction()
    {

        $em = $this->getDoctrine()->getManager();
		$usuario = $this->getUser();

        $entity = $em->getRepository('UsuarioBundle:Usuario')->find($usuario->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createForm(new PerfilType(), $entity);

        return $this->render('UsuarioBundle:Perfil:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Usuario entity.
     *
     */
    public function updateAction()
    {

        $em = $this->getDoctrine()->getManager();
		$usuario = $this->getUser();

        $entity = $em->getRepository('UsuarioBundle:Usuario')->find($usuario->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createForm(new PerfilType(), $entity);

        $request = $this->getRequest();

        $fotoOriginal = $editForm->getData()->getFoto();

        $editForm->bind($request);

        if ($editForm->isValid()) {

            if (null == $entity->getFoto() || 'anonimo.jpg' == $entity->getFoto()   ) {
                // La foto original no se modifica, recuperar su ruta
                $entity->setFoto($fotoOriginal);
            } else {
                // La foto de la oferta se ha modificado
                $directorioFotos = $this->container->getParameter(
                    'lar.usuario.imagenes'
                );

                //$ld() ;
                if ($fotoOriginal != $entity->getFoto()  ) {
                    $entity->subirFoto($directorioFotos);
                    // Borrar la foto anterior
                    if($fotoOriginal != 'anonimo.jpg' && file_exists($directorioFotos . $fotoOriginal) && $fotoOriginal != '') {
                        unlink($directorioFotos . $fotoOriginal);
                    }
                }
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('perfil'));
        } else {
            ////$this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('UsuarioBundle:Perfil:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),

        ));
    }


}
