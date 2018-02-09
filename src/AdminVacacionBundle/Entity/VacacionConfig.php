<?php

namespace AdminVacacionBundle\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * VacacionConfig
 *
 * @ORM\Table(name="vac_vacacion_config")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\VacacionConfigRepository")
 * @UniqueEntity(
 *     fields={"empleado"},
 *     errorPath="empleado",
 *     message="Ya existe un registro para este empleado"
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class VacacionConfig
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=9)
     * @Assert\Regex("/-+/")
     * 
     * @ORM\Column(name="gestion", type="string", length=9)
     */
    private $gestion;

    /**
     * @var int
     *
     * @ORM\Column(name="dias", type="integer")
     */
    private $dias;

    /**
     * @var int
     *
     * @ORM\Column(name="permiso", type="integer")
     */
    private $permiso;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creadoEl", type="datetime")
     */
    private $creadoEl;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado")
     * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
     *
     */
    private $empleado;

    /**
     * Many VacacionCabecera have One Empleado.
     * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;


    
    /**
     * @ORM\PrePersist
     */
   public function PrePersist(){
      $this->creadoEl = new \DateTime();
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
     * Set gestion
     *
     * @param string $gestion
     * @return VacacionConfig
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;

        return $this;
    }

    /**
     * Get gestion
     *
     * @return string 
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * Set dias
     *
     * @param integer $dias
     * @return VacacionConfig
     */
    public function setDias($dias)
    {
        $this->dias = $dias;

        return $this;
    }

    /**
     * Get dias
     *
     * @return integer 
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * Set permiso
     *
     * @param integer $permiso
     * @return VacacionConfig
     */
    public function setPermiso($permiso)
    {
        $this->permiso = $permiso;

        return $this;
    }

    /**
     * Get permiso
     *
     * @return integer 
     */
    public function getPermiso()
    {
        return $this->permiso;
    }

    /**
     * Set creadoEl
     *
     * @param \DateTime $creadoEl
     * @return VacacionConfig
     */
    public function setCreadoEl($creadoEl)
    {
        $this->creadoEl = $creadoEl;

        return $this;
    }

    /**
     * Get creadoEl
     *
     * @return \DateTime 
     */
    public function getCreadoEl()
    {
        return $this->creadoEl;
    }

    /**
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return VacacionConfig
     */
    public function setEmpleado(\Cps\Personal\ArchivoBundle\Entity\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \Cps\Personal\ArchivoBundle\Entity\Empleado 
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * Set usuario
     *
     * @param \AdminVacacionBundle\Entity\Usuario $usuario
     * @return VacacionConfig
     */
    public function setUsuario(\AdminVacacionBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AdminVacacionBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
