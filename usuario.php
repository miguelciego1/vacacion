<?php

namespace Cps\Empleado\AdmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="emp_adm_usuario")
 * @ORM\Entity(repositoryClass="Cps\Empleado\AdmBundle\Entity\UsuarioRepository")
 * @UniqueEntity(fields={"empleado"}, message="Este EMPLEADO ya existe...") 
 * @UniqueEntity(fields="login", message="Este LOGIN esta repetido...") 
 * @ORM\HasLifecycleCallbacks() 
 */
class Usuario implements UserInterface, EquatableInterface, \Serializable {

    public function __construct(){
        $this->accesos  = new \Doctrine\Common\Collections\ArrayCollection();
    }

// INICIO Metodos requerido por la interfaz UserInterface ====================================
    
    public function getUsername(){
        return $this->login;
    }
    
    public function isEqualTo(UserInterface $usuario){
        return $this->getUsername() == $usuario->getUsername();
    }
    
    public function eraseCredentials(){
    }

    public function getRoles(){
        $resp = 'ROLE_EMPADM';
        return array($resp);
    }
    
    public function getSalt(){
        return false;
    }
    
    public function getPassword(){
        return $this->password;
    }
    
// Metodos requerido cuando la entidad USUARIO se relaciona con Cargo o Rol ==================   

    public function serialize(){
       return serialize($this->id);
    }
 
    public function unserialize($data){
        $this->id = unserialize($data);
    }
     
// FIN Metodos requerido por la interfaz UserInterface =======================================
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   /**
    * @ORM\Column(type="string", length=8, unique=true)
    * @Assert\NotBlank(message="El LOGIN no debe estar vacio...")
    * @Assert\Length(min=4, max=8)
    */
    private $login;

   /**
    * @ORM\Column(type="string", length=255, nullable=false)
    * @Assert\Length(min=5)
    */
    private $password;
    
    /**
     * @ORM\Column(name="ingresoEl", type="date")
     * @Assert\NotBlank(message="El INGRESO no debe estar vacio...")
     */
    private $ingresoEl;

    /**
     * @ORM\Column(name="retiroEl", type="date", nullable=true)
     */
    private $retiroEl;
    
    /**
     * @ORM\Column(name="creadoEl", type="datetime")
     */
    private $creadoEl;

    /**
     * @ORM\Column(name="modifiEl", type="datetime", nullable=true)
     */
    private $modifiEl;
    
// === Foraneas ======================================================== //
    
   /**
    * @ORM\OneToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado", inversedBy="empAdmUsu")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\NotBlank(message="El campo EMPLEADO no debe estar vacio...")
    */
   protected $empleado;
   
// === Funciones Auxiliares ============================================ //

    public function setId($id){
        $this->id = $id;
    
        return $this;
    }

    public function __toString(){
        return $this->empleado->getNomCompleto();
    }         

// === Retrollamadas =================================================== //
    
    /**
     * @ORM\PrePersist
     */
   public function PrePersist(){
      $this->login    = strtolower($this->login);
      $this->creadoEl = new \DateTime();
   }
   
    /**
     * @ORM\PreUpdate
     */
   public function PreUpdate(){
      $this->login    = strtolower($this->login);
      $this->modifiEl = new \DateTime();
   }

// === Gets y Sets ===================================================== //

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
     * Set login
     *
     * @param string $login
     * @return Usuario
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set ingresoEl
     *
     * @param \DateTime $ingresoEl
     * @return Usuario
     */
    public function setIngresoEl($ingresoEl)
    {
        $this->ingresoEl = $ingresoEl;

        return $this;
    }

    /**
     * Get ingresoEl
     *
     * @return \DateTime 
     */
    public function getIngresoEl()
    {
        return $this->ingresoEl;
    }

    /**
     * Set retiroEl
     *
     * @param \DateTime $retiroEl
     * @return Usuario
     */
    public function setRetiroEl($retiroEl)
    {
        $this->retiroEl = $retiroEl;

        return $this;
    }

    /**
     * Get retiroEl
     *
     * @return \DateTime 
     */
    public function getRetiroEl()
    {
        return $this->retiroEl;
    }

    /**
     * Set creadoEl
     *
     * @param \DateTime $creadoEl
     * @return Usuario
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
     * Set modifiEl
     *
     * @param \DateTime $modifiEl
     * @return Usuario
     */
    public function setModifiEl($modifiEl)
    {
        $this->modifiEl = $modifiEl;

        return $this;
    }

    /**
     * Get modifiEl
     *
     * @return \DateTime 
     */
    public function getModifiEl()
    {
        return $this->modifiEl;
    }

    /**
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return Usuario
     */
    public function setEmpleado(\Cps\Personal\ArchivoBundle\Entity\Empleado $empleado)
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
}
