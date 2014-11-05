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

}