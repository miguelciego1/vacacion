<?php

namespace Cps\Administracion\AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sucursal
 *
 * @ORM\Table(name="adm_sucursal")
 * @ORM\Entity(repositoryClass="Cps\Administracion\AdministracionBundle\Repository\SucursalRepository")
 */
class Sucursal{

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
     * @ORM\Column(type="string", length=25)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $sigla;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nrouv;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nromza;

    /**
     * @ORM\Column(name="atencionEme", type="boolean")
     */
    private $atencionEme;

    /**
     * @ORM\Column(name="atencionHos" ,type="boolean")
     */
    private $atencionHos;

    /**
     * @ORM\Column(name="atencionPol", type="boolean")
     */
    private $atencionPol;

// === Foraneas ======================================================== //

   /**
    * @ORM\OneToMany(targetEntity="Centrocosto", mappedBy="sucursal")
    */
   protected $centroCostos;

// === Otras Funciones ================================================= //

    public function __toString(){
        return $this->nombre;
    }

// === Getter ========================================================== //

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
     * @return string
     */
    public function getDireccion(){
        return $this->direccion;
    }

    /**
     * @return integer
     */
    public function getNrouv(){
        return $this->nrouv;
    }

    /**
     * @return integer
     */
    public function getNromza(){
        return $this->nromza;
    }

    /**
     * @return boolean
     */
    public function getAtencionEme(){
        return $this->atencionEme;
    }

    /**
     * @return boolean
     */
    public function getAtencionHos(){
        return $this->atencionHos;
    }

    /**
     * @return boolean
     */
    public function getAtencionPol(){
        return $this->atencionPol;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCentroCostos(){
        return $this->centroCostos;
    }

// ===================================================================== //


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Sucursal
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
     * @return Sucursal
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Sucursal
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Set nrouv
     *
     * @param integer $nrouv
     * @return Sucursal
     */
    public function setNrouv($nrouv)
    {
        $this->nrouv = $nrouv;

        return $this;
    }

    /**
     * Set nromza
     *
     * @param integer $nromza
     * @return Sucursal
     */
    public function setNromza($nromza)
    {
        $this->nromza = $nromza;

        return $this;
    }

    /**
     * Set atencionEme
     *
     * @param boolean $atencionEme
     * @return Sucursal
     */
    public function setAtencionEme($atencionEme)
    {
        $this->atencionEme = $atencionEme;

        return $this;
    }

    /**
     * Set atencionHos
     *
     * @param boolean $atencionHos
     * @return Sucursal
     */
    public function setAtencionHos($atencionHos)
    {
        $this->atencionHos = $atencionHos;

        return $this;
    }

    /**
     * Set atencionPol
     *
     * @param boolean $atencionPol
     * @return Sucursal
     */
    public function setAtencionPol($atencionPol)
    {
        $this->atencionPol = $atencionPol;

        return $this;
    }

    /**
     * Add centroCostos
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
     * @return Sucursal
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
