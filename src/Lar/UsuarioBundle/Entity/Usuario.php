<?php

namespace Lar\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;


//use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
//use Symfony\Component\Validator\ExecutionContext;


/**
 * Lar\UsuarioBundle\Entity\Usuario
 *
 * @ORM\Table
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Lar\UsuarioBundle\Entity\UsuarioRepository")
 *
 */
class Usuario implements AdvancedUserInterface

{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=32)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string $apellido
     *
     * @ORM\Column(name="apellido", type="string", length=32)
     */
    private $apellido;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=96, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\Length(min = 6)
     */
    private $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="text")
     */
    private $direccion;

    /**
     * @var boolean $permite_email
     *
     * @ORM\Column(name="permite_email", type="boolean")
     */
    private $permite_email;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @var boolean $isDeleted
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted = false;
    /**
     * @var \DateTime $fecha_alta
     *
     * @ORM\Column(name="fecha_alta", type="datetime")
     */
    private $fecha_alta;

    /**
     * @var \DateTime $fecha_nacimiento
     *
     * @ORM\Column(name="fecha_nacimiento", type="datetime")
     */
    private $fecha_nacimiento;

    /**
     * @var string $dni
     *
     * @ORM\Column(name="dni", type="string", length=9)
     */
    private $dni;

    /**
     * @var ArrayCollection $grupos
     * @ORM\ManyToMany(targetEntity="Grupo", inversedBy="usuarios")
     */
    protected $grupos;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $foto;
    //@ A s sert\Image(maxSize = "500k"

    /**
     * @var Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
     */
    protected $contenedor;



    public function __construct()
    {
        $this->grupos = new ArrayCollection();
        $this->isActive = true;
        $this->salt = sha1(uniqid(mt_rand(), true));

        $this->contenedor = new ParameterBag();
    }

    /**
     * @return Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
     */
    public function getContenedor()
    {
        if ($this->contenedor == null) {
            $this->contenedor = new ParameterBag();
        }
        return $this->contenedor;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public
    function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public
    function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public
    function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public
    function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public
    function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public
    function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public
    function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public
    function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public
    function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public
    function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Usuario
     */
    public
    function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public
    function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set permite_email
     *
     * @param boolean $permiteEmail
     * @return Usuario
     */
    public
    function setPermiteEmail($permiteEmail)
    {
        $this->permite_email = $permiteEmail;

        return $this;
    }

    /**
     * Get permite_email
     *
     * @return boolean
     */
    public
    function getPermiteEmail()
    {
        return $this->permite_email;
    }

    /**
     * Set fecha_alta
     *
     * @param \DateTime $fechaAlta
     * @return Usuario
     */
    public
    function setFechaAlta($fechaAlta)
    {
        $this->fecha_alta = $fechaAlta;

        return $this;
    }

    /**
     * Get fecha_alta
     *
     * @return \DateTime
     */
    public
    function getFechaAlta()
    {
        return $this->fecha_alta;
    }

    /**
     * Set fecha_nacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Usuario
     */
    public
    function setFechaNacimiento($fechaNacimiento)
    {
        $this->fecha_nacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fecha_nacimiento
     *
     * @return \DateTime
     */
    public
    function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }

    /**
     * Set dni
     *
     * @param string $dni
     * @return Usuario
     */
    public
    function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public
    function getDni()
    {
        return $this->dni;
    }

    /**
     * Get an array of grupos
     *
     * @return array
     */
    function getRoles()
    {
        $roles = array();

        foreach ($this->grupos as $grupo) {
            $roles[] = $grupo->getRole();
        }


        return $roles;
    }

    /**
     * Add a group into the collection
     *
     * @param Grupo $grupo
     * @return Usuario
     */
    public
    function addGrupo(Grupo $grupo)
    {
        $this->grupos[] = $grupo;

        return $this;
    }

    public
    function setGrupos($grupos)
    {
        $this->grupos = $grupos;
    }

    /**
     * Get a collection of grupos
     *
     * @return ArrayCollection
     */
    public
    function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Usuario
     */
    public
    function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Set isActive
     *
     * @return boolean
     */
    public
    function getIsActive()
    {
        return $this->isActive;

    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public
    function isActive()
    {
        return $this->isActive && !$this->isDeleted;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Usuario
     */
    public
    function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public
    function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set isDeleted
     *
     * @return boolean
     */
    public
    function getIsDeleted()
    {
        return $this->isDeleted;

    }

    /**
     * @inheritDoc
     */
    public
    function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public
    function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public
    function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public
    function isEnabled()
    {
        return $this->isActive();
    }

    /**
     * @inheritDoc
     */
    public
    function eraseCredentials()
    {
    }

    function getUsername()
    {
        return $this->getEmail();
    }

    public
    function __toString()
    {
        return $this->getNombre() . ' ' . $this->getApellido();
    }


    /**
     * Set foto
     *
     * @param string $foto
     */
    public
    function setFoto($foto)
    {
        $this->foto = $foto;
    }

    /**
     * Get foto
     *
     * @return string
     */
    public
    function getFoto()
    {
        if(!$this->foto){
            $this->foto="anonimo.jpg";
        }
        return $this->foto;
    }

    /**
     * Sube la foto de la oferta copiándola en el directorio que se indica y
     * guardando en la entidad la ruta hasta la foto
     *
     * @param string $directorioDestino Ruta completa del directorio al que se sube la foto
     */
//    public function subirFoto($directorioDestino)
//    {
//        if (null === $this->foto) {
//            return;
//        }
//
//        $nombreArchivoFoto = uniqid('user-').'-foto1.'.$this->foto->guessExtension();
//
//        $this->foto->move($directorioDestino, $nombreArchivoFoto);
//
//        $this->setFoto($nombreArchivoFoto);
//   }


    /**
     * Remove grupos
     *
     * @param \Lar\UsuarioBundle\Entity\Grupo $grupos
     */
    public
    function removeGrupo(\Lar\UsuarioBundle\Entity\Grupo $grupos)
    {
        $this->grupos->removeElement($grupos);
    }


}