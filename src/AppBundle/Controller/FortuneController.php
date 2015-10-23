<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Fortune;
use AppBundle\Form\FortuneType;

/**
 * Fortune controller.
 *
 * @Route("/fortunes")
 */
class FortuneController extends Controller
{

    /**
     * Lists all Fortune entities.
     *
     * @Route("/", name="fortune")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Fortune')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Fortune entity.
     *
     * @Route("/", name="fortune_create")
     * @Method("POST")
     * @Template("AppBundle:Fortune:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Fortune();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fortune_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Fortune entity.
     *
     * @param Fortune $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Fortune $entity)
    {
        $form = $this->createForm(new FortuneType(), $entity, array(
            'action' => $this->generateUrl('fortune_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Fortune entity.
     *
     * @Route("/new", name="fortune_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Fortune();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Fortune entity.
     *
     * @Route("/{id}", name="fortune_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Fortune')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fortune entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Fortune entity.
     *
     * @Route("/{id}/edit", name="fortune_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Fortune')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fortune entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Fortune entity.
    *
    * @param Fortune $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fortune $entity)
    {
        $form = $this->createForm(new FortuneType(), $entity, array(
            'action' => $this->generateUrl('fortune_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Fortune entity.
     *
     * @Route("/{id}", name="fortune_update")
     * @Method("PUT")
     * @Template("AppBundle:Fortune:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Fortune')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fortune entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fortune_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Fortune entity.
     *
     * @Route("/{id}", name="fortune_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Fortune')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Fortune entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('fortune'));
    }

    /**
     * Creates a form to delete a Fortune entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fortune_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
