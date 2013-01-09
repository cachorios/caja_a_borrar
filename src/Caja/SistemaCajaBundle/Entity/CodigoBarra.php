<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodigoBarra
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\CodigoBarraRepository")
 */
class CodigoBarra
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="nombre", type="string", length=64)
     */
    private $nombre;


    /**
     * @var string
     * @ORM\Column(name="empresa", type="string", length=64)
     */
    private $empresa;

    /**
     * @var string
     * @ORM\Column(name="longitud", type="integer" )
     */
    private $longitud;

    /**
     * @var string
     * @ORM\Column(name="identificador", type="string", length=64)
     */
    private $identificador;


    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=64)
     */
    private $valor;

    /**
     * @var text
     * @ORM\Column(name="observacion", type="text", nullable=true )
     */
    private $observacion;


    /**
     * @ORM\OneToMany(targetEntity="BarraDetalle", mappedBy="codigobarra", cascade={"persist"}  )
     */
    protected $posiciones;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return CodigoBarra
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set empresa
     *
     * @param string $empresa
     * @return CodigoBarra
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    
        return $this;
    }

    /**
     * Get empresa
     *
     * @return string 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return CodigoBarra
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
    
        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set identificador
     *
     * @param string $identificador
     * @return CodigoBarra
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
    
        return $this;
    }

    /**
     * Get identificador
     *
     * @return string 
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return CodigoBarra
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return CodigoBarra
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
    /**
     * Constructor
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posiciones = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add posiciones
     *
     * @param \Caja\SistemaCajaBundle\Entity\BarraDetalle $posicion
     * @return CodigoBarra
     */
    public function addPosicione(\Caja\SistemaCajaBundle\Entity\BarraDetalle $posicion)
    {
        ld("addPosiciones",$posicion);
        if (!$this->posiciones->contains($posicion)) {
            ld("addPosiciones2",$posicion);
            $posicion->setCodigobarra($this);
            $this->posiciones->add($posicion);
        }

        //$this->posiciones[] = $posicion;
        exit;
        return $this;
    }

    /**
     * Remove posiciones
     *
     * @param \Caja\SistemaCajaBundle\Entity\BarraDetalle $posicion
     */
    public function removePosicione(\Caja\SistemaCajaBundle\Entity\BarraDetalle $posicion)
    {
        $this->posiciones->removeElement($posicion);
    }

    /**
     * Get posiciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosiciones()
    {
        return $this->posiciones;
    }

    public function setPosiciones(ArrayCollection $posiciones)
    {
        ld("setPosiciones",$posiciones);
        foreach ($posiciones as $posision) {
            $this->addPosicione($posision);
        }

        exit;
        //$this->posiciones = $posiciones;
    }

}