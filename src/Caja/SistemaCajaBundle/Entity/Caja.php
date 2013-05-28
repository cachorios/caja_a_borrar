<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Lar\UsuarioBundle\Entity\Usuario;

/**
 * Caja
 *
 * @ORM\Table(name="sca_caja")
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\CajaRepository")
 *
 * @DoctrineAssert\UniqueEntity("numero")
 * @DoctrineAssert\UniqueEntity("nombre")
 * @DoctrineAssert\UniqueEntity("cajero")
 */
class Caja
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var integer
	 * @ORM\Column(name="numero", type="integer")
	 * @Assert\Length(min = 1, max = 2)
	 */
	private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=64)
     */
    private $nombre;

    /**
     * @ORM\OneToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="cajero_id", referencedColumnName="id")
    **/
    protected  $cajero;

    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="string", length=39)
     * @Assert\Length(min = 5, max = 39)
     */
    protected $ubicacion;

    /**
    * @var string
    *
    * @ORM\Column(name="puerto", type="string", length=10)

    */
    private $puerto;

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
     * @return Caja
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
     * Set cajero
     *
     * @param \Lar\UsuarioBundle\Entity\Grupo $cajero
     * @return Caja
     */
    public function setCajero(\Lar\UsuarioBundle\Entity\Usuario $cajero = null)
    {
        $this->cajero = $cajero;
    
        return $this;
    }

    /**
     * Get cajero
     *
     * @return \Lar\UsuarioBundle\Entity\Usuario 
     */
    public function getCajero()
    {
        return $this->cajero;
    }

    public function __toString(){
        return $this->getNombre();
    }


    /**
     * Set numero
     *
     * @param integer $numero
     * @return Caja
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set puerto
     *
     * @param string $puerto
     * @return Caja
     */
    public function setPuerto($puerto)
    {
        $this->puerto = $puerto;
    
        return $this;
    }

    /**
     * Get puerto
     *
     * @return string 
     */
    public function getPuerto()
    {
        return $this->puerto;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return Caja
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    
        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }
}
