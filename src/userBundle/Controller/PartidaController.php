<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PartidaController extends Controller
{
    public function indexAction(Request $request)
    {
        $em       = $this->getDoctrine()->getManager();
        $dql      = "SELECT p FROM userBundle:Partida p ORDER BY p.id DESC";
        $partidas = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $partidas,
            $request->query->getInt('page', 1),
            3
        );

        //exit('partidas');
        return $this->render('userBundle:Partida:index.html.twig', array('pagination' => $pagination));

    }

    public function customAction(Request $request)
    {
        $idUser = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $em  = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM userBundle:Partida p JOIN p.user u WHERE u.id = :idUser ORDER BY p.id DESC";

        $partidas = $em->createQuery($dql)->setParameter('idUser', $idUser);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $partidas,
            $request->query->getInt('page', 1),
            3
        );

        //$updateForm = $this->createCustomForm(':PARTIDA_ID', 'PUT', 'partida_process');

        return $this->render('userBundle:Partida:custom.html.twig', array('pagination' => $pagination));
    }

    public function viewAction($id)
    {
        $partida = $this->getDoctrine()->getRepository('userBundle:Partida')->find($id);

        if (!$partida) {
            $messageException = $this->get('translator')->trans('Game not found.');
            throw $this->createNotFoundException($messageException);
        }

        $deleteForm = $this->createCustomForm($partida->getId(), 'DELETE', 'partida_delete');

        $user = $partida->getUser();

        return $this->render('userBundle:Partida:view.html.twig', array('partida' => $partida, 'user' => $user, 'delete_form' => $deleteForm->createView()));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $partida = $em->getRepository('userBundle:Partida')->find($id);

        if (!$partida) {
            $messageException = $this->get('translator')->trans('Game not found.');
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createCustomForm($partida->getId(), 'DELETE', 'partida_delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em->remove($partida);
            $em->flush();

            $successMessage = $this->get('translator')->trans('The game has been deleted.');
            $this->addFlash('mensaje', $successMessage);

            return $this->redirectToRoute('partida_index');
        }
    }
    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }

    public function statisticsAction(Request $request)
    {

        $em       = $this->getDoctrine()->getManager();
        $dql      = "SELECT p FROM userBundle:Partida p WHERE p.status = 1 ORDER BY p.score DESC";
        $partidas = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $partidas,
            $request->query->getInt('page', 1),
            3
        );

        $em2   = $this->getDoctrine()->getManager();
        $query = $em2->createQuery('SELECT p  FROM userBundle:Partida p WHERE p.status = 1 ORDER BY p.score DESC')
            ->setMaxResults(1);

        try {
            $part = $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $part = null;
        }

        return $this->render('userBundle:Partida:statistics.html.twig', array('pagination' => $pagination, 'part' => $part));
    }

}
