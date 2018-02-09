<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\Permiso;
use AdminVacacionBundle\Form\PermisoType1;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permiso controller.
 *
 * @Route("p")
 */
class PermisoController extends Controller
{
    /**
     * Lists all permiso entities.
     *
     * @Route("/", name="permiso_index_admin")
     * @Method({"GET","POST"})
     */
    public function indexAction(Request $request)
    {
      $empleado=false;
      $empleadoForm=array('empleado' =>null );
      $form=$this->createForm('AdminVacacionBundle\Form\BusquedaType',$empleadoForm);
      $session = $this->getRequest()->getSession();
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();

      if ($form->isSubmitted() && $form->isValid() )
      {
        $data=$form->getData();
        if ($form->get('buscar')->isClicked()) {
          $session->set('buscarE',$data['empleado']);
        }
        else {
          $session->set('buscarE',0);
        }
      }


      if ($session->get('buscarE')>0) {
        $vac=$em->getRepository('AdminVacacionBundle:VacacionCabecera')->findByEmpleadoDisctinct($session->get('buscarE'));
            if (!is_null($vac)) {
                $empleado=true;
            }
        $query=$em->createQuery(
          'SELECT p
          FROM AdminVacacionBundle:Permiso p
          WHERE p.empleado=:empleado
          AND p.tipoPermiso=:tipoPermiso
          and p.estado  not in (:ess,:es)
          ORDER BY p.id DESC'
          )->setParameter('empleado',$session->get('buscarE'))
            ->setParameter('tipoPermiso','V')
            ->setParameter('es','V')
            ->setParameter('ess','U');
      }
      else {
        $query=$em->createQuery(
          'SELECT p
          FROM AdminVacacionBundle:Permiso p
          WHERE p.tipoPermiso=:tipoPermiso
          and p.estado  not in (:ess,:es)
          ORDER BY p.id DESC'
        )->setParameter('tipoPermiso','V')
        ->setParameter('es','V')
        ->setParameter('ess','U');
      }



        $paginator=$this->get('knp_paginator');
        $pagination=$paginator->paginate($query,$request->query->getInt('page',1),15);

        return $this->render('AdminVacacionBundle:permiso:index.html.twig', array(
            'permisos' => $pagination,
            'formBuscar'=>$form->createView(),
            'empleado'=>$empleado,
        ));
    }


    /**
     * Displays a form to edit an existing permiso entity.
     *
     * @Route("/{id}/edit", name="permiso_edit_admin")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Permiso $permiso)
    {
        $em=$this->getDoctrine()->getManager();

        if ($permiso->getDescontado()) {
          $permiso->setDescontado(false);
          $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se modifico correctamente el permiso ".' '.$permiso->getId());
        }else{
          $permiso->setDescontado(true);
          $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se modifico correctamente el permiso ".' '.$permiso->getId());
        }

        $em->persist($permiso);
        $em->flush($permiso);
        return $this->redirectToRoute('permiso_index_admin');

    }



    /**
     * @Route("/buscarCentroCostoAjax/" , name="buscarCentroCostoAjax")
     */
    public function buscarCentroCostoAjaxAction(Request $request)
    {
      $value = $request->get('term');
      $value=strtoupper($value);

      $em = $this->getDoctrine()->getManager();

      $empleados = $em->getRepository('CpsAdministracionBundle:Centrocosto')->findAjaxValue($value);


      $json = array();
      foreach ($empleados as $empleado) {
          $json[] = array(
              'label' => $empleado->getServicio()." "."-"." ".$empleado->getUbicacion()." "."-"." ".$empleado->getSucursal()." "."-"." ".$empleado->getDistintivo(),
              'value' => $empleado->getId()
          );
      }
      $response = new Response(json_encode($json));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }

    /**
     * Displays a form to edit an existing permiso entity.
     *
     * @Route("/traspaso/permiso/", name="permiso_traspaso_admin")
     * @Method({"GET", "POST"})
     */
    public function traspasoAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $permisoAntiguo=$em->getRepository('AdminVacacionBundle:PermisoAnt')->findByTipoPermiso(3);
        $p=0;
        foreach ($permisoAntiguo as $permisoAnt) {
          $permisoNuevo=$em->getRepository('AdminVacacionBundle:Permiso')->findOneById($permisoAnt->getId());
          if (count($permisoNuevo)>0) {
            
            $permisoNuevo->setVacacionCabecera($permisoAnt->getVacacionCabecera());
             switch ($permisoAnt->getEstado()) {
               case 3:
                 $permisoNuevo->setDescontado(false);
                 $p=$p+1;
                 break;
               
               default:
                 break;
             }

             $em->persist($permisoNuevo);
             $em->flush($permisoNuevo);
          }
          
        }

        $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se modifico correctamente el permiso ".' '.count($permisoAntiguo)." ".$p);
        return $this->redirectToRoute('principal');

    }
    /**
     * Displays a form to edit an existing permiso entity.
     *
     * @Route("/prueba/", name="prueba")
     * @Method({"GET", "POST"})
     */
    public function PruebaAction()
    {
      $em=$this->getDoctrine()->getManager();
        $diasPermisos=$em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosNoCargado(1356);
        $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se modifico correctamente el permiso ".' '.$diasPermisos);
        return $this->redirectToRoute('principal');

    }

}
