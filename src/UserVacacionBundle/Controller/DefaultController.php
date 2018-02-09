<?php

namespace UserVacacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/" , name="principal_por")
     */
    public function indexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
        throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $session = $this->getRequest()->getSession();
        $session->set('empleadoLog',$user->getLogin());
        return $this->render('UserVacacionBundle:dashboard:index.html.twig');
    }
    public function index1Action(){
        $em=$this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $empleado=$session->get('empleadoLog');
        $permisos=$em->getRepository('UserVacacionBundle:Permiso')->findByEmpleadoNoCargado($empleado);
        $permisosH=array();
        $permisosD=array();
        foreach ($permisos as $permiso) {
            if($permiso->getTipo()=='HORAS'){
                $permisosH[]=$permiso;
            }
            else{
                $permisosD[]=$permiso;
            }
        }
        $vacacionGestiones=$em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($empleado);
        $diasH=0;
        if(count($permisosH)!=0){
            $result=$this->get('cps_user_utilitario')->cargaHorasPermiso($permisosH);
            $diasH=$result[1];
        }
        return $this->render('UserVacacionBundle:dashboard:usuario.html.twig',array('vacacionGestiones' => $vacacionGestiones,
        'diasH'=>$diasH, 'permisos'=>$permisosD));
    }
}
