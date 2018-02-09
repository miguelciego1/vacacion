<?php

namespace AdminVacacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminVacacionBundle\Utilitarios\Utilitario;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AdminVacacionBundle\Form\FechaType;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/" ,name="principal")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('buscarE',0);
        // $session->set('buscarPermiso',0);
        // $session->set('buscarVacacion',0);
        return $this->render('AdminVacacionBundle:Default:index.html.twig');
    }
    /**
     * Renders the "new" form
     *
     * @Route("/new/", name="calcular_fecha_new")
     * @Method("GET")
     */
    public function newAction()
    {
        $entity = array('fechaInicio'=>new \DateTime(),'dias'=>0);
        $form = $this->createCreateForm($entity);

        return $this->render('AdminVacacionBundle:Default:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Creates a new Demo entity.
     *
     * @Route("/calcular/", name="calcular_fecha")
     * @Method("POST")
     *
     */
    public function createAction(Request $request)
    {
        //This is optional. Do not do this check if you want to call the same action using a regular request.
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }

        $entity = array('fechaInicio'=>new \DateTime(),'dias'=>0);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //Cargo el objeto de la cabecera de la vacacion
            $em=$this->getDoctrine()->getManager();
            $entity2=$form->getData();
            $dias=$entity2['dias']-1;
            $feriados=$em->getRepository('CpsAdministracionBundle:Feriado')->findAll();
            $fechaV=$entity2['fechaInicio']->format('Y-m-d');
            if (date("w",strtotime($fechaV))==0) {

                return new JsonResponse(array('message' => 'Su fecha de Inicio no puede ser fin de semana'), 400);
            }
            else{
                foreach ($feriados as $feriado) {
                    $fechaFeriado=$feriado->getFecha()->format('Y-m-d');
                    if($fechaV==$fechaFeriado){
                        return new JsonResponse(array('message' => 'Su fecha de Inicio no puede ser FERIADO'), 400);
                    }
                }
            }
            $fecha=$this->get('cps_user_utilitario')->fechaFin($entity2['fechaInicio'],$dias,$feriados);
			$fecha2=$this->get('cps_user_utilitario')->fechaFin($fecha,1,$feriados);
            return new JsonResponse(array('message' => 'Fecha Fin'.':'.'  '.$fecha.' '.'(año-mes-dia) **** Fecha Regreso:'.'  '.$fecha2.' '.'(año-mes-dia)'), 200);
        }

        $response = new JsonResponse(
            array(
                'message' => 'Error',
                'form' => $this->renderView('AdminVacacionBundle:Default:form.html.twig',
                    array(
                        'entity' => $entity,
                        'form' => $form->createView(),
                    ))), 400);

        return $response;
    }

    /**
     * Creates a form to create a Demo entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($fecha)
    {
        $form = $this->createForm(new FechaType(), $fecha,
            array(
                'action' => $this->generateUrl('calcular_fecha'),
                'method' => 'POST',
            ));

        return $form;
    }
}
