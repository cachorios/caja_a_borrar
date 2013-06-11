<?php
/**
 * Created by JetBrains PhpStorm.
 * Usuario: cacho
 * Date: 29/10/12
 * Time: 22:25
 * To change this template use File | Settings | File Templates.
 */

namespace Lar\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Doctrine\Common\Collections\ArrayCollection;
// @ORM\MappedSuperclass
/**
 * Lar\UsuarioBundle\Entity\Grupo
 *
 * @ORM\Table(name="grupo")
 * @ORM\Entity
 */
class Grupo implements RoleInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=30, unique=true)
     */
    protected $nombre;

    /**
     * @var string $role
     *
     * @ORM\Column(name="role", type="string", length=30, unique=true)
     *
     */
    protected $role;

    /**
     * @var ArrayCollection $usuarios
     * @ORM\ManyToMany(targetEntity="Usuario", cascade={"persist"} , mappedBy="grupos" )
     */

    protected $usuarios;


    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
    }

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
     * @return Grupo
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
     * Set role
     *
     * @param string $role
     * @return Grupo
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add a Usuario into the collection
     *
     * @param Usuario $usuario
     * @return Grupo
     */
    public function addUsuario(Usuario $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }

    /**
     * Get a collection of Usuario
     *
     * @return ArrayCollection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    public function setUsuario(ArrayCollection $usuarios)
    {
        $this->usuarios = $usuarios;
    }

    public function __toString(){
        return $this->getNombre();
    }

    /**
     * Remove usuarios
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\Lar\UsuarioBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }
}