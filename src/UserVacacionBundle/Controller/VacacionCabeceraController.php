<?php

namespace UserVacacionBundle\Controller;

use UserVacacionBundle\Entity\VacacionCabecera;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserVacacionBundle\Entity\VacacionDetalle;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vacacioncabecera controller.
 *
 * @Route("vc")
 */
class VacacionCabeceraController extends Controller
{
    /**
     * Lists all vacacionCabecera entities.
     *
     * @Route("/", name="vacacioncabecera_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $empleado=$session->get('empleadoLog');
        $em = $this->getDoctrine()->getManager();
        $query=$em->createQuery(
          'SELECT v
          FROM UserVacacionBundle:VacacionCabecera v
          WHERE v.empleado=:empleado
          ORDER BY v.id DESC
          ')->setParameter('empleado',$empleado);
        $paginator=$this->get('knp_paginator');
        $pagination=$paginator->paginate($query,$request->query->getInt('page',1),20);

        return $this->render('UserVacacionBundle:vacacioncabecera:index.html.twig', array(
            'vacacionCabeceras' => $pagination,
        ));
    }
    /**
     * Creates a new vacacionCabecera entity.
     *
     * @Route("/new", name="vacacioncabecera_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
      $session = $this->getRequest()->getSession();
      $empleado=$session->get('empleadoLog');
        $vacacionCabecera = new Vacacioncabecera();
        $form = $this->createForm('UserVacacionBundle\Form\VacacionCabeceraType', $vacacionCabecera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
              $em = $this->getDoctrine()->getManager();
              $vacacionGestiones=$em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($empleado);
              $diasDisponibles=$em->getRepository('AdminVacacionBundle:VacacionGestion')->findByDiasDisponibles($empleado);
              $diasPermisos=$em->getRepository('UserVacacionBundle:Permiso')->findDiasPermisosNoCargado($empleado);

              //Valido si el empleado tiene dias disponibkles para tomar la vacacion
              if ($vacacionCabecera->getTotalDias()>$diasDisponibles[0]['diasDisponibles']-$diasPermisos) {
                  $this->get('ras_flash_alert.alert_reporter')->addError("Los dias solicitados sobrepasan sus dias disponibles");
                  return $this->redirectToRoute('vacacioncabecera_new');
              }

              //Cargo el objeto de la cabecera de la vacacion
              $dias=$vacacionCabecera->getTotalDias()-1;
              $feriados=$em->getRepository('CpsAdministracionBundle:Feriado')->findAll();

              $vacacionCabecera->setFechaFin($this->get('cps_user_utilitario')
                                ->fechaFin($vacacionCabecera->getFechaInicio(),$dias,$feriados));
              $vacacionCabecera->setFechaRegreso($this->get('cps_user_utilitario')
                                ->fechaFin($vacacionCabecera->getFechaFin(),1,$feriados));
              $vacacionCabecera->setEstado(1);
              $empleado2=$em->getRepository('CpsPerArchivoBundle:Empleado')->findOneById($empleado);
              $vacacionCabecera->setEmpleado($empleado2);
              $em->getConnection()->beginTransaction();

              try { //cargatr y ´persist los detalles y la cebecera de la cabecera
                    $vacacionDetalles=$this->guardarDetalle($vacacionCabecera,$vacacionGestiones);
                    foreach($vacacionDetalles as $vacacionDetalle){
                        $em->persist($vacacionDetalle);
                    }
                    $em->persist($vacacionCabecera);
                    $em->flush($vacacionCabecera);
                    $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se guardo correctamente la solicitud de vacacion");
                    $em->getConnection()->commit();
                    return $this->redirectToRoute('vacacioncabecera_index');
              } catch (Exception $e) {
                  $em->getConnection()->rollback();
                  $this->get('ras_flash_alert.alert_reporter')->addError("No se pudo guardar la solicitud  de vacacion");
              }
        }

        return $this->render('UserVacacionBundle:vacacioncabecera:new.html.twig', array(
            'vacacionCabecera' => $vacacionCabecera,
            'form' => $form->createView(),
        ));
    }

    public function guardarDetalle($vacacionCabecera,$vacacionGestiones){
        $arrayDetalles=array();

        $diasVacacion=$vacacionCabecera->getTotalDias();
      foreach ($vacacionGestiones as $vacacionGestion)
            {
                $vacacionDetalle=new VacacionDetalle();
                    if($diasVacacion<=$vacacionGestion->getDias()-$vacacionGestion->getTomados())
                    {
                        $vacacionDetalle->setDias($diasVacacion);
                        $diasVacacion=0;
                    }
                    else{
                        $vacacionDetalle->setDias($vacacionGestion->getDias()-$vacacionGestion->getTomados());
                        $diasVacacion=$diasVacacion-$vacacionDetalle->getDias();
                    }

                        $vacacionDetalle->setVacacionCabecera($vacacionCabecera);
                        $vacacionDetalle->setVacacionGestion($vacacionGestion);
                        $vacacionDetalle->setEstado(1);
                        $arrayDetalles[]=$vacacionDetalle;
                    if($diasVacacion<1)
                    {
                        break;
                    }
            }
            return $arrayDetalles;
    }
    /**
     * Finds and displays a VacacionCabecera entity.
     *
     * @Route("/{id}", name="vacacioncabecera_show")
     * @Method("GET")
     */
    public function showAction(VacacionCabecera $vacacionCabecera){
        $em=$this->getDoctrine()->getManager();

        $vacacionDetalles=$em->getRepository('UserVacacionBundle:VacacionDetalle')->findByVacacionCabecera($vacacionCabecera);
        $suspendidas=$em->getRepository('AdminVacacionBundle:Suspendida')->findByVacacionCabecera($vacacionCabecera);
        $permisos=$em->getRepository('UserVacacionBundle:Permiso')->findByVacacionCabecera($vacacionCabecera);
        $empleado=$vacacionCabecera->getEmpleado();
        $totalDiasPermiso=$this->get('cps_user_utilitario')->calcularTotalDias($permisos);
        return $this->render('UserVacacionBundle:vacacioncabecera:show.html.twig',array(
            'vacacionCabecera'=>$vacacionCabecera,
            'vacacionDetalles'=>$vacacionDetalles,
            'permisos'=>$permisos,
            'empleado'=>$empleado,
            'totalDiasPermiso'=>$totalDiasPermiso,
            'suspendidas'=>$suspendidas,
            ));
    }



