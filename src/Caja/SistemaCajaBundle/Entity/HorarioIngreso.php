<?php
namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * HorarioIngreso
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class HorarioIngreso
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
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=32)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="mascara", type="string", length=32)
     */
    private $mascara;


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
     * Set descripcion
     *
     * @param string $descripcion
     * @return HorarioIngreso
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set mascara
     *
     * @param string $mascara
     * @return HorarioIngreso
     */
    public function setMascara($mascara)
    {
        $this->mascara = $mascara;
    
        return $this;
    }

    /**
     * Get mascara
     *
     * @return string 
     */
    public function getMascara()
    {
        return $this->mascara;
    }
}