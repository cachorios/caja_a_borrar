<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diego
 * Date: 31/10/13
 * Time: 17:06
 * To change this template use File | Settings | File Templates.
 */
namespace Caja\GeneralBundle\Lib;

use Symfony\Component\DependencyInjection;
use Symfony\Component\Yaml\Parser;

class MotorImpresion {
    private $formato;
    private $plantillaReporte;
    private $formatoArchivo;
    private $separadorColumna;
    private $conf;
    private $csvFile;
    private $archivoOrigenDatos;
    private $imprimible;
    private $container;
    private $parametroSalida;

    /**
     * Contructor
     */
    public function __construct($container, $imprimible) {
        $this->imprimible = $imprimible;

        //Symfony/Component/DependencyInjection;
        $this->container = $container;
    }

    /**
     *
     */
    public function imprimirReporte($imprimible) {
        $this->prepararImpresion($imprimible);

        $this->prepararOrigenDatos($imprimible);

        $this->ejecutarImpresion($imprimible);
    }

    /**
     * Funcion encargada de obtener la configuracion de los reportes
     */
    public function loadConfig() {
        $config_file   = $this->container->get('kernel')->getRootDir() . "/config/reportes.yml";
        $yaml          = new Parser();
        $report_config = $yaml->parse(file_get_contents($config_file));

        if (isset ($report_config ['reportes'])) {
            $this->conf = $report_config ['reportes'];
        }
        return $this->conf;
    }

    public function obtenerParametrosReporte() {
        return $this->parametroSalida;
    }

    /**
     * Setea los ejecutar la impresion del reporte
     */
    private function prepararImpresion($imprimible) {
        $this->loadConfig();

        $this->setNombrePlantilla($imprimible->getNombrePlantilla());

        $this->setFormatoReporte(0);

        $this->setSeparadorColumna("|");
    }

    private function prepararOrigenDatos($imprimible) {

        $datos = $imprimible->generarTxt();

        $this->generaArchivoOrigenDatos($datos);
    }

    /**
     * Funcion que setea el nombre del reporte
     */
    private function setNombrePlantilla($plantilla) {
        $this->plantillaReporte = $plantilla;
    }

    /**
     * Funcion que devuelve el nombre del reporte
     */
    private function getNombrePlantilla() {
        return $this->plantillaReporte;
    }

    /**
     * Funcion que setea el formato del reporte
     * Formatos Posibles: PDF-XSL-XML-HTML-CSV-RTF-ODF
     *
     */
    private function setFormatoReporte($format) {
        switch ($format) {
            case "PDF":
                //echo "esta activada la opcion 1";
                $this->formato = 0;
                break;
            case "XLS":
                //echo "esta activada la opcion 2";
                $this->formato = 1;
                break;
        }
    }

    /**
     * Funcion que devuelve el nombre del reporte
     */
    private function getFormatoReporte() {
        return $this->formato;
    }

    /**
     * Funcion que setea el separador de campos del reporte
     * Por defecto "|"
     */
    private function setSeparadorColumna($separadorColumna) {
        $this->separadorColumna = $separadorColumna;
    }

    /**
     * Funcion que devuelve el nombre del reporte
     */
    private function getSeparadorColumna() {
        return $this->separadorColumna;
    }

    /**
     * Genera un nombre aleatorio, para el nombre del archivo (origen de datos)
     *
     * @param $num Longitud del nombre generados
     */
    private function genera_nombre($num = 10) {

        $voc = array(
            "a",
            "e",
            "i",
            "o",
            "u");
        $con = array(
            "b",
            "c",
            "d",
            "f",
            "g",
            "h",
            "j",
            "k",
            "l",
            "m",
            "n",
            "p",
            "q",
            "r",
            "s",
            "t",
            "w",
            "x",
            "y",
            "z");
        $psw = ""; // cadena que contendra el password.
        $vc  = mt_rand(0, 1); // definde si empieza por vocal o consonante.
        for ($n = 0; $n < $num; $n++) {
            if ($vc == 1) {
                $vc = 0;
                $psw .= $con[mt_rand(0, count($con) - 1)];
            }
            $psw .= $voc[mt_rand(0, count($voc) - 1)];
            $psw .= $con[mt_rand(0, count($con) - 1)];
        }
        $psw = ereg_replace("q", "qu", $psw);
        $psw = ereg_replace("quu", "que", $psw);
        $psw = ereg_replace("yi", "ya", $psw);
        $psw = ereg_replace("iy", "ay", $psw);
        $psw = substr($psw, 0, $num);
        return $psw;
    }

    /**
     *
     */
    private function generaArchivoOrigenDatos($datos) {
        $nombreArchivo = $this->genera_nombre();
        $fp            = fopen($this->conf['paths']['file_path'] . $nombreArchivo . ".txt", "w+");
        $write         = fputs($fp, $datos);
        fclose($fp);

        $nombreArchivo .= ".txt";
        $this->formatoArchivo     = "csv";
        $this->archivoOrigenDatos = $nombreArchivo;

        return $nombreArchivo;
    }

    /**
     * Funcion encargada de ejecutar la compilacion del reporte
     */
    private function ejecutarImpresion() {

        $reporte       = $this->conf['paths']['jxrml_path'] . "/" . $this->getNombrePlantilla();
        $SUBREPORT_DIR = $this->conf['paths']['jxrml_path'] . "/";
        $archivo       = $this->conf['paths']['file_path'] . $this->archivoOrigenDatos;
        $rutaImagen    = $this->conf['paths']['image_path'];
        $aplicacionweb = $this->conf['paths']['aplicacion_path'];

        //remplazo las "/" por "@" para poder enviar ese parametro en la url y no usar urlEncode, etc.
        $reporte    = str_replace("/", "@", $reporte);
        $archivo    = str_replace("/", "@", $archivo);
        $rutaImagen = str_replace("/", "@", $rutaImagen);
        $url        =
            $aplicacionweb
            . "?nombreReporte="
            . $reporte
            . "&archivo="
            . $archivo
            . "&s_SUBREPORT_DIR="
            . $SUBREPORT_DIR
            . "&rutaImagen="
            . $rutaImagen
            . "&p_format="
            . $this->getFormatoReporte()
            . "&separadorColumna=|";

        $this->parametroSalida = array(
            'nombreReporte'    => $reporte,
            'archivo'          => $archivo,
            's_SUBREPORT_DIR'  => $SUBREPORT_DIR,
            'rutaImagen'       => $rutaImagen,
            'p_format'         => $this->getFormatoReporte(),
            'separadorColumna' => '|');
        //header("Location:" . $url);
    }
}
