<?php

namespace AdminVacacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="per_pla_bajamedica")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\BajaMedicaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BajaMedica{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=1, columnDefinition="enum('E', 'M', 'R','X','A')", nullable=false)
     * @Assert\NotBlank(message="El TIPO no debe estar vacio...")     
     */
    private $tipo; //(E=Enfermedad, M=Maternidad, R=Riesgo Profesional)
    
   
    
    /**
     * @ORM\Column(name="desdeEl", type="date", nullable=false)
     * @Assert\NotBlank(message="El campo DESDEEL no debe estar vacio...")     
     */
    private $desdeEl;
    
    /**
     * @ORM\Column(name="hastaEl", type="date", nullable=false)
     * 
     */
    private $hastaEl;
    
    /**
     * @ORM\Column(name="creadoEl", type="datetime")
     */
    private $creadoEl;

    /**
     * @ORM\Column(name="proDesdeEl", type="date", nullable=false)    
     */
    private $proDesdeEl;
    
    /**
     * @ORM\Column(name="proHastaEl", type="date", nullable=false)
     * 
     */
    private $proHastaEl;
    
    /**
     * @ORM\Column(type="string", length=1, columnDefinition="enum('B', 'L', 'P', 'T')", nullable=true)
     */
    private $estado; //(B=Borrador, L=Limpio, P=Procesado, T=Traspasado)

// === Foraneas ======================================================== //

   /**
     * Many VacacionGestion have One Empleado.
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
   
// === RetroLLamadas =================================================== //

    /**
     * @ORM\PrePersist
     */
   public function PrePersist(){
      $this->creadoEl = new \DateTime();
      $this->estado = 'P';
   }



   public function obtenerDiferencia($fin,$inicio){
      $start=new \DateTime($inicio);
      $end=new \DateTime($fin);
      $diferencia=$start->diff($end);
      return $diferencia->d;
   }
     
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->tipo!="X" && $this->desdeEl>$this->hastaEl) {
            $context->buildViolation('Error: Verifique las fechas')
                ->atPath('hastaEl')
                ->addViolation();
        }
        $fecha = strtotime ( '-10 month' , strtotime ( date('Y-m-d') ) ) ;
        if ($this->getDesdeEl()<date('Y-m-d',$fecha)) {
            $context->buildViolation('No se permiten bajas antiguas mas de 10 meses')
                ->atPath('desdeEl')
                ->addViolation();
        }
    }

// === Getter and Setter =============================================== //

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
     * @return BajaMedica
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
     * Set desdeEl
     *
     * @param \DateTime $desdeEl
     * @return BajaMedica
     */
    public function setDesdeEl($desdeEl)
    {
        $this->desdeEl = new \DateTime($desdeEl);
    
        return $this;
    }

    /**
     * Get desdeEl
     *
     * @return \DateTime 
     */
    public function getDesdeEl()
    {
        if ($this->desdeEl) {
            return $this->desdeEl->format('Y-m-d');
        }
        
    }
////funciones para planilla muestra la fecha como objeto datetime
    public function getDesdeElObject()
    {
        return $this->desdeEl;
    }

    public function getHastaElObject()
    {
        return $this->hastaEl;
    }

    public function getProDesdeElObject()
    {
        return $this->proDesdeEl;
    }

    public function getProHastaElObject()
    {
        return $this->proHastaEl;
    }
    ///////////////////////////////
   

    /**
     * Get hastaEl
     *
     * @return \DateTime 
     */
    public function getHastaEl()
    {
        if ($this->hastaEl) {
            return $this->hastaEl->format('Y-m-d');
        }
        
    }

    /**
     * Set hastaEl
     *
     * @param \DateTime $hastaEl
     * @return BajaMedica
     */
    public function setHastaEl($hastaEl)
    {
        $this->hastaEl = new \DateTime($hastaEl);
    
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
     * Set proDesdeEl
     *
     * @param \DateTime $proDesdeEl
     * @return BajaMedica
     */
    public function setProDesdeEl($proDesdeEl)
    {
        $this->proDesdeEl =  new \DateTime($proDesdeEl);
    
        return $this;
    }

    /**
     * Get proDesdeEl
     *
     * @return \DateTime 
     */
    public function getProDesdeEl()
    {
        if ($this->proDesdeEl) {
            return $this->proDesdeEl->format('Y-m-d');
        }
    }

    /**
     * Set proHastaEl
     *
     * @param \DateTime $proHastaEl
     * @return BajaMedica
     */
    public function setProHastaEl($proHastaEl)
    {
        $this->proHastaEl = new \DateTime($proHastaEl);
    
        return $this;
    }

    /**
     * Get proHastaEl
     *
     * @return \DateTime 
     */
    public function getProHastaEl()
    {
        if ($this->proHastaEl) {
            return $this->proHastaEl->format('Y-m-d');
        }
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return BajaMedica
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
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return BajaMedica
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
     * @return BajaMedica
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

    public function getCantidad()
    {
        if ($this->getProDesdeEl()<'2017-10-01') {
            $desdeEl=new \DateTime('2017-10-01');
            $diferencia=$desdeEl->diff($this->proHastaEl);
        }else {
            $diferencia=$this->desdeEl->diff($this->hastaEl);
            /*if ($this->empleado->getId()==615) {
          var_dump($this->desdeEl->diff($this->hastaEl));
        }*/
        }

        return $diferencia->days+1;
    }

    public function getDatosAuxiliares($fecIni,$fecFin)
    {
        $desdeEl=new \DateTime($fecIni);
        $hastaEl=new \DateTime($fecFin);
        $ini=0;
        $fin=0;
        $cantidad=0;
        
        if ($this->getProDesdeElObject()<$desdeEl) {
            $ini=$this->getProDesdeElObject()->diff($desdeEl)->days;
            $fch1=$desdeEl;
        }else{
            $fch1=$this->getProDesdeElObject();
        }

        if ($this->getProHastaElObject()>$hastaEl) {
            $fin=$hastaEl->diff($this->getProHastaElObject())->days;
            $fch2=$hastaEl;
        }else{
            $fch2=$this->getProHastaElObject();
        }
        
        $cantidad=$fch1->diff($fch2)->days+1;
        $arrayName = array('ini' =>$ini ,'fin'=>$fin,'cantidad'=>$cantidad );
        /*if ($this->empleado->getId()==475) {
            var_dump($arrayName);
        }*/
        return $arrayName;
    }
}
