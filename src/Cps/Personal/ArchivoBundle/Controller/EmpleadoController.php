<?php

namespace Cps\Personal\ArchivoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpleadoController extends Controller
{
    /**
     * @Route("/buscarEmpleadoAjax" , name="buscarEmpleadoAjax")
     */
    public function buscarEmpleadoAjaxAction(Request $request)
    {
      $value = $request->get('term');
      $value=strtoupper($value);

      $em = $this->getDoctrine()->getManager();

      $empleados = $em->getRepository('CpsPerArchivoBundle:Empleado')->findAjaxValue($value);


      $json = array();
      foreach ($empleados as $empleado) {
          $json[] = array(
              'label' => $empleado->getNomCompleto(),
              'value' => $empleado->getId()
          );
      }
      $response = new Response(json_encode($json));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }

}
