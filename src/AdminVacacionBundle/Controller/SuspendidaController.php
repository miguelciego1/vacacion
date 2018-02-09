<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\Suspendida;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminVacacionBundle\Entity\VacacionCabecera;
use Symfony\Component\HttpFoundation\Request;

/**
 * Suspendida controller.
 *
 * @Route("suspendida")
 */
class SuspendidaController extends Controller
{
    /**
     * Lists all suspendida entities.
     *
     * @Route("/", name="suspendida_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $suspendidas = $em->getRepository('AdminVacacionBundle:Suspendida')->findAll();

        return $this->render('AdminVacacionBundle:suspendida:index.html.twig', array(
            'suspendidas' => $suspendidas,
        ));
    }

    /**
     * Creates a new Suspendida entity.
     *
     * @Route("/{id}/new", name="suspendida_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request,VacacionCabecera $vacacionCabecera)
    {
        $suspendida = new Suspendida();
        $form = $this->createForm('AdminVacacionBundle\Form\SuspendidaType', $suspendida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $suspendida->setVacacionCabecera($vacacionCabecera);
            $suspendida->setEstado(1);
            $em->persist($suspendida);
            $this->actualizarGestion($suspendida->getDias(),$vacacionCabecera);
            $em->flush();


            return $this->redirectToRoute('vacacioncabecera_index_admin');
        }

        return $this->render('AdminVacacionBundle:suspendida:new.html.twig', array(
            'suspendida' => $suspendida,
            'form' => $form->createView(),
        ));
    }

    public function actualizarGestion($cantidad,$vacacionCabecera)
    {
        $empleado=$vacacionCabecera->getEmpleado()->getId();
        $em = $this->getDoctrine()->getManager();

        $query=$em->createQuery(
            'SELECT g
             FROM AdminVacacionBundle:VacacionGestion g ,AdminVacacionBundle:VacacionDetalle d
             WHERE g.empleado =:empleado
             AND g.estado=:estado
             AND g.id=d.vacacionGestion
             AND d.vacacionCabecera=:cabecera
             ORDER BY g.id DESC'
            )->setParameter('empleado', $empleado)
            ->setParameter('cabecera',$vacacionCabecera->getId())
            ->setParameter('estado',1);

        $vacacionGestiones = $query->getResult();

        foreach($vacacionGestiones as $vacacionGestion){
            if($vacacionGestion->getTomados()>$cantidad){
                $vacacionGestion->setTomados($vacacionGestion->getTomados()-$cantidad);
                $cantidad=0;
                $em->persist($vacacionGestion);
            }
            else{
                $cantidad=$cantidad-$vacacionGestion->getTomados();
                $vacacionGestion->setTomados(0);
                $em->persist($vacacionGestion);


            }
            if($cantidad<1){
                break;
            }
        }
        $em->flush();

    }

    /**
     * Finds and displays a suspendida entity.
     *
     * @Route("/{id}", name="suspendida_show")
     * @Method("GET")
     */
    public function showAction(Suspendida $suspendida)
    {
        return $this->render('AdminVacacionBundle:suspendida:show.html.twig', array(
            'suspendida' => $suspendida,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Suspendida entity.
     *
     * @Route("/{id}/edit", name="suspendida_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Suspendida $suspendida)
    {
        $editForm = $this->createForm('AdminVacacionBundle\Form\SuspendidaType', $suspendida);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($suspendida);
            $em->flush();

            return $this->redirectToRoute('suspendida_edit', array('id' => $suspendida->getId()));
        }

        return $this->render('AdminVacacionBundle:suspendida:edit.html.twig', array(
            'suspendida' => $suspendida,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a suspendida entity.
     *
     * @Route("/{id}", name="suspendida_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Suspendida $suspendida)
    {
        $form = $this->createDeleteForm($suspendida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $suspendida->setEstado(0);
            $em->persist($suspendida);
            $em->flush($suspendida);
        }

        return $this->redirectToRoute('suspendida_index');
    }

}
