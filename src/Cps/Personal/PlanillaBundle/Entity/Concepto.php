<?php

namespace Cps\Personal\PlanillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="per_pla_concepto")
 * @ORM\Entity(repositoryClass="Cps\Personal\PlanillaBundle\Repository\ConceptoRepository")
 */
class Concepto
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
     * @ORM\Column(name="nombre", type="string", length=25)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=8)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="unidad", type="string", length=10)
     */
    private $unidad;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=1)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo2", type="string", length=1)
     */
    private $tipo2;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @var bool
     *
     * @ORM\Column(name="porc", type="boolean", nullable=true)
     */
    private $porc;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Cps\Personal\PlanillaBundle\Entity\Sueldo", mappedBy="concepto")
     */
    private $sueldo;


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
     * @return Concepto
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
     * Set sigla
     *
     * @param string $sigla
     * @return Concepto
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    
        return $this;
    }

    /**
     * Get sigla
     *
     * @return string 
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     * @return Concepto
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad
     *
     * @return string 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Concepto
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
     * Set tipo2
     *
     * @param string $tipo2
     * @return Concepto
     */
    public function setTipo2($tipo2)
    {
        $this->tipo2 = $tipo2;
    
        return $this;
    }

    /**
     * Get tipo2
     *
     * @return string 
     */
    public function getTipo2()
    {
        return $this->tipo2;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Concepto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set porc
     *
     * @param boolean $porc
     * @return Concepto
     */
    public function setPorc($porc)
    {
        $this->porc = $porc;
    
        return $this;
    }

    /**
     * Get porc
     *
     * @return boolean 
     */
    public function getPorc()
    {
        return $this->porc;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return Concepto
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sueldo = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add sueldo
     *
     * @param \Cps\Personal\PlanillaBundle\Entity\Sueldo $sueldo
     * @return Concepto
     */
    public function addSueldo(\Cps\Personal\PlanillaBundle\Entity\Sueldo $sueldo)
    {
        $this->sueldo[] = $sueldo;
    
        return $this;
    }

    /**
     * Remove sueldo
     *
     * @param \Cps\Personal\PlanillaBundle\Entity\Sueldo $sueldo
     */
    public function removeSueldo(\Cps\Personal\PlanillaBundle\Entity\Sueldo $sueldo)
    {
        $this->sueldo->removeElement($sueldo);
    }

    /**
     * Get sueldo
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSueldo()
    {
        return $this->sueldo;
    }
}
