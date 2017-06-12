<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use userBundle\Entity\Comment;
use userBundle\Form\CommentType;


class CommentController extends Controller
{
	public function addAction($id){
		$foro = $this->getDoctrine()->getRepository('userBundle:Foro')->find($id);
        //$foro = $this.getForo($foro_id); 

		$user = $this->container->get('security.context')->getToken()->getUser();
	
		$comment = new Comment();
		$comment->setForo($foro);
		$comment->setUser($user);
		$form = $this->createForm(new CommentType(), $comment);

		return $this->render('userBundle:Comment:add.html.twig', array('form' => $form->createView(), 'comment' => $comment));
	}

	// private function createCreateForm(Comment $entity)
 //    {
    
 //        $form = $this->createForm(new CommentType(), $entity, array(
 //            'action' => $this->generateUrl('comment_create'),
 //            'method' => 'POST'
 //        ));
            
 //        return $form;
 //    }

    public function createAction($foro_id)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$foro = $this->getDoctrine()->getRepository('userBundle:Foro')->find($foro_id);
        
        //$foro = $this.getForo($foro_id);
        $comment = new Comment();
        $comment->setForo($foro);
		$comment->setUser($user);

		$request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $comment);
        $form->bind($request);

        // $form = $this->createCreateForm($comment);
        // $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $successMessage = $this->get('translator')->trans('The comment has been created');
            $this->addFlash('mensaje', $successMessage);

            //return $this->redirect($this->generateUrl('comment_view', array('id' => $comment->getForo()->getId())));
            return $this->redirectToRoute('foro_index');
                
        }
        return $this->render('userBundle:Comment:add.html.twig', array('form' => $form->createView(), 'comment' => $comment));
    }

     public function customAction(Request $request)
    {
        $idUser = $this->container->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT c FROM userBundle:Comment c JOIN c.user u WHERE u.id = :idUser ORDER BY c.id DESC";

        $comments = $em->createQuery($dql)->setParameter('idUser' , $idUser);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            3
        );

        $deleteFormAjax = $this->createCustomForm(':COMMENT_ID', 'DELETE', 'comment_delete');

        return $this->render('userBundle:Comment:custom.html.twig', array('pagination' => $pagination, 'delete_form_ajax' => $deleteFormAjax->createView()));
    }

    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $comment = $em->getRepository('userBundle:Comment')->find($id);
        
        if(!$comment)
        {
            $messageException = $this->get('translator')->trans('Comment not found.');
            throw $this->createNotFoundException($messageException);
        }
        
        $allComment = $em->getRepository('userBundle:Comment')->findAll();
        $countComments = count($allComment);
        
        $form = $this->createCustomForm($comment->getId(), 'DELETE', 'comment_delete');
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            if($request->isXMLHttpRequest())
            {
                $res = $this->deleteUser($em, $comment);
                
                return new Response(
                    json_encode(array('removed' => $res['removed'], 'message' => $res['message'], 'countComments' => $countComments)),
                    200,
                    array('Content-Type' => 'application/json')
                );
            }
            
            $res = $this->deleteUser($em, $comment);
            $this->addFlash($res['alert'], $res['message']);
            return $this->redirectToRoute('comment_custom');            
        }
    }

     private function deleteUser($em, $comment)
    {
            $em->remove($comment);
            $em->flush();
            
            $message = $this->get('translator')->trans('The comment has been deleted.');
            $removed = 1;
            $alert = 'mensaje';
        
        return array('removed' => $removed, 'message' => $message, 'alert' => $alert);
    }

     public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $comment = $em->getRepository('userBundle:Comment')->find($id);
        
        if(!$comment)
        {
             $messageException = $this->get('translator')->trans('Comment not found.');
            throw $this->createNotFoundException($messageException);
        }
        
        $form = $this->createEditForm($comment);
        
        return $this->render('userBundle:Comment:edit.html.twig', array('comment' => $comment, 'form' => $form->createView()));
    }
     private function createEditForm(Comment $entity)
    {
        $form = $this->createForm(new CommentType(), $entity, array('action' => $this->generateUrl('comment_update', array('id' => $entity->getId())), 'method' => 'PUT'));

        return $form;
    }

    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $comment = $em->getRepository('userBundle:Comment')->find($id);
        
        if(!$comment)
        {
            throw $this->createNotFoundException('comment not found');
        }
        
        $form = $this->createEditForm($comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() and $form->isValid())
        {
           
            $em->flush();
            $successMessage = $this->get('translator')->trans('The comment has been modified');
            $this->addFlash('mensaje', $successMessage);
           
            return $this->redirectToRoute('foro_index', array('id' => $comment->getId()));
        }
        
        return $this->render('userBundle:Comment:edit.html.twig', array('comment' => $comment, 'form' => $form->createView()));
    }
}
