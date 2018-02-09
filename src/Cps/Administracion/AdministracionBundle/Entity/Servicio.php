<?php

namespace Cps\Administracion\AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicio
 *
 * @ORM\Table(name="adm_servicio")
 * @ORM\Entity(repositoryClass="Cps\Administracion\AdministracionBundle\Repository\ServicioRepository")
 */
class Servicio{

    public function __construct(){
        $this->centroCostos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $sigla;

    /**
     * @ORM\Column(name="exaSerMedico" , type="boolean")
     */
    private $exaSerMedico;

    /**
     * @ORM\Column(name="tieneCamas" , type="boolean", nullable=false)
     */
    private $tieneCamas;

// === Otras Funciones ================================================= //

    public function __toString(){
        return $this->nombre;
    }

// === Foraneas ======================================================== //

   /**
    * @ORM\OneToMany(targetEntity="Centrocosto", mappedBy="servicio")
    */
   protected $centroCostos;

// === Getter ========================================================= //

    /**
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNombre(){
        return $this->nombre;
    }

    /**
     * @return string
     */
    public function getSigla(){
        return $this->sigla;
    }

    /**
     * @return boolean
     */
    public function getTieneCamas(){
        return $this->tieneCamas;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCentroCostos(){
        return $this->centroCostos;
    }


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Servicio
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Set sigla
     *
     * @param string $sigla
     * @return Servicio
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Set exaSerMedico
     *
     * @param boolean $exaSerMedico
     * @return Servicio
     */
    public function setExaSerMedico($exaSerMedico)
    {
        $this->exaSerMedico = $exaSerMedico;

        return $this;
    }

    /**
     * Get exaSerMedico
     *
     * @return boolean
     */
    public function getExaSerMedico()
    {
        return $this->exaSerMedico;
    }

    /**
     * Set tieneCamas
     *
     * @param boolean $tieneCamas
     * @return Servicio
     */
    public function setTieneCamas($tieneCamas)
    {
        $this->tieneCamas = $tieneCamas;

        return $this;
    }

    /**
     * Add centroCostos
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
     * @return Servicio
     */
    public function addCentroCosto(\Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos)
    {
        $this->centroCostos[] = $centroCostos;

        return $this;
    }

    /**
     * Remove centroCostos
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
     */
    public function removeCentroCosto(\Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos)
    {
        $this->centroCostos->removeElement($centroCostos);
    }
}
