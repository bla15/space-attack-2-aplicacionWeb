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
		$foro = $this->getDoctrine()->getRepository('userBundle:foro')->find($id);
		$user = $this->container->get('security.context')->getToken()->getUser();

		$comment = new Comment();
		$comment->setForo($foro);
		$form = $this->createCreateForm($comment);

		return $this->render('userBundle:Comment:add.html.twig', array('form' => $form->createView(), 'foro' => $foro, 'user' => $user));
	}
	private function createCreateForm(Comment $entity)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('comment_create'),
            'method' => 'POST'
        ));
            
        return $form;
    }
    public function createAction(Request $request)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        $comment = new Comment();
        $form = $this->createCreateForm($comment);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $comment->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $successMessage = $this->get('translator')->trans('The comment has been created.');
            $this->addFlash('mensaje', $successMessage); 
            return $this->redirectToRoute('foro_index');
                
        }
        return $this->render('userBundle:Comment:add.html.twig', array('form' => $form->createView()));

    }

}
