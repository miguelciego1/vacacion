<?php

namespace UserVacacionBundle\Controller;

use UserVacacionBundle\Entity\Permiso;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/", name="permiso_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $empleado=$session->get('empleadoLog');
        $query=$em->createQuery(
          'SELECT p
          FROM UserVacacionBundle:Permiso p
          WHERE p.empleado=:empleado
          and p.estado not in (:es,:ess)
          ORDER BY p.id DESC'
          )->setParameter('empleado',$empleado)
        ->setParameter('ess','V')
        ->setParameter('es','U');

          $paginator=$this->get('knp_paginator');
          $pagination=$paginator->paginate($query,$request->query->getInt('page',1),20);

          return $this->render('UserVacacionBundle:permiso:index.html.twig', array(
              'permisos' => $pagination,
          ));


    }

    /**
     * Creates a new permiso entity.
     *
     * @Route("/new", name="permiso_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
      $session = $this->getRequest()->getSession();
      $empleado=$session->get('empleadoLog');
        $em = $this->getDoctrine()->getManager();
        $permiso = new Permiso();
        $form = $this->createForm('UserVacacionBundle\Form\PermisoType', $permiso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dias=$permiso->getTiempoLicencia()-1;
            $feriados=$em->getRepository('CpsAdministracionBundle:Feriado')->findAll();
            if($permiso->getTipo()=='DIAS'){
                $permiso->setFechaFin($this->get('cps_user_utilitario')
                ->fechaFin($permiso->getFechaInicio(),$dias,$feriados));
                $permiso->setFechaRegreso($this->get('cps_user_utilitario')
                ->fechaFin($permiso->getFechaFin(),1,$feriados));
              
            }
            else{
                $permiso->setFechaFin($this->get('cps_user_utilitario')
                ->fechaHoraFin($permiso->getFechaInicio(),$permiso->getTiempoLicencia()));
                $permiso->setFechaRegreso($permiso->getFechaFin());
            }
            $empleado2=$em->getRepository('CpsPerArchivoBundle:Empleado')->findOneById($empleado);
            $permiso->setFechaSolicitud(new \DateTime("now"));
            $permiso->setEmpleado($empleado2);
            $permiso->setEstado(1);

            $em->persist($permiso);
            $em->flush($permiso);
              $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se guardo correctamente la solicitud de permiso");



            return $this->redirectToRoute('permiso_index');
        }

        return $this->render('UserVacacionBundle:permiso:new.html.twig', array(
            'permiso' => $permiso,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a permiso entity.
     *
     * @Route("/{id}", name="permiso_show")
     * @Method("GET")
     */
    public function showAction(Permiso $permiso)
    {
        return $this->render('UserVacacionBundle:permiso:show.html.twig', array(
            'permiso' => $permiso,
        ));
    }

    /**
     * Displays a form to edit an existing permiso entity.
     *
     * @Route("/{id}/edit", name="permiso_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Permiso $permiso)
    {
        $editForm = $this->createForm('UserVacacionBundle\Form\PermisoType', $permiso);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();
              $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se modifico correctamente la solicitud de permiso ".' '.$permiso->getId());

            return $this->redirectToRoute('permiso_index');
        }

        return $this->render('UserVacacionBundle:permiso:edit.html.twig', array(
            'permiso' => $permiso,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a permiso entity.
     *
     * @Route("/{id}/delete", name="permiso_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Permiso $permiso)
    {

            $em = $this->getDoctrine()->getManager();
            $em->remove($permiso);

            $em->flush($permiso);
            $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se elimino correctamente la solicitud de permiso".' '.$permiso->getId());


        return $this->redirectToRoute('permiso_index');
    }
    public function calcularTotalDias($permisos){
        $totalDiasPermiso=0;
        $totalHorasPermiso=0;
        $saldo=0;
        $permisoH=array();
        foreach ($permisos as $permiso) {//itera por cada registro de gestion y suma los dias de permisos
           if($permiso->getTipo()=="DIAS")
           {
               $totalDiasPermiso=$totalDiasPermiso+$permiso->getTiempoLicencia();
           }
           else
           {
               $permisoH[]=$permiso;
           }
        }
        foreach($permisoH as $per){
            $saldo=$saldo+$per->getTiempoLicencia();
        }
        $dias=$saldo/7;
        $dias=round($dias, 0, PHP_ROUND_HALF_DOWN);
        $totalDiasPermiso=$totalDiasPermiso+$dias;

        return $totalDiasPermiso;
    }
    /**
     * Export to PDF
     *
     * @Route("/{id}/pdfP", name="permiso_print")
     */
     public function pdfAction(Permiso $permiso)
    {
        $em = $this->getDoctrine()->getManager();
        $empleado=$em->getRepository('CpsPerArchivoBundle:Empleado')->findOneById($permiso->getEmpleado()->getId());
        $auxEMpleado=$em->getRepository('CpsPerPlanillaBundle:Auxempleado')->findOneByEmpleado($permiso->getEmpleado()->getId());
        $html = $this->renderView('UserVacacionBundle:permiso:solicitudDia.html.twig', array(
            'empleado'=>$empleado,
            'permiso'=>$permiso,
            'auxempleado'=>$auxEMpleado
        ));

        $filename = sprintf('SolicitudPermiso-%s.pdf', date('Y-m-d'));

        $response= new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
            array('lowquality' => false,
                    'encoding' => 'utf-8',
                    'page-size' => 'Letter',
                    'outline-depth' => 8,
                    'orientation' => 'Portrait',
                    'title'=> 'Solicitud de vacacion',
                    'header-font-size'=>7
                    )),
            200,
            array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('inline; filename="%s"', $filename),
            )
        );
        return $response;
    }


}
