<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 27/06/13
 * Time: 19:17
 * To change this template use File | Settings | File Templates.
 */

namespace Caja\SistemaCajaBundle\Lib;
use Doctrine\ORM\EntityManager;
use Caja\SistemaCajaBundle\Entity\Prorroga;


class ProrrogaService {
    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    /**
     * getVencimiento
     * Retorna un vencimiento valido, siempre que este vencido, y cumpla las reglas de prorroga.
     * @param \DateTime $fecha
     * @param null $hoy
     * @return \DateTime
     */
    public function getVencimiento(\DateTime $fecha, $hoy = null){
        if(null == $hoy ){
            $hoy = new \DateTime('now');
            $hoy->setTime(0,0,0);
        }
        $fecha->setTime(0,0,0);

        if($fecha < $hoy){
            $oldFecha = clone($fecha);
            $fecha = $this->nextHabil($fecha);

            /*
             * Recorrer hasta que oldfecha y fecha sean iguales,
             * o sea que ya es un vencimiento ya es valido.
            */
            while(  $oldFecha != $fecha ){
                $oldFecha = clone($fecha);
                $fecha = $this->nextHabil($fecha) ;
            }
        }
       return  $fecha;
    }

    /**
     * nextHabil
     * obtiene un vencimiento siguiente
     * sabado o domingo retorna un lunes, o si esta en tabla de prorroga, suma un dia
     * @param \DateTime $fecha
     * @return \DateTime
     */
    private function nextHabil(\DateTime $fecha){
        if($fecha->format('w') == 0 || $fecha->format('w') == 6 ){
            $fecha->modify("next monday");
        }elseif($this->esFeriado($fecha)){
            $fecha->modify("+1 day");
        }
        return $fecha;
    }

    /**
     * esFeriado
     * Determina si una fecha es feriado o no.
     * para esto usa una entidad Feriado.
     *
     * @param \DateTime $fecha
     */
    private function esFeriado(\DateTime $fecha){
        //Para provar sin entidad
//        if($fecha->format('Y-m-d') == '2013-06-20' || $fecha->format('Y-m-d') == '2013-06-21' || $fecha->format('Y-m-d') == '2013-06-24' ){
//            return true;
//        }
//
        if( $this->em->getRepository("SistemaCajaBundle:Feriado")->findOneByFecha($fecha)){
            return true;
        }
        return false;

    }
}

//$ob = new ProrrogaService();
//$fecha = new \DateTime('2013-06-20');
//print_r($fecha);
//echo $ob->getVencimiento($fecha)->format('d/m/Y');