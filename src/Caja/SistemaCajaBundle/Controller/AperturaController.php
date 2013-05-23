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

/**
 * Apertura controller.
 *
 */
class AperturaController extends Controller {
    /**
     * Lists all Apertura entities.
     *
     */
    public function indexAction() {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));

        list($filterForm, $queryBuilder) = $this->filter();
        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return $this->render('SistemaCajaBundle:Apertura:index.html.twig',
                             array('entities' => $entities, 'pagerHtml' => $pagerHtml, 'filterForm' => $filterForm->createView(),));
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter() {
        $request      = $this->getRequest();
        $session      = $request->getSession();
        $filterForm   = $this->createForm(new AperturaFilterType());
        $em           = $this->getDoctrine()->getManager();
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
    protected function paginator($queryBuilder) {
        // Paginator
        $adapter     = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta  = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me             = $this;
        $routeGenerator = function ($page) use ($me) {
            return $me->generateUrl('apertura', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view       = new TwitterBootstrapView();
        $pagerHtml  = $view->render($pagerfanta, $routeGenerator, array('proximity' => 3,
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
    public function showAction($id) {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Ver");

        $em = $this->getDoctrine()->getManager();

        $caja   = $this->container->get('caja.manager')->getCaja();
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
    public function newAction() {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Nuevo");

        $entity = new Apertura();
        $form   = $this->createForm(new AperturaType(), $entity);

        return $this->render('SistemaCajaBundle:Apertura:new.html.twig', array('entity' => $entity, 'form' => $form->createView(),));
    }

    /**
     * Creates a new Apertura entity.
     *
     */
    public function createAction() {

        $entity  = new Apertura();
        $request = $this->getRequest();
        $form    = $this->createForm(new AperturaType(), $entity);

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
                $this->get('session')->getFlashBag()->add('success', 'Apertura creada exitosamente');

                return $this->redirect($this->generateUrl('apertura'));
            } else {

                $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            }
        }
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        return $this->render('SistemaCajaBundle:Apertura:new.html.twig', array('entity' => $entity, 'form' => $form->createView(),));
    }

    /**
     * Displays a form to edit an existing Apertura entity.
     *
     */
    public function editAction($id) {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Editar");

        $em = $this->getDoctrine()->getManager();

        $caja   = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }

        $editForm   = $this->createForm(new AperturaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SistemaCajaBundle:Apertura:edit.html.twig',
                             array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Edits an existing Apertura entity.
     *
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();

        $caja   = $this->container->get('caja.manager')->getCaja();
        $entity = $em->getRepository('SistemaCajaBundle:Apertura')->findOneBy(array('id' => $id, "caja" => $caja->getId()));
        $fecha  = $entity->getFecha();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }

        $editForm   = $this->createForm(new AperturaType(), $entity);
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

    public function cierreAction() {
        $entity = $this->container->get('caja.manager')->getApertura();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Apertura entity.');
        }
        $entity->setFechaCierre(new \DateTime());
        $entity->setDireccionIp($_SERVER['REMOTE_ADDR']);
        $entity->setHost($_SERVER['HTTP_HOST']);

        $editForm = $this->createForm(new AperturaCierreType(), $entity);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();

                /*
                $caja   = $this->container->get('caja.manager')->getCaja();
                $apertura = $this->container->get('caja.manager')->getApertura();

                $pagos  = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($apertura->getId());
                //Guardo un registro de cierre, a modo de auditoria:
                $cierre_caja = new CierreCaja();
                $cierre_caja->setFecha($entity->getFecha());//Es la fecha a la cual corresponde el cierre
                $cierre_caja->setImporte($pagos);/////////////////////////////////////////////////////////////
                $cierre_caja->setFechaCierre(new \DateTime()); //Es la fecha en la cual se efectua el cierre
                $cierre_caja->setDireccionIp($_SERVER['REMOTE_ADDR']);
                $cierre_caja->setHost($_SERVER['HTTP_HOST']);
                $cierre_caja->setCajero($caja->getCajero()->getUsername());
                $cierre_caja->setNroCaja( $caja->getNumero());
                $em->persist($cierre_caja);
                //Guardo un registro de cierre, a modo de auditoria
                */

                //Genero el archivo de texto que se envia por mail. CONSIDERACIONES GENERALES:
//                El archivo con el detalle de las cobranzas debería tener  la siguiente estructura.
//                Solicito que el nombre comience con EP seguido de la fecha de cobro en formado DDMMAA. EJ:
//                Cobranzas del Día 18 de Marzo del 2013, EP180313.TXT – Cobranzas del Día 2 de Mayo del 2013, EP020513.TXT,
//                dado que la incorporación de datos a nuestros sistema esta automatizada
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
                // Direccion de correo: cobros@posadas.gov.ar
                $apertura_id = $entity->getId();
                $numero_caja = $entity->getCaja()->getId();
                $path_archivos = $this->container->getParameter('caja.apertura.dir_files');
                $archivo_generado = $em->getRepository('SistemaCajaBundle:Apertura')->generaArchivoTexto($apertura_id, $numero_caja, $path_archivos);

                if (!$archivo_generado) {
                    $this->get('session')->getFlashBag()->add('error', '¡¡¡ Error al generar el archivo de texto que se envia por mail !!!!!');
                    return $this->render('SistemaCajaBundle:Apertura:cierre.html.twig', array('entity' => $entity, 'edit_form' => $editForm->createView(),));
                }
                $path_documento = $path_archivos.$archivo_generado.'.txt';

                $contenido = 'Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado . '.txt';
                // En el contenido se podria incluir la cantidad de comprobantes cobrados, el monto total, la fecha, numero de caja, cajero, etc
                $mensaje = \Swift_Message::newInstance()
                    ->setSubject('Municipalidad de Posadas - Cierre de Caja - ' . $archivo_generado . '.txt')
                    ->setFrom('administrador@posadas.gov.ar')
                    ->setTo('eduardo4979@gmail.com')
                    ->setBody($contenido)
                    ->attach(\Swift_Attachment::fromPath($path_documento));

                /*
                $mensaje->setTo(array(
                    "luis_schw@hotmail.com" => "Luis",
                    "eduardo4979@gmail.com" => "Edu",
                    "cachorios@gmail.com" => "Cacho",
                    "diegoakrein@gmail.com" => "Diego"
                ));
                */
                $this->container->get('mailer')->send($mensaje);

                //Por ultimo: guardo en la tabla Apertura el nombre del archivo generado:
                $entity->setArchivoCierre($archivo_generado.'.txt');
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'La caja se cerro correctamente');

                return $this->redirect($this->generateUrl('apertura'));
                //return $this->redirect($this->generateUrl('home_page'));

            } else {
                $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
            }
        }

        return $this->render('SistemaCajaBundle:Apertura:cierre.html.twig', array('entity' => $entity, 'edit_form' => $editForm->createView(),));
    }

    /**
     * Deletes a Apertura entity.
     *
     */
    public function deleteAction($id) {
        $form    = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
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

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }

    public function monitorAction() {
        $caja     = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('success', 'No hay apertura abierta');
            return $this->redirect($this->generateUrl('apertura_new'));
        }

        $em = $this->getDoctrine()->getManager();

        $pagos        = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagos($apertura->getId());
        $pagosAnulado = $em->getRepository('SistemaCajaBundle:Apertura')->getImportePagosAnulado($apertura->getId());

        $DetalleTipoPago = $this->getDetalleTipoPago($apertura->getId());

        return $this->render('SistemaCajaBundle:Apertura:monitor.html.twig',
                             array('caja' => $caja, 'apertura' => $apertura, 'importe_pago' => $pagos, 'pagos_anulado' => $pagosAnulado,
                                   'detallepago' => $DetalleTipoPago));
    }

    private function getDetalleTipoPago($ap_id) {
        $em           = $this->getDoctrine()->getManager();
        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($ap_id);

        $tipoPago = array();

        foreach ($PagoTipoPago as $tipo) {
            if (!array_key_exists($tipo['id'], $tipoPago)) {
                $tipoPago[$tipo['id']] = array($tipo['descripcion'], 0, 0);
            }
            $tipoPago[$tipo['id']][1] = $tipo['1'];
            /*
            if ($tipo['anulado'] == 1) {
                $tipoPago[$tipo['id']][2] = $tipo['1'];
            } else {
                $tipoPago[$tipo['id']][1] = $tipo['1'];
            }
            */
        }
        return $tipoPago;
    }

    public function anularAction() {

        $lote = new Lote();
        $lote->addPago(new LotePago());
        $form = $this->createForm(new AperturaAnularType(), $lote);

        $caja     = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        //Si no hay apertura activa, no dejo hacer nada:
        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('error', 'No existe una apertura activa.');
            return $this->redirect($this->generateUrl('apertura_new'));
        }

        return $this->render('SistemaCajaBundle:Apertura:anular.html.twig',
                             array('caja' => $caja, "form" => $form->createView(), 'apertura' => $apertura));
    }

    public function anularComprobanteAction() {
        //En vez de crear un nuevo lote, recupero el actual:
        //CONTROLAR QUE EL METODO SEA SIEMPRE POST !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        // A partir del comprobante ingresado, recupero el lote al cual pertenece:
        $request = $this->getRequest();
        $comprobantes = $request->get('comprobantes_anular');         //Recupero los comprobantes que fueron seleccionados para anular:
        if (count($comprobantes) == 0) {
            $this->get('session')->getFlashBag()->add('error', 'No se ingresaron comprobantes para anular.');
            return $this->redirect($this->generateUrl('apertura_anulado'));
        }

        $caja     = $this->container->get("caja.manager")->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('error', 'No existe una apertura activa.');
            return $this->redirect($this->generateUrl('apertura_new '));
        }

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");

        //Se verifica si existe en la base el comprobante ingresado:
        $em   = $this->getDoctrine()->getManager();
        $lote = $em->getRepository('SistemaCajaBundle:Lote')->getLote($apertura->getId(), $comprobantes[0]);

        if ($lote != null) {
            $lote_id                 = $lote->getId();
            $total_comprobantes_lote = $lote->getDetalle()->count();
            $total_comprobantes_seleccionados = count($comprobantes);

            //Pregunto cuantos tipos de pagos se hicieron en ese lote:
            $cantidad_pagos = $em->getRepository('SistemaCajaBundle:Lote')->getConsultaCantidadPagos($lote_id);

            if ($cantidad_pagos == 1) {
                // si es un solo pago se puede anular el comprobante, independientemente del tipo de pago
                $tipo_pago_divisible = $em->getRepository('SistemaCajaBundle:Lote')->getConsultaTipoPago($lote_id); //recupero el tipo de pago

                //el tipo de pago en el registro negativo sera el obtenido en la linea anterior ($tipo_pago_divisible)
                //Si el pago no fue en efectivo, solo se puede anular el lote completo:
                if (($tipo_pago_divisible != 1) && (count($comprobantes) < $total_comprobantes_lote)) {
                    $this->get('session')->getFlashBag()->add('error', 'El lote no fue abonado en efectivo, solo se puede anular de manera total.');
                    return $this->redirect($this->generateUrl('apertura_anulado'));
                }
            } else { //Se pago con mas un tipo de pago, entonces:
                // se anula todo el lote, o se anula hasta donde le alcance el efectivo
               if ($total_comprobantes_seleccionados < $total_comprobantes_lote ) { //SE ANULA PARTE DEL LOTE:
                     //Se selecciono parte del lote, se anula hasta donde le alcance el efectivo:
                    //Comparo el monto abonado en efectivo para ese lote con el monto del comprobante/s a anular
                    $efectivo = $em->getRepository('SistemaCajaBundle:Lote')->getMontoEfectivo($lote_id);

                    //Calculo el monto de los comprobantes seleccionados para anular:
                    $monto_a_anular = 0;
                    foreach ($comprobantes as $comprobante) {
                        $bm->setCodigo($comprobante, $apertura->getFecha());
                        $monto_a_anular += $bm->getImporte();
                    }
                    //Se verifica que se hayan seleccionado todos los comprobantes que componen el lote:
                    if ($monto_a_anular > $efectivo) {
                        $this->get('session')->getFlashBag()
                            ->add('error', 'El monto de los comprobantes seleccionados ($' . $monto_a_anular . ') es mayor al importe abonado en efectivo ($' . $efectivo . ')' );
                        return $this->redirect($this->generateUrl('apertura_anulado'));
                    }
                }
                $tipo_pago_divisible = 1; //Se asume que se va a cancelar hasta donde le alcance el efectivo,
                // por lo cual el tipo de pago en el registro negativo sera 1 (efectivo)
            }

            $em->getConnection()->beginTransaction(); // suspende la consignación automática
            try {
                $lote_id = $lote->getId();

                //Se procede a anular el o los lotes detalles:
                $lotes_actualizados = $em->getRepository('SistemaCajaBundle:Lote')->anularLoteDetallePorComprobantes($comprobantes, $lote_id);

                // Verifico que haya anulado la totalidad de los comprobantes ingresados:
                if ($lotes_actualizados != count($comprobantes)) {
                    $this->get('session')->getFlashBag()->add('error', 'No se pudieron anular los comprobantes seleccionados.');
                    return $this->redirect($this->generateUrl('apertura_anulado'));
                }

                $tipo_pago = $em->getRepository('SistemaCajaBundle:TipoPago')->getTipoPagoDivisible($tipo_pago_divisible); //Recupero el tipo de pago:
                if (!$tipo_pago) {
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo recuperar el tipo de pago.');
                    return $this->redirect($this->generateUrl('apertura_anulado'));
                }

                //Por cada comprobante anulado, se mete un registro "negativo" en el lote de pago
                foreach ($comprobantes as $comprobante) {
                    $bm->setCodigo($comprobante, $apertura->getFecha());
                    $importe = -$bm->getImporte();

                    $em->getRepository('SistemaCajaBundle:LotePago')->anularLotePagoPorComprobantes($lote, $tipo_pago, $importe);
                }

                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                //throw $e;
                $this->get('session')->getFlashBag()->add('error', 'Hubo un fallo al guardar los datos: '.$e);
                return $this->redirect($this->generateUrl('apertura_anulado'));
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Alguno de los comprobantes seleccionados es incorrecto.');
            return $this->redirect($this->generateUrl('apertura_anulado'));
        }

        $this->get('session')->getFlashBag()->add('success', 'Los comprobantes seleccionados han sido anulados');
        return $this->redirect($this->generateUrl('apertura_anulado'));
    }

    /**
     * Método usado para verificar la existencia de un comprobante que se desea anular
     */
    public function existeComprobanteAction() {
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
        $imp = $bm->getImporte();
        //Se verifica si existe en la base:
        $em    = $this->getDoctrine()->getManager();
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
    public function prepararEnvioAction($id) {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("home_page"));
        $breadcrumbs->addItem("Apertura", $this->get("router")->generate("apertura"));
        $breadcrumbs->addItem("Ver");

        $em = $this->getDoctrine()->getManager();

        $caja   = $this->container->get('caja.manager')->getCaja();
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
    public function enviarMailAction($id) {
        $em = $this->getDoctrine()->getManager();

        $caja   = $this->container->get('caja.manager')->getCaja();
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
                $path_documento = $path_archivos.$archivo_generado;

                $fp = fopen($path_documento, "r"); //Apertura para sólo lectura; coloca el puntero al fichero al principio del fichero.
                if (!$fp) {//fopen devuelve un recurso de puntero a fichero si tiene éxito, o FALSE si se produjo un error.
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


                $mensaje->setTo(array(
                    "luis_schw@hotmail.com" => "Luis",
                    "eduardo4979@gmail.com" => "Edu",
                    "cachorios@gmail.com" => "Cacho",
                    "diegoakrein@gmail.com" => "Diego"
                ));

                $resultado = $this->container->get('mailer')->send($mensaje);
                /*
                if (!$this->container->get('mailer')->send($mensaje, $failures)) {
                    echo "Failures:";
                    print_r($failures);
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo');
                }
                */
                if ($resultado != 0) {
                    $this->get('session')->getFlashBag()->add('success', 'El archivo fue enviado exitosamente.');
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo');
                    //Se deberia enviar un mail de todas formas, avisando del error....
                    return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
                }
            } catch (Swift_TransportException $e) {
                $em->getConnection()->rollback();
                $em->close();
                //throw $e;
                $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo: '.$e);
                return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                //throw $e;
                $this->get('session')->getFlashBag()->add('error', 'No se pudo enviar el archivo: '.$e);
                return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
            }
            $deleteForm = $this->createDeleteForm($id);
            return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm->createView(),));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'No se pudieron recuperar los datos de la apertura.');
        }

        return $this->render('SistemaCajaBundle:Apertura:enviar.html.twig',
            array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(),));
    }

}
