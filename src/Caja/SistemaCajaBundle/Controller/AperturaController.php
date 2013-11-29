<?php

namespace Caja\SistemaCajaBundle\Controller;

use Caja\SistemaCajaBundle\Lib\IModuloAuditable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use Caja\SistemaCajaBundle\Entity\Apertura;
use Caja\SistemaCajaBundle\Entity\Lote;
use Caja\SistemaCajaBundle\Entity\LoteDetalle;
use Caja\SistemaCajaBundle\Entity\LotePago;
use Caja\SistemaCajaBundle\Entity\CierreCaja;
use Caja\SistemaCajaBundle\Form\AperturaAnularType;
use Caja\SistemaCajaBundle\Form\AperturaType;
use Caja\SistemaCajaBundle\Form\AperturaCierreType;
use Caja\SistemaCajaBundle\Form\AperturaFilterType;
use Caja\SistemaCajaBundle\Entity\Caja;
use Symfony\Component\HttpFoundation\Response;

use Common\AuditorBundle\Lib\IControllerAuditable;

/**
 * Apertura controller.
 *
 */
class AperturaController extends Controller implements IControllerAuditable
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

        $apertura = $this->container->get('caja.manager')->getApertura();
        if ($apertura) {
            $tiene_apertura_activa = true;
        } else {
            $tiene_apertura_activa = false;
        }
        return $this->render('SistemaCajaBundle:Apertura:index.html.twig',
            array('entities' => $entities,
                'pagerHtml' => $pagerHtml,
                'filterForm' => $filterForm->createView(),
                'tiene_apertura_activa' => $tiene_apertura_activa));
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
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array('proximity' => 3,
            'prev_message' => $translator->trans('views.index.pagprev', array(),
                'JordiLlonchCrudGeneratorBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(),
                'JordiLlonchCrudGeneratorBundle'),));

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

        return $this->render('SistemaCajaBundle:Apertura:show.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
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
            'caja' => $this->container->get("caja.manager")->getCaja()
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
        $msg = false;
        if (!$this->container->get("caja.manager")->getApertura() == null) {
            //$this->get('session')->getFlashBag()->add('error', 'No puede haber mas de una apertura activa para cada caja');
            $msg = 'No puede haber mas de una apertura activa para cada caja';
        } else {

            $caja = $this->container->get("caja.manager")->getCaja();
            $entity->setCaja($caja);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                // no hace falta por uso de ajax//$this->get('session')->getFlashBag()->add('success', 'Apertura creada exitosamente');

                $ticket = $this->get("sistemacaja.ticket");
                $ticket->setContenido(
                    str_pad("Apertura de Caja", 40, " ", STR_PAD_BOTH) .
                        str_pad("-", 40, "-", STR_PAD_BOTH) .
                        str_pad("Apertura nro. " . $entity->getId(), 40, "-", STR_PAD_BOTH)
                );
                $tk = $ticket->getTicketTestigo();
                ////return $this->redirect($this->generateUrl('apertura'));
            } else {
                $msg = "No se pudo crear";
                ///$this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            }
        }
        //$breadcrumbs = $this->get("white_october_breadcrumbs");
        //return $this->render('SistemaCajaBundle:Apertura:new.html.twig', array('entity' => $entity, 'form' => $form->createView(),));

        if (!$msg) {
            $ret = array("ok" => 1, "tk" => $tk);
        } else {
            $ret = array("ok" => 0, "msg" => $msg);
        }

        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;


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

        return $this->render('SistemaCajaBundle:Apertura:edit.html.twig',
            array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(),));
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

        return $this->render('SistemaCajaBundle:Apertura:edit.html.twig',
            array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(),));
    }

    public function cierreAction()
    {
        $entity = $this->container->get('caja.manager')->getApertura();
        $caja = $this->container->get("caja.manager")->getCaja();
        $editForm = $this->createForm(new AperturaCierreType(), $entity);
        $msg = false;
        if (!$entity) {
            $msg = 'No se pudo recuperar la apertura';
        } else {
            $entity->setFechaCierre(new \DateTime());
            $entity->setDireccionIp($_SERVER['REMOTE_ADDR']);
            $entity->setHost($_SERVER['HTTP_HOST']);

            $request = $this->getRequest();
            $tk = "";
            if ($request->getMethod() == 'POST') {
                $editForm->bind($request);
                if ($editForm->isValid()) {
                    $em = $this->getDoctrine()->getManager();

                    // Hago el "commit" del cierre, entonces si falla la generacion del archivo, no interfiere con esto
                    $em->persist($entity);
                    $em->flush();

                    $tk = $this->imprimirCierre($entity->getId(), 1);
                    if ($tk == "") {
                        $msg = 'No se pudieron recuperar los datos del cierre.';
                    }

                    //Genero el archivo de texto que se envia por mail. CONSIDERACIONES GENERALES:
                    //                El archivo con el detalle de las cobranzas debería tener  la siguiente estructura.
                    //                Solicito que el nombre comience con EP seguido de la fecha de cobro en formado DDMMAA. EJ:
                    //                Cobranzas del Día 18 de Marzo del 2013, EP180313.TXT – Cobranzas del Día 2 de Mayo del 2013, EP020513.TXT,
                    //                dado que la incorporacion de datos a nuestros sistema esta automatizada
                    //                y espera un archivo con esa estructura de nombres.
                    //                Cabe mencionar que este formato es igual y único para cualquier tasa municipal que se cobra

                    //Por cada comprobante generado:
                    //                Desde	Hasta	Longitud	Descripción
                    //                1	    44	    44	        Código de Barras de la Municipalidad Utilizado para la Cobranza.  Sin modificaciones
                    //                45	50	    6	        Valor Entero del  Importe Cobrado
                    //                51	52	    2	        Valor Decimales del Importe Cobrado
                    //                53	60	    8	        Fecha de Pago – Formato AAAAMMDD
                    //                61	61	    1	        Código Fijo de empresa. Uso interno de la Municipalidad de Posadas. Usar siempre un Valor Fijo = 1 (Uno)
                    //                62	65	    4	        Numero de Caja Rellenados con ceros a la izquierda


                    /////////////////////////////////////////////////////////////
                    ///////SI NO HUBO COBROS, NO GENERO EL ARCHIVO, SOLO ENVIO EL MAIL////////////////
                    /////////////////////////////////////////////////////////////

                    if ($entity->getComprobanteCantidad() > 0) {
                        $apertura_id = $entity->getId();
                        $numero_caja = $entity->getCaja()->getNumero();
                        $path_archivos = $this->container->getParameter('caja.apertura.dir_files');
                        if (!file_exists($path_archivos)) {
                            //Si no existe el directorio donde se guardan los archivos de cierre, lo creo;
                            if (!mkdir($path_archivos, '0644')) { // 0644 es lectura y escritura para el propietario, lectura para los demás
                                $msg = '¡¡¡ Error al crear el directorio que va a contener los archivos de cierre !!!!!';
                            }
                        }

                        //Si esta todo bien, sigo:
                        if (!$msg) {
                            $archivo_generado = $em->getRepository('SistemaCajaBundle:Apertura')->generaArchivoTexto($apertura_id, $numero_caja, $path_archivos);

                            if (!$archivo_generado) {
                                $msg = '¡¡¡ Error al generar el archivo de texto que se envia por mail !!!!!';
                            }
                            //Si esta todo bien, sigo:
                            if (!$msg) {
                                //Por ultimo: guardo en la tabla Apertura el nombre del archivo generado:
                                $entity->setArchivoCierre($archivo_generado . '.txt');
                                $em->persist($entity);
                                $em->flush();
                            }
                        }
                    }
                    $em->close();
                } else {
                    $msg = 'Alguno de los datos ingresados es incorrecto';
                }

                //Verifico si estuvo todo ok, y devuelvo:
                if (!$msg) {
                    $ret = array("ok" => 1, "tk" => $tk);
                } else {
                    $ret = array("ok" => 0, "msg" => $msg);
                }

                $response = new Response();
                $response->setContent(json_encode($ret));
                return $response;

            }
        }

        return $this->render('SistemaCajaBundle:Apertura:cierre.html.twig', array('entity' => $entity, 'caja' => $caja, 'edit_form' => $editForm->createView(),));
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

        return $this->redirect($this->generateUrl('apertura_new'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
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
        //$detalle_pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetallePagos($apertura->getId());
        $DetalleTipoSeccion = $this->getPagosTipoSeccion($apertura->getId());

        return $this->render('SistemaCajaBundle:Apertura:monitor.html.twig',
            array('caja' => $caja,
                'apertura' => $apertura,
                'importe_pago' => sprintf("%9.2f", $pagos),
                'pagos_anulado' => sprintf("%9.2f", $pagosAnulado),
                'detalle_tipo_pagos' => $DetalleTipoPago,
                'detalle_tipo_seccion' => $DetalleTipoSeccion)); //,
        //'detalle_pagos' => $detalle_pagos));
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

            $tipoPago[$tipo['id']][1] = $tipo['importe'] + $tipo['anulado'];
            $tipoPago[$tipo['id']][2] = $tipo['anulado'];

        }
        return $tipoPago;
    }

    private function getPagosTipoSeccion($ap_id)
    {
        $em = $this->getDoctrine()->getManager();
        $servicio_tabla = $this->get("lar.parametro.tabla");
        $bm = $this->container->get("caja.barra");

        //$pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetalleTodosPagos($ap_id);
        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoSeccion($ap_id);

        $array_detalle_pagos = array();

        foreach ($pagos as $pago) {

            if (!array_key_exists($pago['codigo'], $array_detalle_pagos)) {
                $array_detalle_pagos[$pago['codigo']] = array($pago['descripcion'], 0, 0, "", "");
            }

            $array_detalle_pagos[$pago['codigo']][1] = $pago['importe'];
            $array_detalle_pagos[$pago['codigo']][2] = $pago['anulado'];
            $array_detalle_pagos[$pago['codigo']][3] = $pago['codigo'];
            $array_detalle_pagos[$pago['codigo']][4] = $pago['cantidad'];
        }

        return $array_detalle_pagos;
    }


    public function anularAction()
    {

        $lote = new Lote();
        $lote->addPago(new LotePago());
        $form = $this->createForm(new AperturaAnularType(), $lote);

        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        //Si no hay apertura activa, no dejo hacer nada:
        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('error', 'No existe una apertura activa.');
            return $this->redirect($this->generateUrl('apertura_new'));
        }

        return $this->render('SistemaCajaBundle:Apertura:anular.html.twig',
            array('caja' => $caja, "form" => $form->createView(), 'apertura' => $apertura));
    }

    /**
     * Anula comprobantes que pertencen a un lote determinado
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function anularComprobanteAction()
    {
        $tk = "";
        // A partir del comprobante ingresado, recupero el lote al cual pertenece:
        $request = $this->getRequest();
        $comprobantes = $request->get('comprobantes_anular'); //Recupero los comprobantes que fueron seleccionados para anular:
        if (count($comprobantes) == 0) {
            //$this->get('session')->getFlashBag()->add('error', 'No se ingresaron comprobantes para anular.');
            //return $this->redirect($this->generateUrl('apertura_anulado'));
            $msg = 'No se ingresaron comprobantes para anular.';
            $ret = array("ok" => 0, "msg" => $msg);

            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
        }

        $caja = $this->container->get("caja.manager")->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            //$this->get('session')->getFlashBag()->add('error', 'No existe una apertura activa.');
            //return $this->redirect($this->generateUrl('apertura_new '));
            $msg = 'No existe una apertura activa.';
            $ret = array("ok" => 0, "msg" => $msg);

            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
        }

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");

        //Se verifica si existe en la base el comprobante ingresado:
        $em = $this->getDoctrine()->getManager();
        $lote = $em->getRepository('SistemaCajaBundle:Lote')->getLote($apertura->getId(), $comprobantes[0]);

        if ($lote != null) {
            $puede_anular_parcialmente = $em->getRepository('SistemaCajaBundle:Lote')->verificaAnulacionParcial($lote, $comprobantes);

            if ($puede_anular_parcialmente != "OK") {
                //$this->get('session')->getFlashBag()->add('error', $puede_anular_parcialmente);
                //return $this->redirect($this->generateUrl('apertura_anulado'));
                $msg = $puede_anular_parcialmente;
                $ret = array("ok" => 0, "msg" => $msg);

                $response = new Response();
                $response->setContent(json_encode($ret));
                return $response;
            }

            try {
                $user = $this->getUser();
                $em->getRepository('SistemaCajaBundle:Lote')->anularComprobantesLote($lote, $comprobantes, $user);

                $comprobantes = $em->getRepository('SistemaCajaBundle:LoteDetalle')->findBy(array('codigo_barra' => $comprobantes));

                foreach ($comprobantes as $comprobante) {
                    $ticket = $this->get("sistemacaja.ticket");
                    $ticket->setContenido(
                        str_pad(" ", 40, " ", STR_PAD_BOTH) .
                            str_pad("Comprobante " . $comprobante->getComprobante(), 25, " ", STR_PAD_RIGHT) .
                            str_pad(sprintf("%9.2f", $comprobante->getImporte()), 15, " ", STR_PAD_LEFT)
                    );

                    $ticket->setValores(array(
                        'titulo' => "ANULACION DE COMPROBANTE",
                        'ticket' => $comprobante->getId(),
                        'codigobarra' => $comprobante->getCodigoBarra(),
                        'referencia' => $comprobante->getReferencia()
                    ));
                    $tk .= $ticket->getTicketTestigo();
                }

                //$tk .= $ticket->getTicketTestigo();  //si se quiere imprimir ticket deshabilitar esta linea
                $ret = array("ok" => 1, "tk" => $tk);

            } catch (\Exception $e) {
                //$this->get('session')->getFlashBag()->add('error', 'Hubo un fallo al guardar los datos: ' . $e->getMessage());
                //return $this->redirect($this->generateUrl('apertura_anulado'));
                $msg = 'Hubo un fallo al guardar los datos: ' . $e->getMessage();
                $ret = array("ok" => 0, "msg" => $msg);
            }

        } else {
            //$this->get('session')->getFlashBag()->add('error', 'Alguno de los comprobantes seleccionados es incorrecto.');
            //return $this->redirect($this->generateUrl('apertura_anulado'));
            $msg = 'Alguno de los comprobantes seleccionados es incorrecto.';
            $ret = array("ok" => 0, "msg" => $msg);
        }

        //$this->get('session')->getFlashBag()->add('success', 'Los comprobantes seleccionados han sido anulados');
        //return $this->redirect($this->generateUrl('apertura_anulado'));
        /*Verifico si estuvo todo ok, y devuelvo:*/


        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;


    }

    /**
     * Método usado para verificar la existencia de un comprobante que se desea anular
     */
    public function existeComprobanteAction()
    {
        $response = new Response();
        $cb = $this->getRequest()->get('cb');

        //Codigo de barra recibido
        $cb = trim($cb);

        $apertura = $this->container->get("caja.manager")->getApertura();

        //Preguntar si la caja esta abierta: SE SUPONE QUE SOLO SE PUEDE ANULAR ALGO COBRADO EN LA FECHA DE HOY. CONFIRMAR SI SOLO SE PUEDE ANULAR ALGO COBRADO HOY
        //AGREGAR LOGICA QUE VERIFIQUE QUE EL COMPROBANTE YA NO ESTE ANULADO !!!!!!!!!!!!!!!!!
        if (!$apertura) {
            $rJson = json_encode(array('ok' => 0, 'msg' => 'La caja se encuentra cerrada.'));
            return $response->setContent($rJson);
        }

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");

        //Codigo de barra recibido
        $cb = trim($cb);

        $bm->setCodigo($cb, $apertura->getFecha());
        $imp = $bm->getImporte($this->container->get("sistemacaja.prorroga"));
        //Se verifica si existe en la base:
        $em = $this->getDoctrine()->getManager();
        $lotes = $em->getRepository('SistemaCajaBundle:Lote')->getLote($apertura->getId(), $cb);

        if ($lotes) {
            $rJson = json_encode(array('ok' => 1, 'detalle' => $this->renderView("SistemaCajaBundle:Apertura:_loteDetalle.html.twig",
                array('elementos' => $lotes->getDetalleNoAnulados(),
                    'ingresado' => $cb))));
        } else {
            $rJson = json_encode(array('ok' => 0, 'msg' => 'No existe el comprobante ingresado, o fue cobrado en otra caja.'));
        }
        return $response->setContent($rJson);
    }

    /**
     * Prepara la ventana desde la cual se puede reenviar un archivo de cobranza que no se haya enviado al cerrar la caja, por algun motivo
     *
     */
    public function prepararEnvioAction($id)
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

        return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Permite reenviar un archivo de cobranza que no se haya enviado al cerrar la caja, por algun motivo
     *
     */
    public function enviarMailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));
        $deleteForm = $this->createDeleteForm($id);

        if ($entity) {
            try {
                $path_archivos = $this->container->getParameter('caja.apertura.dir_files');
                if ($entity->getArchivoCierre()) {
                    $archivo_generado = $entity->getArchivoCierre();
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo recuperar el archivo de cobranza');
                    //Se deberia enviar un mail de todas formas, avisando del error....
                    return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
                }
                $path_documento = $path_archivos . $archivo_generado;

                $fp = fopen($path_documento, "r"); //Apertura para sólo lectura; coloca el puntero al fichero al principio del fichero.
                if (!$fp) { //fopen devuelve un recurso de puntero a fichero si tiene éxito, o FALSE si se produjo un error.
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo recuperar el archivo de cobranza');
                    //Se deberia enviar un mail de todas formas, avisando del error....
                    return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
                } else {
                    fclose($fp);
                }
                $contenido = 'REENVIO - Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado;
                // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc
                $mensaje = \Swift_Message::newInstance()
                    ->setSubject('REENVIO - Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado)
                    ->setFrom('administrador@posadas.gov.ar')
                //->setTo('eduardo4979@gmail.com')
                    ->setBody($contenido)
                    ->attach(\Swift_Attachment::fromPath($path_documento));


                //Recupero los responsables a los cuales se les envia el mail:
                $responsables = $em->getRepository('SistemaCajaBundle:Responsable')->getResponsablesActivos();
                $lista = array();
                foreach ($responsables as $responsable) {
                    $lista[] = $responsable->getEmail();
                }
                if (count($lista) > 0) {
                    $mensaje->setTo($lista);
                    $resultado = $this->container->get('mailer')->send($mensaje);
                    if ($resultado != 0) {
                        $this->get('session')->getFlashBag()->add('success', 'El archivo fue enviado exitosamente.');
                    } else {
                        $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo');
                        //Se deberia enviar un mail de todas formas, avisando del error....
                        return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
                    }
                } else { //No habia destinatarios para enviar el mail
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo. No hay destinatarios');
                    //Se deberia enviar un mail de todas formas, avisando del error....
                    return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
                }

            } catch (Swift_TransportException $e) {
                $em->getConnection()->rollback();
                $em->close();
                //throw $e;
                $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo: ' . $e);
                return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                //throw $e;
                $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo: ' . $e);
                return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
            }
            return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'No se pudieron recuperar los datos de la apertura.');
        }

        return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig',
            array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
    }


    /**
     * FUNCION QUE RECIBE UNA REFERENCIA, UN STRING DE RELLENO Y UNA CANTIDAD DE CARACTERES A DEVOLVER
     *
     */
    public function formateaReferencia($referencia, $string_relleno, $largo_total, $orientacion_relleno = null)
    {

        if ($orientacion_relleno == null) {
            $orientacion_relleno = STR_PAD_RIGHT; //si no se pidio orientacion, por defecto es derecha
        }
        $largo_inicial = strlen(trim($referencia));
        if ($largo_inicial <= $largo_total) { //se rellena con el string de relleno hasta completar el largo solicitado
            $referencia_formateada = $referencia;
            while ($largo_inicial < $largo_total) { //relleno hasta completar
                $referencia_formateada = str_pad($referencia_formateada, $largo_total, $string_relleno, $orientacion_relleno);
                $largo_inicial++;
            }
        } else { //la referencia es mas larga de lo que se debe devolver, se debe truncar y devolver al final puntos suspensivos
            $referencia_formateada = substr($referencia, 0, $largo_total - 2) . "..";
        }
        return $referencia_formateada;

    }

    public function envioMailAction()
    {

        $id_apertura = $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getManager();
        $caja = $this->container->get("caja.manager")->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id_apertura, "caja" => $caja->getId()));

        if (!$entity) { //No se pudo recuperar la apertura
            $ret = array("ok" => 0);
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
        }

        $archivo_generado = $entity->getArchivoCierre();

        if ($entity->getComprobanteCantidad() > 0) { //Hubo cobranza
            $path_archivos = $this->container->getParameter('caja.apertura.dir_files');
            $path_documento = $path_archivos . $archivo_generado;

            $contenido = 'Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado;
            // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc
            $mensaje_detalle = \Swift_Message::newInstance()
                ->setSubject('Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado)
                ->setFrom('administrador@posadas.gov.ar')
            //->setTo('cobros@posadas.gov.ar')
                ->setBody($contenido)
                ->attach(\Swift_Attachment::fromPath($path_documento));
            //Adjunto

            $contenido_resumen = 'Municipalidad de Posadas - Resumen de Cierre de Caja - ' . $archivo_generado;
            // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc

            $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($entity->getId());
            $pagos_anulados = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($entity->getId());
            $comprobantes_validos = $entity->getComprobanteCantidad();
            $comprobantes_anulados = $entity->getComprobanteAnulado();
            $mailContext = array('entity' => $entity, 'validos' => $comprobantes_validos, 'anulados' => $comprobantes_anulados, 'pagos' => $pagos, 'pagos_anulados' => $pagos_anulados);
            $html = $this->container->get('twig')->render('SistemaCajaBundle:Apertura:email.html.twig', $mailContext);
            $mensaje_resumen = \Swift_Message::newInstance()
                ->setSubject('Municipalidad de Posadas - Resumen de Cierre de Caja - ' . $archivo_generado)
                ->setFrom('administrador@posadas.gov.ar')
                ->setBody($contenido_resumen)
                ->addPart($html, 'text/html');


        } else { //No hubo cobranza
            $apertura_id = $entity->getId();
            $numero_caja = $entity->getCaja()->getId();
            $datos_caja = 'Caja: ' . $numero_caja . '- Apertura: ' . $apertura_id . ' - Fecha: ' . $entity->getFechaCierre()->format('d-m-Y');
            $contenido = 'No hubo cobranza en la presente caja: ' . $datos_caja;
            // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc
            $mensaje_detalle = \Swift_Message::newInstance()
                ->setSubject('Municipalidad de Posadas - Detalle de Cierre de Caja - ' . $entity->getFechaCierre()->format('d-m-Y'))
                ->setFrom('administrador@posadas.gov.ar')
                ->setBody($contenido);
            //No hay adjunto

            $contenido_resumen = 'Municipalidad de Posadas - Resumen de Cierre de Caja - ' . $entity->getFechaCierre()->format('d-m-Y');
            $contenido_resumen .= '. No hubo cobranza en la presente caja: ' . $datos_caja;
            // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc
            $mensaje_resumen = \Swift_Message::newInstance()
                ->setSubject('Municipalidad de Posadas - Resumen de Cierre de Caja')
                ->setFrom('administrador@posadas.gov.ar')
                ->setBody($contenido_resumen);
        }
        $lista_detalle = array();
        $lista_resumen = array();
        //Recupero los responsables a los cuales se les envia el mail:
        $responsables = $em->getRepository('SistemaCajaBundle:Responsable')->getResponsablesActivos();
        $ret = array("ok" => 0);
        foreach ($responsables as $responsable) {
            if ($responsable->getDetalle()) { //Si tiene activada la opcion para recibir el archivo con el detalle del cierre
                $lista_detalle[] = $responsable->getEmail();
            }
            if ($responsable->getResumen()) { //Si tiene activada la opcion para recibir el archivo con el resumen del cierre
                $lista_resumen[] = $responsable->getEmail();
            }
        }
        if (count($lista_detalle) > 0) {
            $mensaje_detalle->setTo($lista_detalle);
            $this->container->get('mailer')->send($mensaje_detalle);
            $ret = array("ok" => 1);
        }
        if (count($lista_resumen) > 0) {
            $mensaje_resumen->setTo($lista_resumen);
            $this->container->get('mailer')->send($mensaje_resumen);
            $ret = array("ok" => 1);
        }
        $em->close();
        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;

    }

    /**
     * Prepara la ventana desde la cual se puede reeimprimir un cierre de cobranza
     * que no se haya impreso al cerrar la caja, por algun motivo
     *
     */
    public function prepararReimpresionCierreAction($id)
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

        return $this->render('SistemaCajaBundle:Apertura:reimprimirCierre.html.twig', array('entity' => $entity, 'caja' => $caja, 'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Permite reimprimir un cierre de caja no se haya impreso al cerrar la caja, por algun motivo
     *
     */
    public function reimprimirCierreAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));
        $deleteForm = $this->createDeleteForm($id);
        $msg = false;
        if ($entity) {
            $tk = $this->imprimirCierre($id, 2);
            if ($tk == "") {
                $msg = 'No se pudieron recuperar los datos del cierre.';
            }
        } else {
            $msg = 'No se pudieron recuperar los datos de la apertura.';
        }
        //Verifico si estuvo todo ok, y devuelvo:
        if (!$msg) {
            $ret = array("ok" => 1, "tk" => $tk);
        } else {
            $ret = array("ok" => 0, "msg" => $msg);
        }

        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;
    }


    /*
     * Imprime o reimprime el cierre de caja.
     */
    public function imprimirCierre($id, $tipo_impresion = 1)
    {
        //si el tipo es 1 es el primer cierre, o sea el caso normal
        //si el tipo es 2 es una reimpresion
        $em = $this->getDoctrine()->getManager();
        $tk = "";
        $caja = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));
        $ticket = $this->get("sistemacaja.ticket");
        $servicio_tabla = $this->get("lar.parametro.tabla");
        $bm = $this->container->get("caja.barra");

        // Se ingresa una suerte de encabezado de cierre, para facilitar la division de grupos
        if ($tipo_impresion == 1) {
            $contenido = str_pad("CIERRE DE CAJA EFECTUADO", 40, "-", STR_PAD_BOTH) . NL;
        } else {
            $contenido = str_pad("REIMPRESION DE CIERRE DE CAJA", 40, "-", STR_PAD_BOTH) . NL;
        }
        $contenido .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= "CAJA: " . $caja->getNumero() . NL;
        $contenido .= str_pad("FECHA: " . date("d-m-Y"), 20, " ", STR_PAD_RIGHT) . str_pad("HORA: " . $entity->getFechaCierre()->format("H:i:s"), 19, " ", STR_PAD_LEFT) . NL;

        ///////////////////////////////////////////////////////////////////////////////////////////

        //Primer parte de la impresion: detalle de pagos por tipo de seccion:
        $detalle_pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetallePagos($entity->getId());
        $contenido .= str_pad("Cierre de Caja", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Apertura nro. " . $entity->getId(), 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Cajero: " . $caja->getCajero()->getUsername(), 40, " ", STR_PAD_BOTH) . NL;
        $nombre_seccion_actual = "";
        $monto_total_seccion = 0;
        $cantidad_comprobantes_seccion = 0;
        $monto_total_general = 0;
        $cantidad_comprobantes_general = 0;
        foreach ($detalle_pagos as $detalle) {

            $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
            $seccion = $servicio_tabla->getParametro($tabla, $detalle->getSeccion());
            if ($seccion) {
                $nombre_seccion = $seccion->getDescripcion();
            } else {
                $nombre_seccion = "seccion desconocida";
            }

            //Pregunto si es la misma seccion, o tengo que hacer el "cambio" (corte de control)
            if ($nombre_seccion_actual == "") { //entra la primera vez
                $contenido .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
                $contenido .= str_pad("SECCION: " . $nombre_seccion, 40, " ", STR_PAD_BOTH) . NL;
                //$contenido .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH);
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion += $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                }
                $contenido .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                $nombre_seccion_actual = $nombre_seccion;

                $cantidad_comprobantes_seccion++;
            } else if ($nombre_seccion == $nombre_seccion_actual) { //entra si es igual al anterior
                //$contenido .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH);
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion += $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                }
                $contenido .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
                $seccion_actual = $servicio_tabla->getParametro($tabla, $detalle->getSeccion());
                if ($seccion_actual) {
                    $nombre_seccion_actual = $seccion_actual->getDescripcion();
                } else {
                    $nombre_seccion_actual = "seccion desconocida";
                }

                $cantidad_comprobantes_seccion++;
            } else { //corte de control, immprimo una linea, muestro totales, otra linea y empiezo otra seccion:
                $contenido .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL;
                $contenido .= str_pad($nombre_seccion_actual . ": $ " . sprintf("%9.2f", $monto_total_seccion), 40, " ", STR_PAD_BOTH) . NL;
                $contenido .= str_pad("Comprobantes: " . $cantidad_comprobantes_seccion, 40, " ", STR_PAD_BOTH) . NL;
                $contenido .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
                $monto_total_general += $monto_total_seccion;
                $cantidad_comprobantes_general += $cantidad_comprobantes_seccion;
                $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
                $contenido .= str_pad("SECCION: " . $nombre_seccion, 40, " ", STR_PAD_BOTH) . NL;
                //$contenido .= str_pad($detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH)  . " $ " . sprintf("%9.2f",$detalle->getImporte()), 40, " ", STR_PAD_BOTH) . NL;
                $parcial_1 = $detalle->getComprobante() . " " . $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH);
                if (!$detalle->getAnulado()) {
                    $parcial_2 = " $ " . sprintf("%9.2f", $detalle->getImporte());
                    $monto_total_seccion = $detalle->getImporte();
                } else {
                    $parcial_2 = " ANULADO";
                    $monto_total_seccion = 0;
                }
                $contenido .= str_pad($parcial_1 . $parcial_2, 40, " ", STR_PAD_BOTH) . NL;
                //INICIALIZO LOS ACUMULADORES DE SECCION

                $cantidad_comprobantes_seccion = 1;

                $seccion_actual = $servicio_tabla->getParametro($tabla, $detalle->getSeccion());
                if ($seccion_actual) {
                    $nombre_seccion_actual = $seccion_actual->getDescripcion();
                } else {
                    $nombre_seccion_actual = "seccion desconocida";
                }
            }
        }
        $monto_total_general += $monto_total_seccion;
        $cantidad_comprobantes_general += $cantidad_comprobantes_seccion;
        $contenido .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad($nombre_seccion_actual . ": $ " . sprintf("%9.2f", $monto_total_seccion), 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Comprobantes: " . $cantidad_comprobantes_seccion, 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("-", 40, "-", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("TOTAL COBRADO: $ " . sprintf("%9.2f", $monto_total_general), 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("CANTIDAD DE COMPROBANTES: " . $cantidad_comprobantes_general, 40, " ", STR_PAD_BOTH) . NL;

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Segunda parte de la impresion: detalle de pagos por tipo de pago:
        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($entity->getId());
        $tipoPagos = array();
        foreach ($PagoTipoPago as $tipo) {
            if (!array_key_exists($tipo['id'], $tipoPagos)) {
                $tipoPagos[$tipo['id']] = array($tipo['descripcion'], 0, 0);
            }
            $tipoPagos[$tipo['id']][1] = $tipo['importe'] + $tipo['anulado'];
            $tipoPagos[$tipo['id']][2] = $tipo['anulado'];

        }

        $contenido .= str_pad("=", 40, "=", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Formas de Cobro: ", 40, " ", STR_PAD_RIGHT) . NL;
        foreach ($tipoPagos as $tipoPago) {
            $contenido .= str_pad($tipoPago[0] . ": ", 40, " ", STR_PAD_RIGHT) . NL;
            $contenido .= str_pad("$ " . sprintf("%9.2f", $tipoPago[1]) . " - Anulado: $ " . sprintf("%9.2f", $tipoPago[2]), 40, "-", STR_PAD_LEFT) . NL;

        }

        ///////////////////////////////////////////////////////////////////////////
        //Tercer parte de la impresion: cantidad de comprobantes y montos finales:

        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($entity->getId());
        $pagosAnulado = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($entity->getId());
        //$ticket = $this->get("sistemacaja.ticket");
        $contenido .= str_pad("=", 40, "=", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Apertura nro. " . $entity->getId(), 40, " ", STR_PAD_BOTH) . NL;
        $contenido .= str_pad("Comprobantes Validos: " . $entity->getComprobanteCantidad(), 40, " ", STR_PAD_RIGHT) . NL;
        $contenido .= str_pad("Comprobantes Anulados: " . $entity->getComprobanteAnulado(), 40, " ", STR_PAD_RIGHT) . NL;
        $contenido .= str_pad("Importe Cobrado: $ " . sprintf("%9.2f", $pagos), 40, " ", STR_PAD_RIGHT) . NL;
        $contenido .= str_pad("Importe Anulado: $ " . sprintf("%9.2f", $pagosAnulado), 40, " ", STR_PAD_RIGHT) . NL;

        $ticket->setContenido($contenido);

        $tk .= $ticket->getTicketTestigo();

        // EL RESUMEN SE HACE SOLO EN EL CASO NORMAL (NO REIMPRESION)
        if ($tipo_impresion == 1) {
            //Genero una segunda parte de la impresion, que va hacia afuera, a modo de resumen para el cajero:
            $ticket_resumen = $this->get("sistemacaja.ticket");
            $contenido_resumen = str_pad("=", 40, "=", STR_PAD_BOTH) . NL; //doble linea
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("RESUMEN DE CIERRE DE CAJA", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Apertura nro. " . $entity->getId(), 40, " ", STR_PAD_BOTH) . NL;
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Comprobantes Validos: " . $entity->getComprobanteCantidad(), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Comprobantes Anulados: " . $entity->getComprobanteAnulado(), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Importe Cobrado: $ " . sprintf("%9.2f", $pagos), 40, " ", STR_PAD_RIGHT) . NL;
            $contenido_resumen .= str_pad("Importe Anulado: $ " . sprintf("%9.2f", $pagosAnulado), 40, " ", STR_PAD_RIGHT) . NL;

            //Se agrega un resumen por tipo de cobro:
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("Formas de Cobro: ", 40, " ", STR_PAD_RIGHT) . NL;
            foreach ($tipoPagos as $tipoPago) {
                $contenido_resumen .= str_pad($tipoPago[0] . ": ", 40, " ", STR_PAD_RIGHT) . NL;
                $contenido_resumen .= str_pad("$ " . sprintf("%9.2f", $tipoPago[1]) . " - Anulado: $ " . sprintf("%9.2f", $tipoPago[2]), 40, "-", STR_PAD_LEFT) . NL;
            }

            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad(" ", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("_________________________", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("RECIBIDO POR JEFE DE CAJA", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $contenido_resumen .= str_pad("DIRECCION DE TESORERIA - MUN. POSADAS", 40, " ", STR_PAD_BOTH) . NL; //linea en blanco
            $ticket->setContenido($contenido_resumen);

            $tk .= $ticket->getTicketFull();
        }
        return $tk;
    }

    /**
     * Prepara la ventana desde la cual se puede reimprimir un ticket de cobranza
     * que no se haya impreso al momento del cobro, por algun motivo.
     * Lista los tickets cobrados en la caja actual
     *
     */
    public function mostrarReimpresionTicketsAction($id)
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
        } else {
            $entities = $em->getRepository('SistemaCajaBundle:Apertura')->getDetallePagos($entity->getId());
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Apertura:mostrarReimpresionTickets.html.twig', array('entities' => $entities, 'caja' => $caja, 'delete_form' => $deleteForm->createView(),));
    }


    /**
     * Prepara la ventana desde la cual se puede reeimprimir un ticket
     * que no se haya impreso al momento del registro, por algun motivo.
     * Es como una ventana previa o de confirmación, antes de reimprimir.
     *
     */
    public function prepararReimpresionTicketAction($id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Ver");

        $em = $this->getDoctrine()->getManager();

        $caja = $this->container->get('caja.manager')->getCaja();
        $detalle = $em->getRepository('SistemaCajaBundle:LoteDetalle')->findOneBy(array('id' => $id));

        if (!$detalle) {
            throw $this->createNotFoundException('Unable to find LoteDetalle entity.');
        }

        $bm = $this->container->get("caja.barra");
        $servicio_tabla = $this->get("lar.parametro.tabla");
        $tabla = $bm->getTablaSeccionByCodigoBarra($detalle->getCodigoBarra());
        $seccion = $servicio_tabla->getParametro($tabla, $detalle->getSeccion());
        if ($seccion) {
            $seccion = $seccion->getDescripcion();
        } else {
            $seccion = "seccion desconocida";
        }
        $referencia = $this->formateaReferencia($detalle->getReferencia(), " ", 17, STR_PAD_BOTH);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Apertura:reimprimirTicket.html.twig', array('entity' => $detalle, 'caja' => $caja, 'delete_form' => $deleteForm->createView(), 'seccion' => $seccion, 'referencia' => $referencia));
    }


    /**
     * Reimprime un ticket que no se haya impreso al momento del cobro, por algun motivo
     *
     */
    public function reimprimirTicketAction($id)
    {

        $tk = "";
        $ticket = $this->get("sistemacaja.ticket");
        $msg = false;
        $em = $this->getDoctrine()->getManager();
        $caja = $this->container->get('caja.manager')->getCaja();
        $detalle = $em->getRepository('SistemaCajaBundle:LoteDetalle')->findOneBy(array('id' => $id));
        if ($detalle) {
            $ticket->setContenido(array(
                    array("REIMP.Compr." . $detalle->getComprobante(), sprintf("%9.2f", $detalle->getImporte())))
            );
            $ticket->setValores(array(
                'ticket' => $detalle->getId(),
                'codigobarra' => $detalle->getCodigoBarra(),
                'referencia' => $detalle->getReferencia()
            ));
            $tk .= $ticket->getTicketFull();
            $tk .= $ticket->getTicketTestigo();

        } else {
            $msg = 'No se pudieron recuperar los datos del comprobante.';
        }

        if (!$msg) {
            $ret = array("ok" => 1, "tk" => $tk);
        } else {
            $ret = array("ok" => 0, "msg" => $msg);
        }

        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;

    }

    /**
     * @return coleccion de comprobantes del tipo recibido
     */
    public function masInfoAction()
    {
        $tipo_seccion = $this->getRequest()->get('tipo_seccion');
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('success', 'No hay apertura abierta');
            return $this->redirect($this->generateUrl('apertura_new'));
        }
        $detalle_pagos = $this->getDetalleTodosPagosSeccion($apertura->getId(), $tipo_seccion);
        return $this->render('SistemaCajaBundle:Apertura:masinfo.html.twig',
            array('detalle_pagos' => $detalle_pagos));

    }

    private function getDetalleTodosPagosSeccion($ap_id, $tipo_seccion)
    {
        $em = $this->getDoctrine()->getManager();
        $pagos = $em->getRepository('SistemaCajaBundle:Apertura')->getDetalleTodosPagosSeccion($ap_id, $tipo_seccion);

        $array_detalle_pagos = array();

        foreach ($pagos as $pago) {

            if (!array_key_exists($pago['id'], $array_detalle_pagos)) {
                $array_detalle_pagos[$pago['id']] = array($pago['comprobante'], 0, 0, "", "");
            }

            $array_detalle_pagos[$pago['id']][1] = $pago['importe'];
            $array_detalle_pagos[$pago['id']][2] = $pago['anulado'];
            $array_detalle_pagos[$pago['id']][3] = $pago['comprobante'];
            $array_detalle_pagos[$pago['id']][4] = $pago['referencia'];
        }
        //ladybug_dump_die($array_detalle_pagos);
        return $array_detalle_pagos;
    }

    /**
     * @return Array, un array con los nombres de los actions excluidos
     */
    function getNoAuditables()
    {
        return array();
    }

}
