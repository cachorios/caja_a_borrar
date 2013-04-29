<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Caja\SistemaCajaBundle\Entity\LoteDetalle;
/**
 * LoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LoteRepository extends EntityRepository
{
    /**
     * A partir de un numero de comprobante, se recuperan los comprobantes
     *pertenecientes al lote correspondiente, que no esten anulados
     * @param $codigo_barra
     * @return mixed
     */
    public function getLote($apertura_id, $codigo_barra)
    {
        $em = $this->getEntityManager();
        $q = $em->createQuery("
              SELECT ld
              FROM
                  SistemaCajaBundle:LoteDetalle ld JOIN ld.lote l
              WHERE
                  ld.codigo_barra = :codigo_barra
                  and l.apertura = :apertura_id
                  and ld.anulado = 0
              ");
        $q->setParameter('codigo_barra', $codigo_barra);
        $q->setParameter('apertura_id', $apertura_id);

        try {
            $res = $q->getSingleResult();
            return $res->getLote();
        } catch (\Doctrine\Orm\NoResultException $e) {
            //No trajo resultados:
            return null;
        }  catch (\Doctrine\Orm\NonUniqueResultException $e) {
            //Si devolvio una excepcion es porque trajo mas de un resultado,
            // o sea es un error grave haber cobrado mas de una vez un comprobante
            return null;
        }
    }

    /**
     * Consulta de cantidad de pagos de un lote determinado
     * @param $lote_id
     * @return int
     */
    public function getConsultaCantidadPagos($lote_id){
        $em = $this->getEntityManager();

        $consulta_cantidad_pagos = $em->createQuery("
            SELECT COUNT(p.tipo_pago)
            FROM SistemaCajaBundle:LotePago p
            WHERE p.lote = :lote_id ")
            ->setParameter("lote_id", $lote_id);
        $cantidad_pagos = $consulta_cantidad_pagos->getSingleResult();
        $cantidad_pagos = $cantidad_pagos[1];

        return $cantidad_pagos;
    }

    /**
     * Consulta el tipo de pago cuando el lote se pago en un solo pago
     * @param $lote_id
     * @return int
     */
    public function getConsultaTipoPago($lote_id){
        $em = $this->getEntityManager();

        $consulta_tipo_pago= $em->createQuery("
                SELECT tp.id
                FROM SistemaCajaBundle:LotePago p JOIN p.tipo_pago tp
                WHERE p.lote = :lote_id ")
            ->setParameter("lote_id", $lote_id);

        $consulta_tipo_pago->setMaxResults(1);
        $resultado = $consulta_tipo_pago->getSingleResult();
        $tipo_pago = $resultado['id'];

        return $tipo_pago;
    }

    /**
     * Consulta el monto pagado en efectivo de un lote
     * @param $lote_id
     * @return $float
     */
    public function getMontoEfectivo($lote_id){
        $em = $this->getEntityManager();

        $consulta_efectivo = $em->createQuery("SELECT sum(p.importe)
                FROM SistemaCajaBundle:LotePago p JOIN p.tipo_pago tp
                WHERE p.tipo_pago = 1 and p.lote = :lote_id ")
            ->setParameter("lote_id", $lote_id);
        $consulta_efectivo->setMaxResults(1);
        $efectivo = $consulta_efectivo->getSingleResult();
        $efectivo = $efectivo[1];

        return $efectivo;
    }

    /**
     * Método usado para verificar la existencia de un comprobante
     * @param $cb
     * @return
     */
    public function getExisteComprobante($cb){
        //Codigo de barra recibido
        $cb = trim($cb);

        //Servicio de codigo de barra, para interpretarlo
        $bm = $this->container->get("caja.barra");

        $bm->setCodigo($cb, $apertura->getFecha());
        $imp = $bm->getImporte();
        //Se verifica si existe en la base:

        $em    = $this->getDoctrine()->getManager();
        $lotes = $em->getRepository('SistemaCajaBundle:Lote')->getLote($apertura->getId(), $cb);

        return $lotes;
    }
}