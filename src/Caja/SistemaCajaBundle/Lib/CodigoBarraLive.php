<?php
namespace Caja\SistemaCajaBundle\Lib;

include_once(__DIR__ . '/../Lib/barra_utils.php');

/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 17/01/13
 * Time: 22:30
 * To change this template use File | Settings | File Templates.
 */
class CodigoBarraLive
{
    private $em;
    /**
     * @var \Lar\ParametroBundle\Lib\LarParametroService
     */
    private $tabla_man;

    /**
     * @var string
     */
    private $codigo;
    private $cbReg;
    private $detalle;
    private $comprobante;

    public function __construct($em, $tabla_man)
    {
        $this->em = $em;
        $this->tabla_man = $tabla_man;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = trim($codigo);
        $this->identificarCodigo();
    }

    public function getDetalle()
    {
        return $this->detalle;
    }


    private function identificarCodigo()
    {
        //Recorrer los codigo hasta encontrar el que corresponda con la longitud y el identidicador
        // primero recuperar los codigos de la longitud

        $regs = $this->em->getRepository("SistemaCajaBundle:CodigoBarra")->findBy(array("longitud" =>strlen($this->codigo)));

        foreach ($regs as $reg) {
            if ($this->obtenerIdentificador($reg->getIdentificador()) == $reg->getValor()) {
                $this->cbReg = $reg;

                $this->detalle = $this->def2Array($reg);
                return true;
            }
        }
        return false;

    }

    private function obtenerIdentificador($id)
    {
        $pos = preg_split('/\[+(\d+),(\d+)\]/', $id, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $ret = "";
        for ($i = 0; $i < count($pos); $i = $i + 2) {
            $ret .= substr($this->codigo, $pos[$i] - 1, $pos[$i + 1] + 0);
        }
        return $ret;
    }


    private function def2Array($cb)
    {

        $det = $cb->getPosiciones();
        $aDet = array();

        foreach ($det as $pos) {
            if ($pos->getEstado() == 2) {
                $this->comprobante = substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud());
            }

            $ls_desc_tab = substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud());
            if ($pos->getTabla() > 0) {
                $t = $this->tabla_man->getParametro($pos->getTabla(), substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud()));
                if ($t && $t->getTabla() > 1) {
                    $ls_desc_tab = $t->getDescripcion();
                }
            }

            $aDet[] = array(
                $pos->getPosicion(),
                $pos->getDescripcion(),
                $ls_desc_tab,
            );

        }

        return $aDet;
    }

    public function getComprobante()
    {
        return $this->comprobante;
    }


    public function getVtosImportes()
    {
        $vtos = $this->cbReg->getVtosImportes();
        $aVtos = array();

        foreach ($vtos as $vto) {
            $aVtos[] = array(
                $this->evalExp($this->strReplacePos($vto->getVencimiento(), true)),
                $this->evalExp($this->strReplacePos($vto->getImporte())));
        }
        return $aVtos;
    }

    private function strReplacePos($cadena, $esStr = false)
    {

        $patrones = array();
        $sust = array();
        $delim = "";
        if ($esStr) {
            $delim = "'";
        }
        foreach ($this->detalle as $det) {
            $patrones[] = "/\[" . $det[0] . "\]/";
            $sust[] = $delim . $det[2] . $delim;
        }
        $ret = preg_replace($patrones, $sust, $cadena);
        return $ret;
    }

    private
    function evalExp($exp)
    {
       eval("\$ret=".$exp.";");

        //$fn = create_function("", "return ({$exp});");
        //$ret = $fn();
        return $ret;
    }


}



