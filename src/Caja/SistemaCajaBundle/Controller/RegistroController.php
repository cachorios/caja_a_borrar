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
}
