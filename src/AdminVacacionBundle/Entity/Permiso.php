<?php

namespace AdminVacacionBundle\Entity;

use AdminVacacionBundle\Entity\VacacionCabecera;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * @ORM\Table(name="emp_por_permiso")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\PermisoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Permiso
{

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=1, columnDefinition="enum('D', 'H')", nullable=false)
	 * @Assert\NotBlank(message="El TIPO no debe estar vacio...")
	 */
	private $tipo; //(D=Dia, H=Hora)

	/**
	 * @ORM\Column(name="cant" ,type="smallint", nullable=false)
	 * Assert\NotBlank(message="El campo CANT no debe estar vacio...")
	 */
	private $tiempoLicencia;

	/**
	 * @ORM\Column(type="string", length=40, nullable=false)
	 * @Assert\NotBlank(message="El MOTIVO no debe estar vacio...")
	 */
	private $motivo;

	/**
	 * @ORM\Column(name="desdeEl", type="date", nullable=false)
	 * @Assert\NotBlank(message="El campo DESDEEL no debe estar vacio...")
	 */
	private $fechaInicio;

	/**
	 * @ORM\Column(name="desdeLas", type="time", nullable=false)
	 * @Assert\NotBlank(message="El campo DESDELAS no debe estar vacio...")
	 */
	private $desdeLas;

	/**
	 * @ORM\Column(name="hastaEl", type="date", nullable=false)
	 * @Assert\NotBlank(message="El campo HASTAEl no debe estar vacio...")
	 */
	private $fechaFin;

	/**
	 * @ORM\Column(name="hastaLas", type="time", nullable=false)
	 * Assert\NotBlank(message="El campo HASTALAS no debe estar vacio...")
	 */
	private $hastaLas;

	/**
	 * @ORM\Column(name="retornoEl", type="date", nullable=false)
	 * @Assert\NotBlank(message="El campo RETORNOEl no debe estar vacio...")
	 */
	private $fechaRegreso;

	/**
	 * @ORM\Column(name="retornoaLas", type="time", nullable=false)
	 * @Assert\NotBlank(message="El campo RETORNOALAS no debe estar vacio...")
	 */
	private $retornoaLas;

	/**
	 * @ORM\Column(name="creadoEl", type="datetime")
	 */
	private $creadoEl;

	/**
	 * @ORM\Column(name="tipoDescuento", type="string", length=1, columnDefinition="enum('C', 'V','G', 'X')", nullable=false)
	 * @Assert\NotBlank(message="El TIPODESCUENTO no debe estar vacio...")
	 */
	private $tipoPermiso; //(V=con cargo a Vacacion, C=con Goce de haber, S=Sin goce de haber)

	/**
	 * @ORM\Column(type="string", length=1, columnDefinition="enum('B', 'L', 'I', 'P', 'R', 'T', 'A','U','V')", nullable=false)
	 */
	private $estado; //(B=Borrador, L=Limpio, I=Limpio RRHH, P=Procesado, R=Procesado RRHH, T=Traspasado, A=Traspaso RRHH)

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="descontado", type="boolean")
	 */
	private $descontado;

// === Foraneas ======================================================== //

	/**
	 * @ORM\ManyToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado")
	 * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
	 *
	 */
	private $empleado;


	/**
	 * @var VacacionCabecera
	 *
	 * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\VacacionCabecera")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="vacacion_id", referencedColumnName="id", nullable=true)
	 * })
	 */
	private $vacacionCabecera;


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
	 * Set tipo
	 *
	 * @param string $tipo
	 * @return Permiso
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
	 * Set tiempoLicencia
	 *
	 * @param integer $tiempoLicencia
	 * @return Permiso
	 */
	public function setTiempoLicencia($tiempoLicencia)
	{
		$this->tiempoLicencia = $tiempoLicencia;

		return $this;
	}

	/**
	 * Get tiempoLicencia
	 *
	 * @return integer
	 */
	public function getTiempoLicencia()
	{
		return $this->tiempoLicencia;
	}

	/**
	 * Set motivo
	 *
	 * @param string $motivo
	 * @return Permiso
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
	 * Set fechaInicio
	 *
	 * @param \DateTime $fechaInicio
	 * @return Permiso
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
	 * Set desdeLas
	 *
	 * @param \DateTime $desdeLas
	 * @return Permiso
	 */
	public function setDesdeLas($desdeLas)
	{
		$this->desdeLas = $desdeLas;

		return $this;
	}

	/**
	 * Get desdeLas
	 *
	 * @return \DateTime
	 */
	public function getDesdeLas()
	{
		return $this->desdeLas;
	}

	/**
	 * Set fechaFin
	 *
	 * @param \DateTime $fechaFin
	 * @return Permiso
	 */
	public function setFechaFin($fechaFin)
	{
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
	 * Set hastaLas
	 *
	 * @param \DateTime $hastaLas
	 * @return Permiso
	 */
	public function setHastaLas($hastaLas)
	{
		$this->hastaLas = $hastaLas;

		return $this;
	}

	/**
	 * Get hastaLas
	 *
	 * @return \DateTime
	 */
	public function getHastaLas()
	{
		return $this->hastaLas;
	}

	/**
	 * Set fechaRegreso
	 *
	 * @param \DateTime $fechaRegreso
	 * @return Permiso
	 */
	public function setFechaRegreso($fechaRegreso)
	{
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
	 * Set retornoaLas
	 *
	 * @param \DateTime $retornoaLas
	 * @return Permiso
	 */
	public function setRetornoaLas($retornoaLas)
	{
		$this->retornoaLas = $retornoaLas;

		return $this;
	}

	/**
	 * Get retornoaLas
	 *
	 * @return \DateTime
	 */
	public function getRetornoaLas()
	{
		return $this->retornoaLas;
	}

	/**
	 * Set creadoEl
	 *
	 * @param \DateTime $creadoEl
	 * @return Permiso
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
	 * Set tipoPermiso
	 *
	 * @param string $tipoPermiso
	 * @return Permiso
	 */
	public function setTipoPermiso($tipoPermiso)
	{
		$this->tipoPermiso = $tipoPermiso;

		return $this;
	}

	/**
	 * Get tipoPermiso
	 *
	 * @return string
	 */
	public function getTipoPermiso()
	{
		return $this->tipoPermiso;
	}

	/**
	 * Set estado
	 *
	 * @param string $estado
	 * @return Permiso
	 */
	public function setEstado($estado)
	{
		$this->estado = $estado;

		return $this;
	}

	/**
	 * Get estado
	 *
	 * @return string
	 */
	public function getEstado()
	{
		return $this->estado;
	}

	/**
	 * Set descontado
	 *
	 * @param boolean $descontado
	 * @return Permiso
	 */
	public function setDescontado($descontado)
	{
		$this->descontado = $descontado;

		return $this;
	}

	/**
	 * Get descontado
	 *
	 * @return boolean
	 */
	public function getDescontado()
	{
		return $this->descontado;
	}

	/**
	 * Set empleado
	 *
	 * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
	 * @return Permiso
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
	 * Set vacacionCabecera
	 *
	 * @param \AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera
	 * @return Permiso
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
}
