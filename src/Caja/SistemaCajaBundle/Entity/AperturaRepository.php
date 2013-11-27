<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AperturaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AperturaRepository extends EntityRepository
{
    public function getAperturas($usuario)
    {
        $em = $this->getEntityManager();

        $a = $this->createQueryBuilder("a")
            ->join("a.caja", "c")
            ->where('c.cajero = :cajero')
            ->setParameter("cajero", $usuario->getId())
            ->orderBy("a.fecha", 'desc');

        return $a;
    }

    public function getPagosByTipoPago($apertura_id)
    {
        $em = $this->getEntityManager();
        $q = $em->createQuery("SELECT  t.id, t.descripcion,
                                (select sum(pp.importe) FROM SistemaCajaBundle:LotePago pp JOIN pp.lote ll
                                WHERE
                                  ll.apertura = l.apertura
                                  and pp.tipo_pago = t.id
                                   and pp.importe > 0
                                ) as importe,
                                (select sum(ppp.importe) FROM SistemaCajaBundle:LotePago ppp JOIN ppp.lote lll
                                WHERE
                                  lll.apertura = l.apertura
                                  and ppp.tipo_pago = t.id
                                  and ppp.importe < 0
                                ) as anulado
              FROM
                  SistemaCajaBundle:LotePago p JOIN p.tipo_pago t JOIN p.lote l
              WHERE
                  l.apertura = :apertura_id
              GROUP BY
                  t.id, t.descripcion, l.apertura
              ORDER BY
                  t.id")
            ->setParameter("apertura_id", $apertura_id);

        $res = $q->getResult();

        return $res;

    }

    /**
     * Obtiene el importe total de pagos de la apertura
     * @param $apertura_id
     * @return float
     */
    public function getImportePagos($apertura_id)
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery("
              SELECT sum(p.importe)
              FROM
                  SistemaCajaBundle:LotePago p JOIN p.lote l
              WHERE
                  l.apertura = :apertura_id
              ")
            ->setParameter("apertura_id", $apertura_id);

        $res = $q->getSingleResult();

        if ($res[1] > 0) {
            return $res[1];
        } else {
            return 0;
        }

    }

    /**
     * Obtiene el importe total de pagos anulado de la apertura
     * @param $apertura_id
     * @return float
     */
    public function getImportePagosAnulado($apertura_id)
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery("
              SELECT sum(p.importe)
              FROM
                  SistemaCajaBundle:LotePago p JOIN p.lote l
              WHERE
                  l.apertura = :apertura_id
                  AND p.importe < 0
              ")
            ->setParameter("apertura_id", $apertura_id);

        $res = $q->getSingleResult();

        if ($res[1] < 0) {
            return -$res[1];
        } else {
            return 0;
        }

    }


    /**
     * Genera el archivo de texto que se envia por mail al cerrar una caja
     *
     * @return string
     */
    public function generaArchivoTexto($apertura_id, $numero_caja, $path_archivos)
    {

        $em = $this->getEntityManager();
        $q = $em->createQuery("
        	SELECT a
              FROM SistemaCajaBundle:Apertura a
             WHERE a.id = :id")
            ->setParameter("id", $apertura_id);
        $res = $q->getResult();
        $apertura = $res[0];

        //Genero el nombre del archivo:
        //El nombre de archivo siempre empieza con EP
        $nombre_archivo = "MU";
        //Despues va la fecha:
        $nombre_archivo .= $apertura->getFecha()->format('d'); //dia
        $nombre_archivo .= $apertura->getFecha()->format('m'); //mes
        $nombre_archivo .= $apertura->getFecha()->format('y'); //año
        $nombre_archivo .= '_' . $numero_caja; //numero de caja
        $nombre_archivo .= '_' . $apertura_id; //id de caja

        $fp = fopen($path_archivos . $nombre_archivo . ".txt", "w+");
        if ($fp) { //fopen devuelve un recurso de puntero a fichero si tiene éxito, o FALSE si se produjo un error.
            //Recorro los comprobantes cobrados en esa caja, no anulados:
            foreach ($apertura->getLotes() as $lote) {
                foreach ($lote->getDetalle() as $detalle) {
                    if (!$detalle->getAnulado()) {
                        //Desde	Hasta	Longitud	Descripción
                        //1	    44	    44	        Código de Barras de la Municipalidad Utilizado para la Cobranza.  Sin modificaciones
                        //45	50	    6	        Valor Entero del  Importe Cobrado
                        //51	52	    2	        Valor Decimales del Importe Cobrado
                        //53	60	    8	        Fecha de Pago – Formato AAAAMMDD
                        //61	61	    1	        Código Fijo de empresa. Uso interno de la Municipalidad de Posadas. Usar siempre un Valor Fijo = 1 (Uno)
                        //62	63	    2	        Numero de Caja Rellenados con ceros a la izquierda
                        //64	65	    2	        Numero de Sucursal Rellenados con ceros a la izquierda = 00
                        $datos = $detalle->getCodigoBarra();
                        $decimales = explode(".", $detalle->getImporte());
                        $datos .= sprintf('%08d', $detalle->getImporte() * 100); //elimino decimales

                        /*
                        $datos .= str_pad($decimales[0] , 6, "0", STR_PAD_LEFT); //parte entera, rellenada con ceros -> 6 posiciones
                        if (count($decimales) > 1) {
                            $datos .= $decimales[1]  ; //parte decimal, 2 digitos
                        } else {
                            $datos .= '00';//El importe original era redondo, no tenia decimales
                        }
                        */
                        $datos .= $detalle->getFecha()->format('Ymd'); //fecha de pago
                        $datos .= 1; //Código Fijo de empresa. Uso interno. Usar siempre un Valor Fijo = 1 (Uno)
                        $datos .= sprintf("%02d", $apertura->getCaja()->getNumero()); //Numero de Caja Rellenados con ceros a la izquierda
                        $datos .= "00" . "\n"; //Numero de sucursal Rellenados con ceros a la izquierda, no se esta usando
                        $write = fputs($fp, $datos);
                    }
                }
            }
            fclose($fp);
        } else {
            //Hubo un error al abrir/escribir el archivo
            return false;
        }

        return $nombre_archivo;
    }


    /**
     * Obtiene el detalle de comprobantes validos registrados en la apertura
     * @param $apertura_id
     * @return array que contiene cada comprobante registrado
     */
    public function getDetallePagos($apertura_id)
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery("
              SELECT ld
              FROM
                  SistemaCajaBundle:LoteDetalle ld JOIN ld.lote l
              WHERE
                  l.apertura = :apertura_id
                  AND ld.importe > 0
              ORDER BY ld.seccion, ld.comprobante
              ")
            ->setParameter("apertura_id", $apertura_id);

        $res = $q->getResult();

        return $res;

    }

    /**
     * Obtiene el detalle de cada tipo de pago registrado en la apertura
     * @param $apertura_id
     * @return array que contiene el agrupado de cada tipo de pago
     */
    public function getDetalleTipoPagos($apertura_id)
    {
        $em = $this->getEntityManager();

        $PagoTipoPago = $em->getRepository('SistemaCajaBundle:Apertura')->getPagosByTipoPago($apertura_id);

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

    /**
     * Obtiene el detalle de TODOS LOS comprobantes registrados en la apertura
     * @param $apertura_id
     * @return array que contiene cada comprobante registrado
     */
    public function getDetalleTodosPagosSeccion($apertura_id, $tipo_seccion)
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery("
              SELECT ld.id, ld.importe, ld.anulado, ld.comprobante, ld.referencia, ld.codigo_barra, ld.seccion
              FROM
                  SistemaCajaBundle:LoteDetalle ld JOIN ld.lote l,
                  LarParametroBundle:LarParametro lp
              WHERE lp.tabla = 10
                  AND lp.codigo = ld.seccion
                  AND l.apertura = :apertura_id
                  AND ld.seccion = :tipo_seccion
              ORDER BY ld.seccion, ld.comprobante
              ")
            ->setParameter("apertura_id", $apertura_id)
            ->setParameter("tipo_seccion", $tipo_seccion);

        $res = $q->getResult();
        return $res;

    }

    /**
     * Obtiene la sumatoria de los pagos, por tipo de seccion
     * @param $apertura_id
     * @return array que contiene cada comprobante registrado
     */
    public function getPagosByTipoSeccion($apertura_id)
    {
        $em = $this->getEntityManager();
        $q = $em->createQuery("SELECT lp.id, lp.descripcion,
                                (select sum(pp.importe)
                                 FROM SistemaCajaBundle:LotePago pp
                                 JOIN pp.lote ll
                                 JOIN ll.detalle ldd,
                                 LarParametroBundle:LarParametro lpp
                                 WHERE lpp.tabla = 10
                                 AND lpp.codigo = ldd.seccion
                                 AND ldd.seccion = ld.seccion
                                 AND ll.apertura = :apertura_id
                                 AND ll.apertura = l.apertura
                                 AND ldd.anulado = 0
                                 AND pp.importe > 0
                                ) as importe,
                                (select sum(ppp.importe)
                                 FROM SistemaCajaBundle:LotePago ppp
                                 JOIN ppp.lote lll
                                 JOIN lll.detalle lddd,
                                 LarParametroBundle:LarParametro lppp
                                 WHERE lppp.tabla = 10
                                 AND lppp.codigo = lddd.seccion
                                 AND lddd.seccion = ld.seccion
                                 AND lll.apertura = :apertura_id
                                 AND lll.apertura = l.apertura
                                 AND lddd.anulado = 1
                                 AND ppp.importe < 0
                                )  as anulado
              FROM
                  SistemaCajaBundle:LotePago p
                  JOIN p.tipo_pago t
                  JOIN p.lote l
                  JOIN l.detalle ld,
                  LarParametroBundle:LarParametro lp
              WHERE lp.tabla = 10
                  AND lp.codigo = ld.seccion
                  AND l.apertura = :apertura_id
              GROUP BY
                  lp.id, lp.descripcion, l.apertura, ld.seccion
              ORDER BY lp.descripcion")
            ->setParameter("apertura_id", $apertura_id);

        $res = $q->getResult();

        return $res;

    }

}
