<?php

namespace AdminVacacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VacacionMod
 *
 * @ORM\Table(name="vac_vacacion_mod")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\VacacionModRepository")
 */
class VacacionMod
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
     * @var \DateTime
     *
     * @ORM\Column(name="creadoEl", type="datetime")
     */
    private $creadoEl;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="string", length=200)
     */
    private $motivo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoCambio", type="string", length=1)
     */
    private $tipo;

	/**
	 * Many VacacionDetalle have One VacacionCabecera.
	 * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\VacacionCabecera", inversedBy="vacacionMod")
	 * @ORM\JoinColumn(name="vacacion_id", referencedColumnName="id")
	 */
	private $vacacionCabecera;

	/**
	 * Many VacacionDetalle have One VacacionCabecera.
	 * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\Usuario", inversedBy="vacacionMod")
	 * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
	 */
	private $usuario;


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
     * Set creadoEl
     *
     * @param \DateTime $creadoEl
     * @return VacacionMod
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
     * Set motivo
     *
     * @param string $motivo
     * @return VacacionMod
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    
        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return VacacionMod
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set vacacionCabecera
     *
     * @param \AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera
     * @return VacacionMod
     */
    public function setVacacionCabecera(\AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera = null)
    {
        $this->vacacionCabecera = $vacacionCabecera;
    
        return $this;
    }

    /**
     * Get vacacionCabecera
     *
     * @return \AdminVacacionBundle\Entity\VacacionCabecera 
     */
    public function getVacacionCabecera()
    {
        return $this->vacacionCabecera;
    }

    /**
     * Set usuario
     *
     * @param \AdminVacacionBundle\Entity\Usuario $usuario
     * @return VacacionMod
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
