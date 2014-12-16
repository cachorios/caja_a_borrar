<?php
namespace Caja\SistemaCajaBundle\Controller;

/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 21/01/13
 * Time: 19:29
 * To change this template use File | Settings | File Templates.
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Caja\SistemaCajaBundle\Entity\Lote;
use Caja\SistemaCajaBundle\Entity\LotePago;
use Caja\SistemaCajaBundle\Form\RegistroType;

use Common\AuditorBundle\Lib\IControllerAuditable;

class RegistroController extends Controller implements IControllerAuditable
{
    public function registroAction()
    {
        $lote = new Lote();
        $lote->addPago(new LotePago());
        $form = $this->createForm(new RegistroType(), $lote);
        $em = $this->getDoctrine()->getManager();
        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();
        $puesto = $apertura->getHabilitacion()->getPuesto();
        $habilitacion = $em->getRepository('SistemaCajaBundle:Habilitacion')->findOneBy(array('id' => $apertura->getHabilitacion()));
        $em = $this->getDoctrine()->getManager();
        $tipoPago = $em->getRepository("SistemaCajaBundle:TipoPago")->getDefaulPago();
        return $this->render("SistemaCajaBundle:Registro:registro.html.twig", array(
                "lote" => $lote,
                "habilitacion" => $habilitacion,
                "form" => $form->createView(),
                "caja" => $caja,
                "apertura" => $apertura,
                'puesto' => $puesto,
                "tipoPagoDefault" => $tipoPago
            )
        );

    }

    public function createAction()
    {

        $lote = new Lote();
        $request = $this->getRequest();
        $form = $this->createForm(new RegistroType(), $lote);

        $form->bind($request);

        $caja = $this->container->get("caja.manager")->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        if (!$apertura) {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            return $this->redirect($this->generateUrl('apertura'));
        }

        $lote->setApertura($apertura);


        $response = new Response();
        if ($form->isValid()) {

            if (strlen($msg = $this->validarDetallesPagos($lote)) == 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($lote);
                $em->flush();

                //Aqui debe retornar al timbrado de cada comprobante

                $tk = "";
                foreach ($lote->getDetalle() as $detalle) {
                    $ticket = $this->get("sistemacaja.ticket");
                    $ticket->setContenido(array(
                            array("Comprobante " . $detalle->getComprobante(), $detalle->getImporte()),
                        )

                    );
                    $ticket->setValores(array(
                        'ticket' => $detalle->getId(),
                        'codigobarra' => $detalle->getCodigoBarra(),
                        'referencia' => $detalle->getReferencia()
                    ));
                    $tk .= $ticket->getTicketFull();
                    $tk .= $ticket->getTicketTestigo();

                }
                $response->setContent(json_encode(array(
                    "ok" => 1,
                    "ticket" => $tk
                )));


            } else {
                $response->setContent(json_encode(array(
                    "ok" => 0,
                    "error" => $msg
                )));
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.create.error');
            $response->setContent(json_encode(array(
                "ok" => false,
                "error" => 'Error de creacion'
            )));

        }

        return $response;

    }

    private function validarDetallesPagos(Lote $lote)
    {
        $msgError = '';
        $tDetalle = 0;
        $tPago = 0;
        foreach ($lote->getDetalle() as $det) {
            $det->setFecha(new \DateTime());
            $det->setLote($lote);

            if ($det->getCodigoBarra() == null || strlen(trim($det->getCodigoBarra())) == 0) {
                $msgError += "Falta Codigo de Barra\n";
            }

            if ($det->getComprobante() == null || strlen(trim($det->getComprobante())) == 0) {
                $msgError += "Comprobante\n";
            }

            if ($det->getImporte() == null || $det->getImporte() <= 0) {
                $msgError += "Importe invalido\n";
            } else {
                $tDetalle += $det->getImporte();
            }

        }

        foreach ($lote->getPagos() as $pago) {
            $pago->setFecha(new \DateTime());
            $pago->setLote($lote);

            if ($pago->getImporte() == null || $pago->getImporte() <= 0) {
                $msgError += "Pago, Importe invalido\n";
            } else {
                $tPago += $pago->getImporte();
            }
        }

        if (abs($tDetalle - $tPago) > 0.001) {
            $msgError += "Error de totales";
        }

        return $msgError;
    }

    public function barraDetalleAction()
    {

        $imp = 0;
        $response = new Response();

        $cb = $this->getRequest()->get('cb');
        //Codigo de barra recibido
        $cb = trim($cb);

        $apertura = $this->container->get("caja.manager")->getApertura();

        //Verificar si ya no se cobro
        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository("SistemaCajaBundle:LoteDetalle")->findBy(array('codigo_barra' => $cb));

        if (count($res) > 0) {
            $rJson = json_encode(array(
                'ok' => 0,
                'msg' => "Este comprobante ya se ha registrado el dia " . $res[0]->getFecha()->format('d-m-Y H:i:s')
            ));
            return $response->setContent($rJson);
        }

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");
        $bm->setCodigo($cb, $apertura->getFecha());
        //valido el tipo de seccion del codigo:
        if (!$bm->validarSeccion()) {
            $rJson = json_encode(array(
                'ok' => 0,
                'msg' => "Seccion No Reconocida - Codigo de Barra Invalido"
            ));
            return $response->setContent($rJson);
        }
        $imp = $bm->getImporte($this->container->get("sistemacaja.prorroga"));

        if ($imp > 0) {
            $rJson = json_encode(array('ok' => 1,
                'importe' => number_format($imp, 2, '.', ''),
                'comprobante' => $bm->getComprobante(),
                'seccion' => $bm->getSeccion(),
                'vencimiento' => $bm->getVto(),
                'referencia' => $bm->getReferencia(),
                'detalle' => $this->renderView("SistemaCajaBundle:Registro:_detalle.html.twig", array('elementos' => $bm->getDetalle()))
            ));
        } else {

            $rJson = json_encode(array(
                'ok' => 0,
                'msg' => count($bm->getDetalle()) == 0 ? 'Codigo de Barra desconocido' : 'El comprobante se encuentra vencido'
            ));
        }

        return $response->setContent($rJson);

    }

    /**
     * @return Array, un array con los nombres de los actions excluidos
     */
    function getNoAuditables()
    {
        return array();
    }

    /**
     * @return Array, un array asociativo con los datos que se estan creando / modificando / eliminando / imprimiendo
     */
    function getDatosAuditoria()
    {
        return null;
    }
}
