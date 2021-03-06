<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Fortune;
use AppBundle\Form\FortuneType;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Pagerfanta\Pagerfanta;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findLastPublished();

        $pagedEntities = new Pagerfanta($entities);
        $pagedEntities->setMaxPerPage(5); // 10 by default
        $pagedEntities->setCurrentPage($request->get("page", 1));

        return $this->render('default/index.html.twig', array(
            'entities' => $pagedEntities,
        ));
    }

    /**
     * No route needed, displayed on every page.
     */
    public function headerAction()
    {
        $nbUnpublishedEntities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->countUnpublished();

        return $this->render('default/_header.html.twig', array(
            'nbUnpublishedEntities' => $nbUnpublishedEntities,
        ));
    }

    /**
     * No route needed, displayed on every page.
     */
    public function showBestRatedAction()
    {
        $bestEntities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findBestRated(3);

        return $this->render('default/_showBestRated.html.twig', array(
            'bestEntities' => $bestEntities,
        ));
    }

    /**
     * @Route("/upvote/{id}", name="upvote")
     */
    public function upVoteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        if ($this->get('session')->has('upvotedId_'.$id)) {
            return $this->redirectToRoute('homepage');
        }

        $this->get('session')->set('upvotedId_'.$id, 'value');

        $entity->voteUp();
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/downvote/{id}", name="downvote")
     */
    public function downVoteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        if ($this->get('session')->has('downvotedId_'.$id)) {
            return $this->redirectToRoute('homepage');
        }

        $this->get('session')->set('downvotedId_'.$id, 'value');

        $entity->voteDown();
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('homepage');
    }


    /**
     * @Route("/by_author/{author}", name="byAuthor")
     */
    public function showByAuthorAction($author)
    {
        $authorEntities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findByAuthor($author);

        if (!$authorEntities) {
            throw $this->createNotFoundException('Unable to find author.');
        }

        return $this->render('default/showByAuthor.html.twig', array(
            'authorEntities' => $authorEntities,
        ));
    }

    /**
     * @Route("/new", name="createFortune")
     */
    public function createAction(Request $request)
    {
        // Create form.
        $form = $this->createForm(new FortuneType, new Fortune);
        
        // Create entity.
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/create_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/fortune/{id}/edit", name="editFortune")
     */
    public function editAction($id, Request $request)
    {
        $uniqueEntity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$uniqueEntity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        // Create form.
        $form = $this->createForm(new FortuneType, $uniqueEntity);
        
        // Create entity.
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $uniqueEntity = $form->getData();
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/edit_form.html.twig', array(
            'item' => $uniqueEntity,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/fortune/{id}", name="uniqueFortune")
     */
    public function showUniqueFortuneAction($id, Request $request)
    {
        $uniqueEntity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$uniqueEntity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        $form = $this->createForm(new CommentType, new Comment);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $comment = $form->getData();
            $comment->setFortune($uniqueEntity);
            $em->persist($comment);

            $em->flush();

            return $this->redirectToRoute('uniqueFortune', array('id' => $id));
        }

        return $this->render('default/showUniqueFortune.html.twig', array(
            'item' => $uniqueEntity,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/moderate", name="moderate")
     */
    public function showUnpublishedAction()
    {
        $unpublishedEntities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findUnpublished();

        return $this->render('default/moderate.html.twig', array(
            'unpublishedEntities' => $unpublishedEntities,
        ));
    }

    /**
     * @Route("/moderate/publish/{id}", name="publish")
     */
    public function publishAction($id)
    {
        $unpublishedEntity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$unpublishedEntity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        $unpublishedEntity->setPublished();
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('moderate');
    }

    /**
     * @Route("/moderate/delete/{id}", name="delete")
     */
    public function deleteAction($id)
    {
        $unpublishedEntity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if (!$unpublishedEntity) {
            throw $this->createNotFoundException('Unable to find Fortune.');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($unpublishedEntity);
        $em->flush();

        return $this->redirectToRoute('moderate');
    }
}
