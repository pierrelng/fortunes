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

// {{ pagerfanta(my_pager, bootstrap) }}

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findLast();

        $pagedEntities = new Pagerfanta($entities);
        $pagedEntities->setMaxPerPage(5); // 10 by default
        $pagedEntities->setCurrentPage($request->get("page", 1));

        return $this->render('default/index.html.twig', array(
            'entities' => $pagedEntities,
        ));
    }

    /**
     * @Route("/upvote/{id}", name="upvote")
     */
    public function upVoteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Fortune')->find($id);

        if ($entity === null) {
            throw $this->createNotFoundException();
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

        if ($entity === null) {
            throw $this->createNotFoundException();
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
     * @Route("/by_author/{author}", name="byAuthor")
     */
    public function showByAuthorAction($author)
    {
        $authorEntities = $this->getDoctrine()->getRepository('AppBundle:Fortune')->findByAuthor($author);

        return $this->render('default/showByAuthor.html.twig', array(
            'authorEntities' => $authorEntities,
        ));
    }

    /**
     * Displays create form.
     * Creates a new Fortune entity.
     *
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
     * Edit a Fortune entity.
     * Creates a new Fortune entity.
     *
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

        if ($uniqueEntity === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new CommentType, new Comment);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $comment = $form->getData();
            $comment->setFortune($uniqueEntity);
            $em->persist($comment);

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/showUniqueFortune.html.twig', array(
            'item' => $uniqueEntity,
            'form' => $form->createView()
        ));
    }
}

// $this->get('session')->has('key');
// $this->get('session')->set('key', 'value');
// $this->get('session')->get('key', 'default');