    /**
     * Deletes a vacacionCabecera entity.
     *
     * @Route("/{id}/delete", name="vacacioncabecera_delete")
     * @Method({"GET","POST"})
     */
    public function deleteAction(Request $request, VacacionCabecera $vacacionCabecera)
    {

            $em = $this->getDoctrine()->getManager();
            $vacacionDetalles=$em->getRepository('UserVacacionBundle:VacacionDetalle')->findByVacacionCabecera($vacacionCabecera);

            foreach ($vacacionDetalles as $vacacionDetalle) {
              $em->remove($vacacionDetalle);
            }
            $em->remove($vacacionCabecera);
            $em->flush($vacacionCabecera);

        return $this->redirectToRoute('vacacioncabecera_index');
    }

    /**
     * Export to PDF
     *
     * @Route("/{id}/pdf", name="vacacioncabecera_print")
     */
    public function pdfAction(VacacionCabecera $vacacionCabecera){
        $em = $this->getDoctrine()->getManager();
        $query=$em->createQuery(
            'SELECT d
            FROM UserVacacionBundle:VacacionDetalle d
            WHERE d.vacacionCabecera=:cabecera'
            )->setParameter('cabecera',$vacacionCabecera->getId());
        $vacacionDetalles=$query->getResult();

        $permisos=$em->getRepository('UserVacacionBundle:Permiso')->findByEmpleadoNoCargado($vacacionCabecera->getEmpleado()->getId());

        $totalDiasPermiso=$this->get('cps_user_utilitario')->calcularTotalDias($permisos);
        $auxEmpleado=$em->getRepository('CpsPerPlanillaBundle:Auxempleado')->findOneByEmpleado($vacacionCabecera->getEmpleado()->getId());

        $html = $this->renderView('UserVacacionBundle:vacacioncabecera:solicitud.html.twig', array(
            'auxEmpleado'=>$auxEmpleado,
            'vacacionCabecera'=>$vacacionCabecera,
            'vacacionDetalles'=>$vacacionDetalles,
            'totalDiasPermiso'=>$totalDiasPermiso
        ));

        $filename = sprintf('SolicitudVacacion-%s.pdf', date('Y-m-d'));

        $response= new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
            array('lowquality' => false,
                    'encoding' => 'utf-8',
                    'page-size' => 'letter',
                    'outline-depth' => 8,
                    'orientation' => 'Portrait',
                    'title'=> 'Solicitud de vacacion',
                    'copies'=>1,
                    'outline'=>true,
                    'print-media-type'=>true,
                    'exclude-from-outline'=>false,
                    'images'=>true,
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
