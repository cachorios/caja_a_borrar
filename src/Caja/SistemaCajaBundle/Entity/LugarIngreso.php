<?php
namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * LugarIngreso
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class LugarIngreso
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
     * @ORM\Column(name="descripcion", type="string", length=256)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="desde", type="string", length=8)
     */
    private $desde;

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
     * @return LugarIngreso
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
     * Set desde
     *
     * @param string $desde
     * @return LugarIngreso
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;
    
        return $this;
    }

    /**
     * Get desde
     *
     * @return string 
     */
    public function getDesde()
    {
        return $this->desde;
    }
}