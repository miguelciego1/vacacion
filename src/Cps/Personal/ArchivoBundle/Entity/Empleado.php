<?php

namespace Cps\Personal\ArchivoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="per_empleado",
              indexes={ @ORM\Index(name="pat_idx", columns={"paterno"}),
                        @ORM\Index(name="mat_idx", columns={"materno"}),
                        @ORM\Index(name="nom_idx", columns={"nombre"})
                      }
             )
 * @ORM\Entity(repositoryClass="Cps\Personal\ArchivoBundle\Entity\EmpleadoRepository")
 */
class Empleado{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $paterno;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $materno;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=15, unique=true)
     */
    private $docid;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $sexo;

    /**
     * @ORM\Column(type="date")
     */
    private $fchnac;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telfij;
    
    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telcel;
    
    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telref;
    
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $busajax;
    
    /**
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * One Customer has One Cart.
     * @ORM\OneToOne(targetEntity="Cps\Personal\PlanillaBundle\Entity\Auxempleado", mappedBy="empleado")
     */
    private $auxempleado;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Cps\Personal\PlanillaBundle\Entity\Sueldo", mappedBy="empleado")
     */
    private $sueldo;

    
// === Funciones Auxiliares ============================================ //

    public function setId($id){
        $this->id = $id;
    
        return $this;
    }
    
    public function __toString(){
        return $this->getNomCompleto();
    }
    
    public function getNomCompleto(){
        return $this->getPaterno().' '.$this->getMaterno().' '.$this->getNombre();
    }
    
    public function getNomCompletoNPM(){
        return $this->getNombre().' '.$this->getPaterno().' '.$this->getMaterno();
    }
    
    public function getIdNomCompleto(){
        return $this->id.'   '.$this->getNomCompleto();
    }
    
    public function getNombre(){
        return ucwords(strtolower($this->nombre));
    }

    public function getPaterno(){
        return ucwords(strtolower($this->paterno));
    }

    public function getMaterno(){
        return ucwords(strtolower($this->materno));
    }
       
// === Foraneas ======================================================== //

   /**
    * @ORM\ManyToOne(targetEntity="Cps\Administracion\AdministracionBundle\Entity\Nacionalidad", inversedBy="empleados")
    * @ORM\JoinColumn(name="nacionalidad_id", nullable=false)
    */
   protected $nacionalidad;
   
   /**
    * @ORM\ManyToOne(targetEntity="Cps\Administracion\AdministracionBundle\Entity\Dptobol", inversedBy="empleados")
    * @ORM\JoinColumn(name="prodocid_id", nullable=false)
    */
   protected $proDocId;
   
   /**
    * @ORM\ManyToOne(targetEntity="Cps\Administracion\AdministracionBundle\Entity\Tipdocid", inversedBy="empleados")
    * @ORM\JoinColumn(name="tipdocid_id", nullable=false)
    */
   protected $tipDocId;
   
// === Getter ======================================================== //

    /**
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string 
     */
    public function getDocid(){
        return $this->docid;
    }

    /**
     * @return string 
     */
    public function getSexo(){
        return $this->sexo;
    }

    /**
     * @return \DateTime 
     */
    public function getFchnac(){
        return $this->fchnac;
    }

    /**
     * @return string 
     */
    public function getDireccion(){
        return $this->direccion;
    }

    /**
     * @return string 
     */
    public function getTelfij(){
        return $this->telfij;
    }

    /**
     * @return string 
     */
    public function getTelcel(){
        return $this->telcel;
    }

    /**
     * @return string 
     */
    public function getTelref(){
        return $this->telref;
    }

    /**
     * @return string 
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @return string 
     */
    public function getBusajax(){
        return $this->busajax;
    }

    /**
     * @return boolean 
     */
    public function getActivo(){
        return $this->activo;
    }

    /**
     * @return \Cps\Administracion\AdministracionBundle\Entity\Nacionalidad 
     */
    public function getNacionalidad(){
        return $this->nacionalidad;
    }

    /**
     * @return \Cps\Administracion\AdministracionBundle\Entity\Dptobol 
     */
    public function getProDocId(){
        return $this->proDocId;
    }

    /**
     * @return \Cps\Administracion\AdministracionBundle\Entity\Tipdocid 
     */
    public function getTipDocId(){
        return $this->tipDocId;
    }

    public function getAuxempleado(){
        return $this->auxempleado;
    }

}
