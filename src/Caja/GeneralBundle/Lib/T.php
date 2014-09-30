<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fito
 * Date: 09/10/13
 * Time: 18:55
 * To change this template use File | Settings | File Templates.
 */

namespace Caja\GeneralBundle\Lib;

//use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\Common\Persistence\ObjectManager;

class T {
    static $em;
    private static $coefsRecargo = array('2014-02-03' => 2, '2025-01-01' => 3);

    /**
     * @name                setEM
     * @author fito
     * @version 07/11/2013
     * @param ObjectManager $enman
     * @throws \Exception
     *
     * Establece el entity manager global.
     */
    static function setEM(ObjectManager $enman) {
        if (is_null($enman)) {
            throw new \Exception(M::ERROR_EM_NULO);
        }
        if (!($enman instanceof ObjectManager)) {
            throw new \Exception(M::ERROR_PARAMETRO_UNKNOW);
        }
        self::$em = $enman;
    }

    /**
     * @name getEM
     * @author fito
     * @version 07/11/2013
     * @return ObjectManager
     * @throws \Exception
     *
     * Retorna el entity manager global.
     */
    static function getEM() {
        if (is_null(self::$em)) {
            throw new \Exception(M::ERROR_EM_NULO);
        }

        return self::$em;
    }

    /**
     * @name  nullCoalescingOp
     * @author  fito
     * @version 09/10/203
     * @param $variable , la variable a controlar
     * @param $opcion , el valor de retorno si la variable es nula
     * @return any, la variable o el valor de opcion si la misma es nula
     *
     * Implementa el null-coalescing operator.
     */
    static function nullCoalescingOp($variable, $opcion) {
        return (is_null($variable) ? $opcion : $variable);
    }

    /**
     * @name  zeroCoalescingOp
     * @author  fito
     * @version 09/10/203
     * @param $variable , la variable a controlar
     * @param $opcion , el valor de retorno si la variable es cero
     * @return any, la variable o el valor de opcion si el mismo es cero
     *
     * Implementa el zero-coalescing operator.
     */
    static function zeroCoalescingOp($variable, $opcion) {
        return ($variable == 0 ? $opcion : $variable);
    }

    /**
     * @name          redondear
     * @author  fito
     * @version 09/10/203
     * @param         $valor , el valor a redondear
     * @param integer $digitos , la cantidad de digitos a la cual redondear
     * @throws \Exception
     * @internal param $opcion , el valor de retorno si la variable es cero
     * @return float, la variable o el valor de opcion si el mismo es cero
     *
     * Implementa el redondeo de un valor.
     */
    static function redondear($valor, $digitos) {
        if (is_null($digitos) || $digitos < 1 || $digitos > 8) {
            throw new \Exception(M::ERROR_PARAMETRO_OVERFLOW);
        }

        return round($valor, $digitos);
    }

    /**
     * @name coefRecargo
     * @author fito
     * @version 08/04/2014
     * @param $fecha , la fecha de la cual tomar el recargo vigente
     * @return number
     * @throws \Exception
     *
     * Devuelve el coeficiente de recargo estándar, como un porcentaje,
     * vigente a la fecha pasada como argumento (o hoy si no viene nada).
     */
    static function coefRecargo($fecha = null) {
        $fecha = T::nullCoalescingOp($fecha, F::getFechaActual());

        foreach (self::$coefsRecargo as $fec => $coef) {
            $coeficiente = $coef;
            if ($fecha < $fec) {
                return $coeficiente;
            }
        }

        throw new \Exception(M::ERROR_COEF_RECARGO);
    }

    /**
     * @name getArrayCoefRecargo
     * @author fito
     * @version 08/04/2014
     * @return array
     *
     * Devuelve el aray de coeficientes de recargo
     * (asociativo con fechas de corte como claves).
     */
    static function getArrayCoefRecargo() {
        return self::$coefsRecargo;
    }
}