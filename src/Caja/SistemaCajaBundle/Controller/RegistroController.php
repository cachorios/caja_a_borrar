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

use Symfony\Component\HttpFoundation\Response;
use Caja\SistemaCajaBundle\Entity\Lote;
use Caja\SistemaCajaBundle\Form\RegistroType;

class RegistroController extends Controller
{
    public function registroAction()
    {
        $lote = new Lote();

        $form = $this->createForm(new RegistroType(), $lote);

        $caja = $this->container->get('caja.manager')->getCaja();
        $apertura = $this->container->get('caja.manager')->getApertura();

        return $this->render("SistemaCajaBundle:Registro:registro.html.twig",array(
            "lote" => $lote,
            "form" => $form->createView(),
            "caja" => $caja,
            "apertura" => $apertura
            )
        );

    }

    public function createAction()
    {

    }


    public function barraDetalleAction()
    {
		$response = new Response();
		$log = $this->get('logger');

		$cb = $this->getRequest()->get('cb');

		$log->info("---> Codigo de barra: $cb" );
		//Codigo de barra recibido


		//$cb = substr($cb,2);
		$cb = trim($cb);



		$apertura = $this->container->get("caja.manager")->getApertura();

		//Servicio de codigo de barra, para interpretarlo
		$bm = $this->container->get("caja.barra");
//        $bm->setCodigo("93390001234513015201101000000123400001231000");


		$bm->setCodigo($cb, $apertura->getFecha());

		$imp = $bm->getImporte();

		if($imp>0){
			$rJson = json_encode(array(	'ok' => 1,
										'importe' => number_format($imp,2),
										'comprobante' => $bm->getComprobante(),
										'vencimiento' =>$bm->getVto(),
										'detalle' => $this->renderView("SistemaCajaBundle:Registro:_detalle.html.twig" ,array('elementos' => $bm->getDetalle()))
					));
		}else{

			$rJson = json_encode(array(
				'ok' => 0,
				'msg'=> count($bm->getDetalle())==0 ? 'Codigo de Barra desconocido' : 'Comprobante vencido'
			));
		}

		return $response->setContent($rJson);
    }


}
