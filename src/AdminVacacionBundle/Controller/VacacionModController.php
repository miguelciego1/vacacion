<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\VacacionMod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Vacacionmod controller.
 *
 * @Route("vd")
 */
class VacacionModController extends Controller
{
    /**
     * Lists all vacacionMod entities.
     *
     * @Route("/", name="vacacionmod_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $vacacionMods = $em->getRepository('AdminVacacionBundle:VacacionMod')->findAll();

        return $this->render('vacacionmod/index.html.twig', array(
            'vacacionMods' => $vacacionMods,
        ));
    }

    /**
     * Creates a new vacacionMod entity.
     *
     * @Route("/new", name="vacacionmod_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $vacacionMod = new Vacacionmod();
        $form = $this->createForm('AdminVacacionBundle\Form\VacacionModType', $vacacionMod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vacacionMod);
            $em->flush($vacacionMod);

            return $this->redirectToRoute('vacacionmod_show', array('id' => $vacacionMod->getId()));
        }

        return $this->render('vacacionmod/new.html.twig', array(
            'vacacionMod' => $vacacionMod,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vacacionMod entity.
     *
     * @Route("/{id}", name="vacacionmod_show")
     * @Method("GET")
     */
    public function showAction(VacacionMod $vacacionMod)
    {
        $deleteForm = $this->createDeleteForm($vacacionMod);

        return $this->render('vacacionmod/show.html.twig', array(
            'vacacionMod' => $vacacionMod,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vacacionMod entity.
     *
     * @Route("/{id}/edit", name="vacacionmod_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, VacacionMod $vacacionMod)
    {
        $deleteForm = $this->createDeleteForm($vacacionMod);
        $editForm = $this->createForm('AdminVacacionBundle\Form\VacacionModType', $vacacionMod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vacacionmod_edit', array('id' => $vacacionMod->getId()));
        }

        return $this->render('vacacionmod/edit.html.twig', array(
            'vacacionMod' => $vacacionMod,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vacacionMod entity.
     *
     * @Route("/{id}", name="vacacionmod_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, VacacionMod $vacacionMod)
    {
        $form = $this->createDeleteForm($vacacionMod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vacacionMod);
            $em->flush($vacacionMod);
        }

        return $this->redirectToRoute('vacacionmod_index');
    }

    /**
     * Creates a form to delete a vacacionMod entity.
     *
     * @param VacacionMod $vacacionMod The vacacionMod entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(VacacionMod $vacacionMod)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vacacionmod_delete', array('id' => $vacacionMod->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
