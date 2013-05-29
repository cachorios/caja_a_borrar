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
    public function getAperturas($usuario){
        $em = $this->getEntityManager();

        $a = $this->createQueryBuilder("a")
            ->join("a.caja","c")
            ->where('c.cajero = :cajero')
            ->setParameter("cajero", $usuario->getId())
            ->orderBy("a.fecha",'asc');

        return $a;
    }

    public function getPagosByTipoPago($apertura_id)
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery("
              SELECT
                  t.id,t.descripcion, sum(p.importe)
              FROM
                  SistemaCajaBundle:LotePago p JOIN p.tipo_pago t JOIN p.lote l
              WHERE
                  l.apertura = :apertura_id
              GROUP BY
                  t.id, t.descripcion
              ORDER BY
                  t.id ")
            ->setParameter("apertura_id", $apertura_id)
        ;

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
            ->setParameter("apertura_id", $apertura_id)
        ;

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
            ->setParameter("apertura_id", $apertura_id)
        ;

        $res = $q->getSingleResult();

        if ($res[1] < 0) {
            return - $res[1];
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
        $nombre_archivo = "EP";
        //Despues va la fecha:
        $nombre_archivo .= $apertura->getFecha()->format('d');//dia
        $nombre_archivo .= $apertura->getFecha()->format('m');//mes
        $nombre_archivo .= $apertura->getFecha()->format('y');//año
        $nombre_archivo .= '_' . $numero_caja;//numero de caja
        $nombre_archivo .= $apertura_id;//id de caja

        $fp = fopen($path_archivos.$nombre_archivo.".txt", "w+");
        if ($fp) {//fopen devuelve un recurso de puntero a fichero si tiene éxito, o FALSE si se produjo un error.
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
                        //62	65	    4	        Numero de Caja Rellenados con ceros a la izquierda
                        $datos = $detalle->getCodigoBarra();
                        $decimales = explode(".",$detalle->getImporte());
                        $datos .= str_pad($decimales[0] , 6, "0", STR_PAD_LEFT); //parte entera, rellenada con ceros -> 6 posiciones
                        if (count($decimales) > 1) {
                            $datos .= $decimales[1]  ; //parte decimal, 2 digitos
                        } else {
                            $datos .= '00';//El importe original era redondo, no tenia decimales
                        }
                        $datos .= $detalle->getFecha()->format('Ymd'); //fecha de pago
                        $datos .= 1; //Código Fijo de empresa. Uso interno. Usar siempre un Valor Fijo = 1 (Uno)
                        $datos .= $apertura->getCaja()->getNumero(). "\n"; //Numero de Caja Rellenados con ceros a la izquierda
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

}
