<?php
namespace Caja\SistemaCajaBundle\Lib;

include_once(__DIR__ . '/../Lib/barra_utils.php');

use Caja\SistemaCajaBundle\Lib\ProrrogaService;

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
    private $logger;
    /**
     * @var string
     */
    private $codigo;
    private $cbReg;
    private $detalle;
    private $detalleVisible;
    private $fechaCalculo;
    private $comprobante;
    private $seccion;
    private $vtos;
    private $conReferencia;


    public function __construct($em, $tabla_man, $logger = null)
    {
        $this->em = $em;
        $this->tabla_man = $tabla_man;
        $this->logger = $logger;
        $this->comprobante = 0;

        $this->detalleVisible = array();
        $this->vtos = array();
    }

    public function setCodigo($codigo, $fecha = null)
    {
        $this->codigo = trim($codigo);
        $this->fechaCalculo = $fecha == null ? new \DateTime('now') : $fecha;

        if ($this->identificarCodigo()) {
            $this->vtos = $this->getVtosImportes();
        }

    }

    public function getDetalle()
    {
        return $this->detalleVisible;
    }

    public function getComprobante()
    {
        return $this->comprobante;
    }

    public function getSeccion()
    {
        return $this->seccion;
    }


    /**
     * getTablaSeccion
     *
     * Retorna la tabla utilizada en la seccion
     * @return int
     */
    public function getTablaSeccionByCodigoBarra($codigo)
    {
        $tabla = null;
        $this->codigo = trim($codigo);
        if ($this->identificarCodigo(false)) {
            foreach ($this->cbReg->getPosiciones() as $pos) {
                if ($pos->getSeccion())
                    if ($pos->getTabla() > 0)
                        $tabla = $pos->getTabla();
            }
        }

        return $tabla;
    }


    public function getConReferencia()
    {
        return $this->conReferencia;
    }

    public function getImporte(ProrrogaService $oProrroga)
    {

        foreach ($this->vtos as $vto) {
            $lVto = $oProrroga->getVencimiento($vto[0]);
            $this->logger->info("-->: " . $this->fechaCalculo->format('Y-m-d') . ' - ' . $lVto->format('Y-m-d'));
            if ($this->fechaCalculo->format('Y-m-d') <= $lVto->format('Y-m-d')) {
                return $vto[1];
            }
        }
        $this->logger->info("No tiene importe: " . $this->codigo);
        return 0;

    }

    public function getVto()
    {
        foreach ($this->vtos as $vto) {
            if ($this->fechaCalculo <= $vto[0]) {
                return $vto[0]->format('d-m-Y');
            }
        }
        $this->logger->info("No tiene vencimiento: " . $this->codigo);
        return '';
    }

    public function getVtos()
    {
        return $this->vtos;
    }


    /**
     * Indentificar cual es el codigo de barra que utiilza
     * @return bool
     *
     * Si identifica el codigo de barra, lo carga en detalle
     */
    private function identificarCodigo($soloIndentificar = false)
    {
        //Recorrer los codigo hasta encontrar el que corresponda con la longitud y el identidicador
        // primero recuperar los codigos de la longitud

        $regs = $this->em->getRepository("SistemaCajaBundle:CodigoBarra")->findBy(array("longitud" => strlen($this->codigo)));

        foreach ($regs as $reg) {
            if ($this->obtenerIdentificador($reg->getIdentificador()) == $reg->getValor()) {
                $this->cbReg = $reg;

                if ($soloIndentificar == false) {
                    $this->conReferencia = $this->cbReg->getConReferencia();
                    $this->detalle = $this->def2Array($reg);
                }

                return true;
            }
        }
        return false;

    }

    /**
     * obtenerIdentificador
     *
     * Obtener el identificador del codigo de barra
     *
     * @param $id
     * @return string
     */
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
            if ($pos->getComp()) {
                $this->comprobante = substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud());
            }
            if ($pos->getSeccion()) {
                $this->seccion = substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud());
            }

            $ls_desc_tab = substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud());
            if ($pos->getTabla() > 0) {
                $t = $this->tabla_man->getParametro($pos->getTabla(), substr($this->codigo, $pos->getPosicion() - 1, $pos->getLongitud()));
                if ($t && $t->getTabla() > 1) {
                    $ls_desc_tab = $t->getDescripcion();
                }
            }

            if ($pos->getVer()) {
                $this->detalleVisible[] = array(
                    $pos->getPosicion(),
                    $pos->getDescripcion(),
                    $ls_desc_tab,
                );
            }
            $aDet[] = array(
                $pos->getPosicion(),
                $pos->getDescripcion(),
                $ls_desc_tab,
            );

        }

        return $aDet;
    }


    private function getVtosImportes()
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

    private function evalExp($exp)
    {

        //eval("\$ret=".$exp.";");

        $fn = create_function("", "return ({$exp});");
        $ret = $fn();
        return $ret;
    }

    public function getReferencia(){
        if ($this->getConReferencia() == 1) { //solo se aplica para el código de barra del sistema nuevo
            $sql = "select REFERENCIA from view_boleta_referencia where comprobante = " . $this->getComprobante();
            $connection = $this->em->getConnection();
            $statement = $connection->prepare($sql);
            $statement->execute();
            $referencias = $statement->fetchAll();
            $cantidad = count($referencias);
            $referencia = "";
            if ($cantidad > 0) {
                foreach ($referencias as $ref) {
                    $referencia .= $ref['REFERENCIA'] . ' * '; //FALTA EL FOR EACH ....
                }
            } else {
                $referencia = "";
            }
        } else {
            $referencia = "";
        }

        return $referencia;
    }


}



