<?php

namespace AdminVacacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * VacacionCabecera
 *
 * @ORM\Table(name="vac_vacacion_cabecera")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\VacacionCabeceraRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class VacacionCabecera
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
	 *
	 * @ORM\Column(name="tipo", type="string", length=1, nullable=false)
	 */
	private $tipo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="reemplazo", type="string", length=90 ,nullable=true)
	 */
	private $reemplazo;

	/**
	 * @var \DateTime
	 *
	 *
	 * @ORM\Column(name="fecha_inicio", type="date")
	 */
	private $fechaInicio;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_fin", type="date")
	 */
	private $fechaFin;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_regreso", type="date")
	 */
	private $fechaRegreso;

	// estados(1='Solicitado',2='Confirmado',3='Historico o Traspasado')
	/**
	 * @var int
	 *
	 * @ORM\Column(name="estado", type="integer")
	 */
	private $estado;

	/**
	 * @var int
	 *
	 * @Assert\GreaterThan(0)
	 * @ORM\Column(name="total_dias", type="integer")
	 */
	private $totalDias;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="creado_el", type="datetime")
	 */
	private $creadoEl;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="modificado_el", type="datetime")
	 */
	private $modificadoEl;

	/**
	 * @ORM\Column(name="anulado", type="boolean",nullable=true)
	 */
	private $anulado;

	/**
	 * Many VacacionCabecera have One Empleado.
	 * @ORM\ManyToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado")
	 * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
	 */
	private $empleado;

	/**
	 * Many VacacionCabecera have One Empleado.
	 * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\Usuario")
	 * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
	 */
	private $usuario;


	/**
	 * Many VacacionCabecera have One Empleado.
	 * @ORM\ManyToOne(targetEntity="Cps\Administracion\AdministracionBundle\Entity\Centrocosto")
	 * @ORM\JoinColumn(name="cen_costo", referencedColumnName="id")
	 */
	private $centroCostos;

	/**
	 * One Product has Many Features.
	 * @ORM\OneToMany(targetEntity="AdminVacacionBundle\Entity\VacacionDetalle", mappedBy="vacacionCabecera")
	 */
	private $vacacionDetalle;

	/**
	 * One Product has Many Features.
	 * @ORM\OneToMany(targetEntity="AdminVacacionBundle\Entity\VacacionMod", mappedBy="vacacionCabecera")
	 */
	private $vacacionMod;
	// ...


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
	 * Set fechaInicio
	 *
	 * @param \DateTime $fechaInicio
	 * @return VacacionCabecera
	 */
	public function setFechaInicio($fechaInicio)
	{
		$this->fechaInicio = $fechaInicio;

		return $this;
	}

	/**
	 * Get fechaInicio
	 *
	 * @return \DateTime
	 */
	public function getFechaInicio()
	{
		return $this->fechaInicio;
	}

	/**
	 * Set fechaFin
	 *
	 * @param \DateTime $fechaFin
	 * @return VacacionCabecera
	 */
	public function setFechaFin($fechaFin)
	{
		$fechaFin = new \DateTime($fechaFin);
		$this->fechaFin = $fechaFin;

		return $this;
	}

	/**
	 * Get fechaFin
	 *
	 * @return \DateTime
	 */
	public function getFechaFin()
	{
		return $this->fechaFin;
	}

	/**
	 * Set fechaRegreso
	 *
	 * @param \DateTime $fechaRegreso
	 * @return VacacionCabecera
	 */
	public function setFechaRegreso($fechaRegreso)
	{
		$fechaRegreso = new \DateTime($fechaRegreso);
		$this->fechaRegreso = $fechaRegreso;

		return $this;
	}

	/**
	 * Get fechaRegreso
	 *
	 * @return \DateTime
	 */
	public function getFechaRegreso()
	{
		return $this->fechaRegreso;
	}

	/**
	 * Set estado
	 *
	 * @param integer $estado
	 * @return VacacionCabecera
	 */
	public function setEstado($estado)
	{
		$this->estado = $estado;

		return $this;
	}

	/**
	 * Get estado
	 *
	 * @return integer
	 */
	public function getEstado()
	{
		return $this->estado;
	}

	/**
	 * Set totalDias
	 *
	 * @param integer $totalDias
	 * @return VacacionCabecera
	 */
	public function setTotalDias($totalDias)
	{
		$this->totalDias = $totalDias;

		return $this;
	}

	/**
	 * Get totalDias
	 *
	 * @return integer
	 */
	public function getTotalDias()
	{
		return $this->totalDias;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function setCreadoEl()
	{
		$this->creadoEl = new \DateTime();

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
	 *
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 *
	 */
	public function setModificadoEl()
	{
		$this->modificadoEl = new \DateTime();

		return $this;
	}

	/**
	 * Get modificadoEl
	 *
	 * @return \DateTime
	 */
	public function getModificadoEl()
	{
		return $this->modificadoEl;
	}


	/**
	 * Set empleado
	 *
	 * @param \AdminVacacionBundle\Entity\Empleado $empleado
	 * @return VacacionCabecera
	 */
	public function setEmpleado(\Cps\Personal\ArchivoBundle\Entity\Empleado $empleado = null)
	{
		$this->empleado = $empleado;

		return $this;
	}

	/**
	 * Get empleado
	 *
	 * @return \AdminVacacionBundle\Entity\Empleado
	 */
	public function getEmpleado()
	{
		return $this->empleado;
	}

	public function _toString()
	{
		return $this->id;
	}

	/**
	 * Set centroCostos
	 *
	 * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
	 * @return VacacionCabecera
	 */
	public function setCentroCostos(\Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos = null)
	{
		$this->centroCostos = $centroCostos;

		return $this;
	}

	/**
	 * Get centroCostos
	 *
	 * @return \Cps\Administracion\AdministracionBundle\Entity\Centrocosto
	 */
	public function getCentroCostos()
	{
		return $this->centroCostos;
	}

	/**
	 * Set usuario
	 *
	 * @param \AdminVacacionBundle\Entity\Usuario $usuario
	 * @return VacacionCabecera
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

	/**
	 * Set reemplazo
	 *
	 * @param string $reemplazo
	 * @return VacacionCabecera
	 */
	public function setReemplazo($reemplazo)
	{
		$this->reemplazo = $reemplazo;

		return $this;
	}

	/**
	 * Get reemplazo
	 *
	 * @return string
	 */
	public function getReemplazo()
	{
		return $this->reemplazo;
	}

	/**
	 * Set anulado
	 *
	 * @param boolean $anulado
	 * @return VacacionCabecera
	 */
	public function setAnulado($anulado)
	{
		$this->anulado = $anulado;

		return $this;
	}

	/**
	 * Get anulado
	 *
	 * @return boolean
	 */
	public function getAnulado()
	{
		return $this->anulado;
	}

	/**
	 * Set tipo
	 *
	 * @param string $tipo
	 * @return VacacionCabecera
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
	 * Add vacacionDetalle
	 *
	 * @param \AdminVacacionBundle\Entity\VacacionDetalle $vacacionDetalle
	 * @return VacacionCabecera
	 */
	public function addVacacionDetalle(\AdminVacacionBundle\Entity\VacacionDetalle $vacacionDetalle)
	{
		$this->vacacionDetalle[] = $vacacionDetalle;

		return $this;
	}

	/**
	 * Remove vacacionDetalle
	 *
	 * @param \AdminVacacionBundle\Entity\VacacionDetalle $vacacionDetalle
	 */
	public function removeVacacionDetalle(\AdminVacacionBundle\Entity\VacacionDetalle $vacacionDetalle)
	{
		$this->vacacionDetalle->removeElement($vacacionDetalle);
	}

	/**
	 * Get vacacionDetalle
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getVacacionDetalle()
	{
		return $this->vacacionDetalle;
	}
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vacacionDetalle = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vacacionMod = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add vacacionMod
     *
     * @param \AdminVacacionBundle\Entity\VacacionMod $vacacionMod
     * @return VacacionCabecera
     */
    public function addVacacionMod(\AdminVacacionBundle\Entity\VacacionMod $vacacionMod)
    {
        $this->vacacionMod[] = $vacacionMod;
    
        return $this;
    }

    /**
     * Remove vacacionMod
     *
     * @param \AdminVacacionBundle\Entity\VacacionMod $vacacionMod
     */
    public function removeVacacionMod(\AdminVacacionBundle\Entity\VacacionMod $vacacionMod)
    {
        $this->vacacionMod->removeElement($vacacionMod);
    }

    /**
     * Get vacacionMod
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVacacionMod()
    {
        return $this->vacacionMod;
    }
}
